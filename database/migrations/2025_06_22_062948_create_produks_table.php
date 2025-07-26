<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Diubah ke gaya anonim untuk konsistensi
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('produks', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('foto')->nullable();
            $table->integer('stok')->default(0);
            $table->integer('minimal_stok')->default(10);
            $table->enum('satuan', ['pouch', 'gram', 'kg', 'pcs', 'roll', 'pack'])->default('pcs');
            $table->foreignId('jenis_produk_id')->constrained('jenis_produks')->onDelete('restrict');
            $table->foreignId('toko_id')->constrained('tokos')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};