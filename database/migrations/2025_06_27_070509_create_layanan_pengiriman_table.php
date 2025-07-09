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
        // PENJELASAN: Ini adalah "Buku Menu Utama" kita yang baru dan benar.
        Schema::create('layanan_pengiriman', function (Blueprint $table) {
            $table->id();

            // Tiga pilar utama dari hubungan kita
            $table->foreignId('toko_id')->constrained('tokos')->onDelete('restrict');
            $table->foreignId('merchant_id')->constrained('merchants')->onDelete('restrict');
            $table->foreignId('ekspedisi_id')->constrained('ekspedisis')->onDelete('restrict');

            // PENJELASAN: Aturan 'unique' ini sangat penting.
            // Ini mencegah Anda tidak sengaja memasukkan kombinasi yang sama persis
            // (Toko + Merchant + Ekspedisi) lebih dari satu kali.
            $table->unique(['toko_id', 'merchant_id', 'ekspedisi_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('layanan_pengiriman');
    }
};
