<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PaketPengirimanController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\TokoController;
use App\Http\Controllers\JenisProdukController;
use App\Http\Controllers\EkspedisiController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\StokAdjustmentController;
use App\Http\Controllers\LayananPengirimanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SuperAdmin\LaporanController;
use App\Http\Controllers\SuperAdmin\MasterController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Analisis\LaporanStokRendahController;


// Rute Autentikasi dari Breeze
require __DIR__.'/auth.php';

// Rute Halaman Utama
Route::get('/', function () {
    if (Auth::check()) { return redirect()->route('dashboard'); }
    return redirect()->route('login');
});

// === RUTE YANG MEMERLUKAN LOGIN ===
Route::middleware(['auth', 'verified'])->group(function () {

    // --- GRUP 1: Rute untuk SEMUA PERAN (Pegawai, Admin, Super Admin) ---
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Melihat daftar pengiriman dan stok bisa dilakukan semua peran
    Route::get('/pengiriman', [PaketPengirimanController::class, 'index'])->name('pengiriman.index');
    Route::get('/stok/jenis/{jenisProduk}', [ProdukController::class, 'showByJenis'])->name('stok.show_by_jenis');
    // Mengubah status juga bisa dilakukan semua peran (namun opsi 'dibatalkan' akan di-handle di view & controller)
    Route::patch('/pengiriman/paket/{paketPengiriman}/update-status', [PaketPengirimanController::class, 'updateStatusPaket'])->name('paket.updateStatus');
    Route::get('/log/stok', [LogController::class, 'stokLog'])->name('log.stok');
    Route::get('/laporan/stok-rendah', LaporanStokRendahController::class)->name('laporan.stok-rendah');
    Route::get('/laporan/produk-terlaris', [LaporanController::class, 'produkTerlaris'])->name('laporan.produk-terlaris');

    // --- GRUP 2: Rute untuk PEGAWAI & ADMIN (sesuai Gate 'adjust-stock') ---
    Route::middleware('can:adjust-stock')->group(function() {
        Route::get('/stok-adjustment', [StokAdjustmentController::class, 'index'])->name('stok-adj.index');
        Route::patch('/stok/koreksi/{produk}', [ProdukController::class, 'koreksiStok'])->name('stok.koreksi');
        Route::post('/stok-adjustment', [StokAdjustmentController::class, 'store'])->name('stok-adj.store');
    });

    // --- GRUP 3: Rute untuk ADMIN & SUPER ADMIN (sesuai Gate 'manage-shipments') ---
    Route::middleware('can:manage-shipments')->group(function() {
        Route::get('/pengiriman/create', [PaketPengirimanController::class, 'create'])->name('pengiriman.create');
        Route::post('/pengiriman/tambah', [PaketPengirimanController::class, 'tambahKePratinjau'])->name('pengiriman.tambah');
        Route::get('/pengiriman/pratinjau', [PaketPengirimanController::class, 'pratinjau'])->name('pengiriman.pratinjau');
        Route::delete('/pengiriman/hapus/{pratinjauItem}', [PaketPengirimanController::class, 'hapusDariPratinjau'])->name('pengiriman.hapus');
        Route::post('/pengiriman/proses', [PaketPengirimanController::class, 'prosesPratinjau'])->name('pengiriman.proses');
        Route::patch('/pengiriman/pratinjau/update-jumlah/{pratinjauItem}', [PaketPengirimanController::class, 'updateJumlahPratinjau'])->name('pratinjau.updateJumlah');
    });

    // --- GRUP 4: RUTE HANYA UNTUK SUPER ADMIN ---
    Route::middleware('can:is-super-admin')->prefix('superadmin')->name('superadmin.')->group(function() {
        Route::get('/master', [MasterController::class, 'index'])->name('master.index');
        Route::get('/laporan', [LaporanController::class, 'penjualan'])->name('laporan.penjualan');
        Route::resource('user', UserController::class);
        Route::resource('toko', TokoController::class);
        Route::resource('jenis-produk', JenisProdukController::class);
        Route::resource('ekspedisi', EkspedisiController::class);
        Route::resource('merchant', MerchantController::class);
        Route::resource('kategori', KategoriController::class);
        Route::resource('produk', ProdukController::class)->except(['show']);
        Route::resource('layanan-pengiriman', LayananPengirimanController::class)->except(['show', 'edit', 'update']);
    });
});


Route::prefix('api')->name('api.')->group(function () {
    
    // --- API untuk Form Pengiriman ---
    Route::prefix('pengiriman')->name('pengiriman.')->group(function () {
        Route::get('/get-jenis-produk-by-filters', [PaketPengirimanController::class, 'getJenisProdukByFilters'])->name('get_jenis_produk_by_filters');
        Route::get('/get-merchants', [PaketPengirimanController::class, 'getMerchantsByToko'])->name('get_merchants');
        Route::get('/get-ekspedisis', [PaketPengirimanController::class, 'getEkspedisisByToko'])->name('get_ekspedisis');
        Route::get('/search-produk', [PaketPengirimanController::class, 'searchProdukByFilters'])->name('search_produk');
    });

    // --- API untuk Stok Adjustment ---
    Route::prefix('stok-adj')->name('stok-adj.')->group(function () {
        Route::get('/get-kategori-by-toko', [StokAdjustmentController::class, 'getKategoriByToko'])->name('get_kategori_by_toko');
        Route::get('/get-jenis-produk-by-filters', [StokAdjustmentController::class, 'getJenisProdukByFilters'])->name('get_jenis_produk_by_filters');
        Route::get('/search-produk', [StokAdjustmentController::class, 'searchProduk'])->name('search_produk');
    });

});