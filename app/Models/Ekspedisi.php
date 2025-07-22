<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 */
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
