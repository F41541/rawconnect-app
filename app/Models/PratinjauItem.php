<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PratinjauItem extends Model
{
    use HasFactory;

    // Menentukan nama tabel secara manual karena nama model
    // 'PratinjauItem' tidak otomatis menjadi 'pratinjau_items' oleh Laravel.
    protected $table = 'pratinjau_items';

    // Daftar kolom yang boleh diisi secara massal
    protected $fillable = [
        'produk_id',
        'jumlah',
        'berat_per_item',
        'deskripsi_varian',
        'toko_id',
        'merchant_id',
        'ekspedisi_id',
        'user_id',
    ];

    // --- Definisi Relasi ---
    // Ini akan sangat membantu kita nanti saat menampilkan data di halaman pratinjau

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

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
