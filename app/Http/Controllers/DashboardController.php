<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\PratinjauItem;
use App\Models\PaketPengiriman;
use Illuminate\Http\Request;
use App\Models\ItemPaket;
use App\Models\StockLog;
use App\Models\JenisProduk;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = auth()->user();

        // Ambil data ringkasan
        $cardData = $this->getCardData($user);

        // Ambil data stok rendah + grafik (khusus super admin)
        $combinedData = $this->getWidgetAndChartData($request, $user);

        // Data dropdown jenis produk
        $combinedData['jenis_produk_list'] = JenisProduk::orderBy('name')->get();

        // Gabungkan semua data
        $data = array_merge($cardData, $combinedData);

        return view('dashboard', [
            'title' => 'DASHBOARD',
            'data'  => $data
        ]);
    }

    private function getCardData($user): array
    {
        $data = [
            'jumlah_proses'     => PaketPengiriman::where('status', 'proses')->count(),
            'jumlah_selesai'    => PaketPengiriman::where('status', 'selesai')->whereDate('updated_at', today())->count(),
            'jumlah_dibatalkan' => PaketPengiriman::where('status', 'dibatalkan')->whereDate('updated_at', today())->count(),
            'jumlah_pratinjau'  => 0,
        ];

        if ($user->can('create-shipments')) {
            $data['jumlah_pratinjau'] = PratinjauItem::where('user_id', $user->id)->count();
        }

        return $data;
    }

    private function getWidgetAndChartData(Request $request, $user): array
    {
        $data = [];

        $tanggalMulai  = $request->input('tanggal_mulai', now()->subDays(6)->toDateString());
        $tanggalSelesai = $request->input('tanggal_selesai', now()->toDateString());
        $jenisProdukIdFilter = $request->input('jenis_produk_stok');

        // --- Data Widget: Produk Stok Rendah (untuk semua role) ---
        $sort = $request->input('sort', 'stok_asc');
        $query = Produk::with(['toko', 'jenisProduk.kategoris'])
            ->whereRaw('stok <= minimal_stok')->where('stok', '>', 0);

        switch ($sort) {
            case 'jenis_produk':
                $query->join('jenis_produks', 'produks.jenis_produk_id', '=', 'jenis_produks.id')
                      ->orderBy('jenis_produks.name')->orderBy('produks.stok')->select('produks.*');
                break;
            case 'toko':
                $query->join('tokos', 'produks.toko_id', '=', 'tokos.id')
                      ->orderBy('tokos.name')->orderBy('produks.stok')->select('produks.*');
                break;
            default:
                $query->orderBy('stok');
                break;
        }

        $data['produk_stok_rendah'] = $query->get();
        $data['current_sort'] = $sort;

        // --- Sisanya hanya untuk Super Admin ---
        if (!$user->can('is-super-admin')) {
            return $data;
        }

        // Generate range tanggal
        $dateRange = collect(range(0, (strtotime($tanggalSelesai) - strtotime($tanggalMulai)) / 86400))
            ->map(fn($day) => \Carbon\Carbon::parse($tanggalMulai)->addDays($day));
        $data['chartLabels'] = $dateRange->map(fn($date) => $date->format('d M'))->toArray();

        // Grafik Penjualan (sales chart)
        $salesData = ItemPaket::whereHas('paketPengiriman', fn($q) =>
            $q->where('status', 'selesai')->whereBetween(DB::raw('DATE(created_at)'), [$tanggalMulai, $tanggalSelesai])
        )
            ->select(DB::raw('DATE(created_at) as tanggal'), DB::raw('SUM(jumlah) as total'))
            ->groupBy('tanggal')->orderBy('tanggal')->get()->keyBy('tanggal');
        $data['chartData'] = $dateRange->map(fn($date) =>
            $salesData->get($date->format('Y-m-d'))->total ?? 0
        )->toArray();

        // Grafik Stok Masuk vs Keluar
        $stokMasukQuery = StockLog::where('jumlah_berubah', '>', 0)
            ->whereBetween(DB::raw('DATE(created_at)'), [$tanggalMulai, $tanggalSelesai]);
        $stokKeluarQuery = StockLog::where('jumlah_berubah', '<', 0)
            ->whereBetween(DB::raw('DATE(created_at)'), [$tanggalMulai, $tanggalSelesai]);

        if ($jenisProdukIdFilter) {
            $stokMasukQuery->whereHas('produk', fn($q) =>
                $q->where('jenis_produk_id', $jenisProdukIdFilter)
            );
            $stokKeluarQuery->whereHas('produk', fn($q) =>
                $q->where('jenis_produk_id', $jenisProdukIdFilter)
            );
        }

        $stokMasukData = $stokMasukQuery->select(DB::raw('DATE(created_at) as tanggal'), DB::raw('SUM(jumlah_berubah) as total'))
            ->groupBy('tanggal')->get()->keyBy('tanggal');
        $stokKeluarData = $stokKeluarQuery->select(DB::raw('DATE(created_at) as tanggal'), DB::raw('SUM(jumlah_berubah) as total'))
            ->groupBy('tanggal')->get()->keyBy('tanggal');

        $data['stockChartLabels'] = $data['chartLabels'];
        $data['stockMasukData'] = $dateRange->map(fn($date) =>
            $stokMasukData->get($date->format('Y-m-d'))->total ?? 0
        )->toArray();
        $data['stockKeluarData'] = $dateRange->map(fn($date) =>
            abs($stokKeluarData->get($date->format('Y-m-d'))->total ?? 0)
        )->toArray();

        // Grafik Penjualan per Merchant
        $salesByMerchant = PaketPengiriman::where('status', 'selesai')
            ->whereBetween(DB::raw('DATE(paket_pengiriman.created_at)'), [$tanggalMulai, $tanggalSelesai])
            ->join('merchants', 'paket_pengiriman.merchant_id', '=', 'merchants.id')
            ->select('merchants.name as nama_merchant', DB::raw('COUNT(*) as total'))
            ->groupBy('merchants.name')->orderByDesc('total')->get();

        $data['merchantLabels'] = $salesByMerchant->pluck('nama_merchant');
        $data['merchantData'] = $salesByMerchant->pluck('total');

        // Grafik Produk Terlaris
        $produkTerlaris = ItemPaket::whereHas('paketPengiriman', fn($q) =>
            $q->where('status', 'selesai')->whereBetween(DB::raw('DATE(created_at)'), [$tanggalMulai, $tanggalSelesai])
        )
            ->join('produks', 'item_paket.produk_id', '=', 'produks.id')
            ->select('produks.nama', DB::raw('SUM(CASE WHEN item_paket.berat_per_item > 0 THEN item_paket.jumlah * item_paket.berat_per_item ELSE item_paket.jumlah END) as total_terjual'))
            ->groupBy('produks.nama')->orderByDesc('total_terjual')->limit(5)->get();

        $data['topProdukLabels'] = $produkTerlaris->pluck('nama');
        $data['topProdukData'] = $produkTerlaris->pluck('total_terjual');

        // Penjualan Ringkasan
        $data['penjualan_hari_ini'] = ItemPaket::whereHas('paketPengiriman', fn($q) =>
            $q->where('status', 'selesai')->whereDate('created_at', now()->toDateString())
        )->sum('jumlah');

        $data['penjualan_bulan_ini'] = ItemPaket::whereHas('paketPengiriman', fn($q) =>
            $q->where('status', 'selesai')->whereMonth('created_at', now()->month)
        )->sum('jumlah');

        return $data;
    }
}
