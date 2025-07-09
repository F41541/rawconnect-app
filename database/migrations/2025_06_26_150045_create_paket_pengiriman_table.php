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
        Schema::create('paket_pengiriman', function (Blueprint $table) {
            $table->id();

            // PENJELASAN: Menghubungkan paket ini ke data master yang relevan
            $table->foreignId('toko_id')->constrained('tokos')->onDelete('restrict');
            $table->foreignId('merchant_id')->constrained('merchants')->onDelete('restrict');
            $table->foreignId('ekspedisi_id')->constrained('ekspedisis')->onDelete('restrict');

            // PENJELASAN: Status dari paket ini secara keseluruhan
            $table->enum('status', ['proses', 'selesai', 'dibatalkan'])->default('proses');

            // PENJELASAN: (Opsional tapi bagus) Mencatat siapa yang membuat paket ini
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paket_pengiriman');
    }
};
