<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\PratinjauItem;
use App\Models\PaketPengiriman;
use Illuminate\Http\Request;
use App\Models\ItemPaket;
use App\Models\StockLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;



class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai', now()->subDays(6)->toDateString());
        $tanggalSelesai = $request->input('tanggal_selesai', now()->toDateString());
        $user = auth()->user();
        $data = [];

        $data['jumlah_proses'] = PaketPengiriman::where('status', 'proses')->count();
        $data['jumlah_selesai'] = PaketPengiriman::where('status', 'selesai')->count();
        $data['jumlah_dibatalkan'] = PaketPengiriman::where('status', 'dibatalkan')->count();
        $data['jumlah_pratinjau'] = PratinjauItem::count();
        $sort = $request->input('sort', 'stok_asc'); 

        $query = Produk::with(['toko', 'jenisProduk.kategoris'])
            ->whereRaw('stok < minimal_stok')
            ->where('stok', '>', 0);

        switch ($sort) {
            case 'jenis_produk':
                $query->join('jenis_produks', 'produks.jenis_produk_id', '=', 'jenis_produks.id')
                    ->orderBy('jenis_produks.name', 'asc')
                    ->orderBy('produks.stok', 'asc')
                    ->select('produks.*');
                break;
            case 'toko':
                $query->join('tokos', 'produks.toko_id', '=', 'tokos.id')
                    ->orderBy('tokos.name', 'asc')
                    ->orderBy('produks.stok', 'asc')
                    ->select('produks.*');
                break;
            default: 
                $query->orderBy('stok', 'asc');
                break;
        }

        $data['produk_stok_rendah'] = $query->get();
        $data['current_sort'] = $sort; 

        if ($user->can('is-super-admin')) {
            $today = now()->toDateString();
            $data['penjualan_hari_ini'] = ItemPaket::whereHas('paketPengiriman', fn($q) => $q->where('status', 'selesai')->whereDate('created_at', $today))->sum('jumlah');
            $data['penjualan_bulan_ini'] = ItemPaket::whereHas('paketPengiriman', fn($q) => $q->where('status', 'selesai')->whereMonth('created_at', now()->month))->sum('jumlah');
        }

        $chartData = $this->getChartData();

        $data = array_merge($data, $chartData);

        return view('dashboard', [
            'title' => 'DASHBOARD',
            'data'  => $data
        ]);
    }

    /**
     * Method pembantu pribadi untuk mengambil dan memproses semua data untuk grafik.
     */
    private function getChartData(): array
    {
        $data = [];
        // Ambil tanggal dari request (via query string), default 7 hari terakhir
        $tanggalMulai = request('tanggal_mulai', now()->subDays(6)->toDateString());
        $tanggalSelesai = request('tanggal_selesai', now()->toDateString());

        // Buat range tanggal untuk label grafik
        $dateRange = collect(range(0, (strtotime($tanggalSelesai) - strtotime($tanggalMulai)) / 86400))
            ->map(fn($day) => \Carbon\Carbon::parse($tanggalMulai)->addDays($day));

        // --- Data untuk Grafik Penjualan 7 Hari (atau range yang dipilih) ---
        $salesData = ItemPaket::whereHas('paketPengiriman', function($q) use ($tanggalMulai, $tanggalSelesai) {
                $q->where('status', 'selesai')
                  ->whereBetween(DB::raw('DATE(created_at)'), [$tanggalMulai, $tanggalSelesai]);
            })
            ->select(DB::raw('DATE(created_at) as tanggal'), DB::raw('sum(jumlah) as total'))
            ->groupBy('tanggal')->orderBy('tanggal', 'asc')->get()->keyBy('tanggal');
            
        $data['chartLabels'] = $dateRange->map(fn($date) => $date->format('d M'))->toArray();
        $data['chartData'] = $dateRange->map(fn($date) => $salesData->get($date->format('Y-m-d'))->total ?? 0)->toArray();

        // --- Data untuk Grafik Stok Masuk vs Keluar ---
        $stokMasukData = StockLog::where('jumlah_berubah', '>', 0)
            ->whereBetween(DB::raw('DATE(created_at)'), [$tanggalMulai, $tanggalSelesai])
            ->select(DB::raw('DATE(created_at) as tanggal'), DB::raw('sum(jumlah_berubah) as total'))
            ->groupBy('tanggal')->get()->keyBy('tanggal');
        $stokKeluarData = StockLog::where('jumlah_berubah', '<', 0)
            ->whereBetween(DB::raw('DATE(created_at)'), [$tanggalMulai, $tanggalSelesai])
            ->select(DB::raw('DATE(created_at) as tanggal'), DB::raw('sum(jumlah_berubah) as total'))
            ->groupBy('tanggal')->get()->keyBy('tanggal');

        $data['stockChartLabels'] = $data['chartLabels'];
        $data['stockMasukData'] = $dateRange->map(fn($date) => $stokMasukData->get($date->format('Y-m-d'))->total ?? 0)->toArray();
        $data['stockKeluarData'] = $dateRange->map(fn($date) => abs($stokKeluarData->get($date->format('Y-m-d'))->total ?? 0))->toArray();

        // --- Data untuk Grafik Penjualan per Merchant ---
        $salesByMerchant = PaketPengiriman::where('status', 'selesai')
            ->whereBetween(DB::raw('DATE(paket_pengiriman.created_at)'), [$tanggalMulai, $tanggalSelesai])
            ->join('merchants', 'paket_pengiriman.merchant_id', '=', 'merchants.id')
            ->select('merchants.name as nama_merchant', DB::raw('count(*) as total'))
            ->groupBy('merchants.name')->orderBy('total', 'desc')->get();

        $data['merchantLabels'] = $salesByMerchant->pluck('nama_merchant');
        $data['merchantData'] = $salesByMerchant->pluck('total');

        return $data;
    }

}
