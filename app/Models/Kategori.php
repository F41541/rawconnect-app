<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Kategori extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function jenisProduks()
    {
        return $this->belongsToMany(JenisProduk::class, 'kategori_jenis_produk');
    }

    public function produks()
    {
        return $this->hasManyThrough(Produk::class, JenisProduk::class);
    }
}