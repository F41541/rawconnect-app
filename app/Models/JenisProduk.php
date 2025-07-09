<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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