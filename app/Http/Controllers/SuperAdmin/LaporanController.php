<?php

namespace App\Http\Controllers\SuperAdmin; 

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use App\Models\StockLog;

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
}