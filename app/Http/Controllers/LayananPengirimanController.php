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
        $layananPengirimans = LayananPengiriman::with(['toko', 'merchant', 'ekspedisi'])
            ->orderBy('toko_id')
            ->orderBy('merchant_id')
            ->get();

        $groupedLayanan = $layananPengirimans->groupBy('toko.name')->map(function ($byToko) {
            return $byToko->groupBy('merchant.name');
        });

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
            'title' => 'TAMBAH KONFIGURASI LAYANAN BARU',
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
        $validatedData = $request->validate([
            'toko_id'       => 'required|exists:tokos,id',
            'merchant_id'   => 'required|exists:merchants,id',
            'ekspedisi_ids'  => 'required|array', 
            'ekspedisi_ids.*' => 'exists:ekspedisis,id', 
        ], [
            'ekspedisi_ids.required' => 'Anda harus memilih minimal satu ekspedisi.'
        ]);

        $tokoId = $validatedData['toko_id'];
        $merchantId = $validatedData['merchant_id'];
        $createdCount = 0;

        foreach ($validatedData['ekspedisi_ids'] as $ekspedisiId) {
            $layanan = LayananPengiriman::firstOrCreate([
                'toko_id' => $tokoId,
                'merchant_id' => $merchantId,
                'ekspedisi_id' => $ekspedisiId
            ]);

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
