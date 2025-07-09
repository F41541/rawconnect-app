<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPaket extends Model
{
    use HasFactory;

    // Menentukan nama tabel secara manual
    protected $table = 'item_paket';

    // Daftar kolom yang boleh diisi
    protected $fillable = [
        'paket_pengiriman_id',
        'produk_id',
        'jumlah',
        'berat_per_item',
        'deskripsi_varian'
    ];

    // --- DEFINISI RELASI ---

    /**
     * Relasi ke PaketPengiriman: Item ini milik satu Paket.
     */
    public function paketPengiriman()
    {
        return $this->belongsTo(PaketPengiriman::class);
    }

    /**
     * Relasi ke Produk: Item ini merujuk pada satu Produk.
     */
    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
