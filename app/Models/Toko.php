<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Produk;
use App\Models\LayananPengiriman;


class Toko extends Model
{
    use HasFactory;
    protected $fillable = ['name','logo'];
    
    public function produks()
    {
        return $this->hasMany(Produk::class);
    }

        public function layananPengiriman()
    {
        return $this->hasMany(LayananPengiriman::class);
    }
}
