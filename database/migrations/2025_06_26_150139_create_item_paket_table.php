<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('item_paket', function (Blueprint $table) {
            $table->id();

            // PENJELASAN: Kunci utama yang menghubungkan item ini ke 'paket' induknya
            $table->foreignId('paket_pengiriman_id')->constrained('paket_pengiriman')->onDelete('cascade');
            
            // PENJELASAN: Produk mana yang ada di dalam paket ini
            $table->foreignId('produk_id')->constrained('produks')->onDelete('cascade');

            // PENJELASAN: Berapa jumlah produk ini di dalam paket
            $table->unsignedInteger('jumlah');
            $table->decimal('berat_per_item', 10, 2)->nullable();
            $table->string('deskripsi_varian')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_paket');
    }
};
