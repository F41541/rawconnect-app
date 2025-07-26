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
        Schema::create('layanan_pengiriman', function (Blueprint $table) {
            $table->id();

            $table->foreignId('toko_id')->constrained('tokos')->onDelete('restrict');
            $table->foreignId('merchant_id')->constrained('merchants')->onDelete('restrict');
            $table->foreignId('ekspedisi_id')->constrained('ekspedisis')->onDelete('restrict');
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
