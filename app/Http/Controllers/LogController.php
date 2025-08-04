<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockLog;

class LogController extends Controller
{
    public function stokLog(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
            'jenis_produk_id' => 'nullable|exists:jenis_produks,id', // Validasi filter baru
        ]);

        $query = StockLog::with(['produk', 'user'])->latest();

        // Terapkan filter tanggal jika ada
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_selesai);
        }
        
        // Terapkan filter Jenis Produk jika ada
        if ($request->filled('jenis_produk_id')) {
            $query->whereHas('produk', function($q) use ($request) {
                $q->where('jenis_produk_id', $request->jenis_produk_id);
            });
        }

        $logs = $query->paginate(25)->withQueryString();
        $jenisProdukList = \App\Models\JenisProduk::orderBy('name')->get();

        return view('log.stok', [
            'title' => 'RIWAYAT PERUBAHAN STOK',
            'logs' => $logs,
            'jenisProduks' => $jenisProdukList, // Kirim daftar jenis produk ke view
        ]);
    }
}