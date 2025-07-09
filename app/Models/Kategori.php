<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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