<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $status
 * @property int $toko_id
 * @property int $merchant_id
 * @property int $ekspedisi_id
 * @property int $kategoris
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $user
 * @property-read \App\Models\Toko|null $toko
 * @property-read \App\Models\Merchant|null $merchant
 * @property-read \App\Models\Ekspedisi|null $ekspedisi
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ItemPaket[] $items
 */

class PaketPengiriman extends Model
{
    use HasFactory;

    protected $table = 'paket_pengiriman';

    protected $fillable = [
        'toko_id',
        'merchant_id',
        'ekspedisi_id',
        'status',
        'user_id',
    ];


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
