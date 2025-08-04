<?php

namespace App\Http\Controllers\SuperAdmin; 

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use App\Models\StockLog;
use Illuminate\Support\Facades\DB;
use App\Models\ItemPaket;
use App\Models\JenisProduk;
use Illuminate\Support\Facades\Auth;



class LaporanController extends Controller
{
    public function penjualan(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        $query = StockLog::where('tipe', 'penjualan')
                         ->with(['produk', 'user'])
                         ->latest();

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', '>=', $request->input('tanggal_mulai'));
        }
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('created_at', '<=', $request->input('tanggal_selesai'));
        }

        $laporanPenjualan = $query->paginate(20); 

        return view('superadmin.laporan.penjualan', [
            'title' => 'LAPORAN PENJUALAN',
            'laporan' => $laporanPenjualan,
        ]);
    }

    public function produkTerlaris(Request $request)
    {
        // 1. Validasi untuk kedua filter
        $request->validate([
            'periode' => 'nullable|in:1d,7d,1m,1y',
            'jenis_produk_id' => 'nullable|exists:jenis_produks,id',
        ]);
        
        $periode = $request->input('periode', '7d');
        $jenisProdukId = $request->input('jenis_produk_id');
        $tanggalMulai = now();

        // 2. Logika untuk menentukan rentang tanggal berdasarkan periode
        switch ($periode) {
            case '1d': $tanggalMulai = now()->startOfDay(); break;
            case '1m': $tanggalMulai = now()->subMonth(); break;
            case '1y': $tanggalMulai = now()->subYear(); break;
            case '7d':
            default: $tanggalMulai = now()->subDays(6)->startOfDay(); break;
        }

        // 3. Query utama untuk mengambil data produk terlaris
        $query = ItemPaket::whereHas('paketPengiriman', function($q) use ($tanggalMulai) {
                $q->where('status', 'selesai')->where('created_at', '>=', $tanggalMulai);
            })
            ->join('produks', 'item_paket.produk_id', '=', 'produks.id')
            ->select(
                'produks.nama', 
                'produks.satuan', 
                DB::raw('SUM(CASE WHEN item_paket.berat_per_item > 0 THEN item_paket.jumlah * item_paket.berat_per_item ELSE item_paket.jumlah END) as total_terjual')
            )
            ->groupBy('produks.nama', 'produks.satuan')
            ->orderBy('total_terjual', 'desc');
            
        // 4. Terapkan filter Jenis Produk jika dipilih
        if ($jenisProdukId) {
            $query->where('produks.jenis_produk_id', $jenisProdukId);
        }

        $produkTerlaris = $query->paginate(20)->withQueryString();
        
        // 5. Ambil daftar semua Jenis Produk untuk ditampilkan di dropdown filter
        $jenisProdukList = JenisProduk::orderBy('name')->get();

        return view('laporan.produk-terlaris', [
            'title' => 'LAPORAN PRODUK TERLARIS',
            'produks' => $produkTerlaris,
            'jenisProduks' => $jenisProdukList,
            'periode_aktif' => $periode,
        ]);
    }
}