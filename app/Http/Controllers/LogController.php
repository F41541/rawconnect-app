<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockLog;

class LogController extends Controller
{
    public function stokLog()
    {
        // Ambil semua data log, urutkan dari yang terbaru, dan paginasi
        $logs = StockLog::with(['produk', 'user'])
                        ->latest()
                        ->paginate(25); // Tampilkan 25 data per halaman

        return view('log.stok', [
            'title' => 'Riwayat Perubahan Stok',
            'logs' => $logs,
        ]);
    }
}