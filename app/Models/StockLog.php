<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'produk_id', 'user_id', 'jumlah_berubah', 
        'stok_sebelum', 'stok_sesudah', 'tipe', 'keterangan'
    ];

    public function produk() { return $this->belongsTo(Produk::class); }
    public function user() { return $this->belongsTo(User::class); }
}