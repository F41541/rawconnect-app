<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Toko;
use App\Models\JenisProduk;

class Produk extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'nama',
        'foto',
        'stok',
        'satuan', // <-- PENJELASAN: Ini adalah perbaikan penting yang kita tambahkan
        'jenis_produk_id',
        'toko_id',
    ];

    public function toko()
    {
        return $this->belongsTo(Toko::class);
    }

    public function jenisProduk()
    {
        return $this->belongsTo(JenisProduk::class);
    }
}