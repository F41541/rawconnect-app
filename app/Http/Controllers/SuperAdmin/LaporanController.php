<?php

namespace App\Http\Controllers\SuperAdmin; // <-- Pastikan namespace-nya benar

use App\Http\Controllers\Controller; // <-- Tambahkan ini
use Illuminate\Http\Request;
use App\Models\StockLog;

class LaporanController extends Controller
{
    public function penjualan(Request $request)
    {
        // Validasi input filter tanggal (opsional)
        $request->validate([
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        // Ambil data log stok dengan tipe 'penjualan'
        $query = StockLog::where('tipe', 'penjualan')
                         ->with(['produk', 'user']) // Ambil juga data relasinya
                         ->latest(); // Urutkan dari yang terbaru

        // Terapkan filter tanggal jika ada
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', '>=', $request->input('tanggal_mulai'));
        }
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('created_at', '<=', $request->input('tanggal_selesai'));
        }

        $laporanPenjualan = $query->paginate(20); // Paginasi agar tidak berat

        return view('superadmin.laporan.penjualan', [
            'title' => 'Laporan Penjualan',
            'laporan' => $laporanPenjualan,
        ]);
    }
}