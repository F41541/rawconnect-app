<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ekspedisi extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    // PENJELASAN: Relasi baru ini berarti satu Ekspedisi bisa menjadi bagian dari banyak 'LayananPengiriman'.
    public function layananPengiriman()
    {
        return $this->hasMany(LayananPengiriman::class);
    }
    
}
