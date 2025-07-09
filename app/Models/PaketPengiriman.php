<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketPengiriman extends Model
{
    use HasFactory;

    // Menentukan nama tabel secara manual karena nama model kita tidak jamak
    protected $table = 'paket_pengiriman';

    // Daftar kolom yang boleh diisi secara massal
    protected $fillable = [
        'toko_id',
        'merchant_id',
        'ekspedisi_id',
        'status',
        'user_id',
    ];

    // --- DEFINISI RELASI ---

    /**
     * Relasi ke Toko: Satu paket ini milik satu Toko.
     */
    public function toko()
    {
        return $this->belongsTo(Toko::class);
    }

    /**
     * Relasi ke Merchant: Satu paket ini milik satu Merchant.
     */
    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }

    /**
     * Relasi ke Ekspedisi: Satu paket ini milik satu Ekspedisi.
     */
    public function ekspedisi()
    {
        return $this->belongsTo(Ekspedisi::class);
    }

    /**
     * Relasi ke User: Satu paket ini dibuat oleh satu User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke ItemPaket: Satu paket ini memiliki BANYAK item.
     */
    public function items()
    {
        return $this->hasMany(ItemPaket::class);
    }
}
