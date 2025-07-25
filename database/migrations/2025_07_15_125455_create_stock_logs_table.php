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
        Schema::create('stock_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produks')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->integer('jumlah_berubah');
            $table->integer('stok_sebelum');
            $table->integer('stok_sesudah');
            $table->string('tipe'); 
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_logs');
    }
};
