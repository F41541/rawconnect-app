<?php

namespace App\Http\Controllers;

use App\Models\LayananPengiriman;
use App\Models\Toko;
use App\Models\Merchant;
use App\Models\Ekspedisi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LayananPengirimanController extends Controller
{
    /**
     * Menampilkan daftar semua layanan yang telah dikonfigurasi,
     * dikelompokkan berdasarkan Toko dan Merchant.
     */
    public function index()
    {
        // 1. Ambil semua data, urutkan agar siap dikelompokkan
        $layananPengirimans = LayananPengiriman::with(['toko', 'merchant', 'ekspedisi'])
            ->orderBy('toko_id')
            ->orderBy('merchant_id')
            ->get();

        // 2. Kelompokkan data dalam 2 level: Pertama berdasarkan nama toko, 
        //    kemudian di dalamnya dikelompokkan lagi berdasarkan nama merchant.
        $groupedLayanan = $layananPengirimans->groupBy('toko.name')->map(function ($byToko) {
            return $byToko->groupBy('merchant.name');
        });

        // 3. Kirim data yang sudah dikelompokkan ke view
        return view('layanan-pengiriman.index', [
            'title' => 'LAYANAN PENGIRIMAN',
            'groupedLayanan' => $groupedLayanan
        ]);
    }
    /**
     * Menampilkan form untuk menambah konfigurasi layanan baru.
     */
    public function create()
    {
        return view('layanan-pengiriman.create', [
            'title' => 'Tambah Layanan Pengiriman',
            'tokos' => Toko::orderBy('name')->get(),
            'merchants' => Merchant::orderBy('name')->get(),
            'ekspedisis' => Ekspedisi::orderBy('name')->get(),
        ]);
    }

    /**
     * Menyimpan beberapa konfigurasi layanan baru ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi diubah untuk menerima array dari checkbox
        $validatedData = $request->validate([
            'toko_id'       => 'required|exists:tokos,id',
            'merchant_id'   => 'required|exists:merchants,id',
            'ekspedisi_ids'  => 'required|array', // Harus berupa array
            'ekspedisi_ids.*' => 'exists:ekspedisis,id', // Setiap item di array harus ada di tabel ekspedisis
        ], [
            'ekspedisi_ids.required' => 'Anda harus memilih minimal satu ekspedisi.'
        ]);

        $tokoId = $validatedData['toko_id'];
        $merchantId = $validatedData['merchant_id'];
        $createdCount = 0;

        // 2. Loop melalui setiap ekspedisi yang dipilih
        foreach ($validatedData['ekspedisi_ids'] as $ekspedisiId) {
            // 3. Gunakan firstOrCreate untuk keamanan data
            // Ini akan membuat data HANYA JIKA kombinasi ini belum ada.
            // Mencegah duplikat data secara otomatis.
            $layanan = LayananPengiriman::firstOrCreate([
                'toko_id' => $tokoId,
                'merchant_id' => $merchantId,
                'ekspedisi_id' => $ekspedisiId
            ]);

            // Jika record baru benar-benar dibuat (bukan hanya ditemukan)
            if ($layanan->wasRecentlyCreated) {
                $createdCount++;
            }
        }

        return redirect()->back()->with('success', $createdCount . ' layanan baru berhasil ditambahkan!');
    }
    
    /**
     * Menghapus konfigurasi layanan dari database.
     */
    public function destroy(LayananPengiriman $layananPengiriman)
    {
        $layananPengiriman->delete();

        return redirect()->back()->with('success', 'Layanan berhasil dihapus.');
    }
}
