<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LayananPengiriman extends Model
{
    use HasFactory;
    protected $table = 'layanan_pengiriman';
    protected $fillable = ['toko_id', 'merchant_id', 'ekspedisi_id'];

    // PENJELASAN: Mendefinisikan bahwa setiap baris di tabel ini 'milik' satu Toko,
    // satu Merchant, dan satu Ekspedisi.

    public function toko()
    {
        return $this->belongsTo(Toko::class);
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    public function ekspedisi()
    {
        return $this->belongsTo(Ekspedisi::class);
    }
}