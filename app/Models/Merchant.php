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
class Merchant extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function layananPengiriman()
    {
        return $this->hasMany(LayananPengiriman::class);
    }
}