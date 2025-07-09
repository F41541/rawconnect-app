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

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Memeriksa apakah user sudah login atau belum.
Route::get('/', function () {
    // Jika sudah login, langsung lempar ke dashboard.
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    // Jika belum, arahkan ke halaman login.
    return redirect()->route('login');
});

// --- SEMUA RUTE YANG MEMERLUKAN LOGIN DITARUH DI DALAM GRUP INI ---
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [PaketPengirimanController::class, 'dashboard'])->name('dashboard');

    Route::middleware('can:is-super-admin')->group(function() {
        Route::resource('user', UserController::class);
    });

    // --- PENGIRIMAN ---
    Route::get('/pengiriman', [PaketPengirimanController::class, 'index'])->name('pengiriman.index');
    Route::get('/pengiriman/create', [PaketPengirimanController::class, 'create'])->name('pengiriman.create');
    Route::post('/pengiriman/tambah', [PaketPengirimanController::class, 'tambahKePratinjau'])->name('pengiriman.tambah');
    Route::get('/pengiriman/pratinjau', [PaketPengirimanController::class, 'pratinjau'])->name('pengiriman.pratinjau');
    Route::delete('/pengiriman/hapus/{pratinjauItem}', [PaketPengirimanController::class, 'hapusDariPratinjau'])->name('pengiriman.hapus');
    Route::post('/pengiriman/proses', [PaketPengirimanController::class, 'prosesPratinjau'])->name('pengiriman.proses');
    Route::patch('/pengiriman/paket/{paketPengiriman}/update-status', [PaketPengirimanController::class, 'updateStatusPaket'])->name('paket.updateStatus');
    Route::patch('/pengiriman/pratinjau/update-jumlah/{pratinjauItem}', [PaketPengirimanController::class, 'updateJumlahPratinjau'])->name('pratinjau.updateJumlah');

    // HALAMAN STOK DINAMIS
    Route::get('/stok/jenis/{jenisProduk}', [ProdukController::class, 'showByJenis'])->name('stok.show_by_jenis');
    Route::patch('/stok/koreksi/{produk}', [ProdukController::class, 'koreksiStok'])->name('stok.koreksi');
    
    // FITUR UPDATE STOK CEPAT
    Route::get('/stok-adjustment', [StokAdjustmentController::class, 'index'])->name('stok-adj.index');
    Route::post('/stok-adjustment', [StokAdjustmentController::class, 'store'])->name('stok-adj.store');

    // MANAJEMEN DATA MASTER (RESOURCE CONTROLLERS)
    Route::resource('toko', TokoController::class);
    Route::resource('jenis-produk', JenisProdukController::class);
    Route::resource('ekspedisi', EkspedisiController::class);
    Route::resource('merchant', MerchantController::class);
    Route::resource('kategori', KategoriController::class);
    Route::resource('produk', ProdukController::class)->except(['show']);
    Route::resource('layanan-pengiriman', LayananPengirimanController::class)->except(['show', 'edit', 'update']);
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
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

// Rute Bawaan Breeze untuk proses login, logout, register, dll.
require __DIR__.'/auth.php';