<?php
namespace App\Traits;
use App\Models\StockLog;

trait HasStockLog {
    public function recordStockChange(int $jumlahBerubah, string $tipe, string $keterangan = null)
    {
        $stokSebelum = $this->stok;
        $stokSesudah = $stokSebelum + $jumlahBerubah;

        StockLog::create([
            'produk_id'       => $this->id,
            'user_id'         => auth()->id(),
            'jumlah_berubah'  => $jumlahBerubah,
            'stok_sebelum'    => $stokSebelum,
            'stok_sesudah'    => $stokSesudah,
            'tipe'            => $tipe,
            'keterangan'      => $keterangan,
        ]);
    }
}