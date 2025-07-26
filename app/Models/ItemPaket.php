<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $paket_pengiriman_id
 * @property int $produk_id
 * @property int $jumlah
 * @property string|null $deskripsi_varian
 * @property float|null $berat_per_item
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Produk|null $produk
 */

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
