<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property int $minimal_stok
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Kategori[] $kategoris
 */
class JenisProduk extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function kategoris()
    {
        return $this->belongsToMany(Kategori::class, 'kategori_jenis_produk');
    }

    public function produks()
    {
        return $this->hasMany(Produk::class);
    }
}