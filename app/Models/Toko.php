<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Produk;
use App\Models\LayananPengiriman;

/**
 * @property int $id
 * @property string $name
 * @property string|null $logo
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */

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
