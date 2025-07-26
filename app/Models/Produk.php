<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Toko;
use App\Models\JenisProduk;
use App\Traits\HasStockLog; 

/**
 * @property int $id
 * @property string $nama
 * @property string|null $foto
 * @property int $stok
 * @property int $minimal_stok
 * @property string $satuan
 * @property int $jenis_produk_id
 * @property int $toko_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\JenisProduk|null $jenisProduk
 * @property-read \App\Models\Toko|null $toko
 */
class Produk extends Model
{
    use HasFactory, HasStockLog;
    
    protected $fillable = [
        'nama',
        'foto',
        'stok',
        'minimal_stok',
        'satuan',
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