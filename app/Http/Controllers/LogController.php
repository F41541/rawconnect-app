<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockLog;

class LogController extends Controller
{
    public function stokLog(Request $request)
    {
        $query = StockLog::with(['produk', 'user'])->latest();

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_selesai);
        }

        $logs = $query->paginate(25)->withQueryString();
        
        return view('log.stok', [
            'title' => 'Riwayat Perubahan Stok',
            'logs' => $logs,
        ]);
    }
}