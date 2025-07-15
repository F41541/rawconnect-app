<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Produk;
use App\Models\Toko;
use App\Models\JenisProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StokAdjustmentController extends Controller
{
    /**
     * Menampilkan halaman utama untuk update stok.
     */
    public function index()
    {
        // PENJELASAN: Kita hanya perlu mengirim data untuk dropdown pertama saja.
        // Dropdown lain akan diisi secara dinamis oleh JavaScript.
        $tokos = Toko::orderBy('name')->get();

        return view('stok-adj.index', [
            'title'     => 'UPDATE STOK PRODUK',
            'tokos'     => $tokos,
        ]);
    }

    /**
     * API: Mengambil Kategori yang relevan berdasarkan Toko yang dipilih.
     */
    public function getKategoriByToko(Request $request)
    {
        $request->validate(['toko_id' => 'required|exists:tokos,id']);
        $tokoId = $request->input('toko_id');

        $kategoris = Kategori::whereHas('jenisProduks.produks', function ($query) use ($tokoId) {
            $query->where('toko_id', $tokoId);
        })->orderBy('name')->get();

        return response()->json($kategoris);
    }

    /**
     * API: Mengambil Jenis Produk berdasarkan Toko dan Kategori yang dipilih.
     */
    public function getJenisProdukByFilters(Request $request)
    {
        $request->validate([
            'toko_id' => 'required|exists:tokos,id',
            'kategori_id' => 'required|exists:kategoris,id'
        ]);
        $tokoId = $request->input('toko_id');
        $kategoriId = $request->input('kategori_id');

        $jenisProduks = JenisProduk::query()
            ->whereHas('kategoris', fn ($q) => $q->where('kategori_id', $kategoriId))
            ->whereHas('produks', fn ($q) => $q->where('toko_id', $tokoId))
            ->orderBy('name')
            ->get();

        return response()->json($jenisProduks);
    }

    /**
     * API: Mencari Produk berdasarkan semua filter untuk autocomplete.
     */
    public function searchProduk(Request $request)
    {
        $request->validate([
            'toko_id' => 'required|exists:tokos,id',
            'jenis_produk_id' => 'required|exists:jenis_produks,id',
            'q' => 'required|string',
        ]);
        $tokoId = $request->input('toko_id');
        $jenisProdukId = $request->input('jenis_produk_id');
        $searchTerm = $request->input('q');

        $produks = Produk::query()
            ->where('toko_id', $tokoId)
            ->where('jenis_produk_id', $jenisProdukId)
            ->where('nama', 'LIKE', "%{$searchTerm}%")
            ->select('id', 'nama as text')
            ->limit(10)
            ->get();

        return response()->json($produks);
    }

    /**
     * Memproses form dan menyimpan perubahan stok.
     */
    public function store(Request $request)
    {
        // 1. Validasi input (tambahkan 'keterangan')
        $validatedData = $request->validate([
            'produk_id'   => 'required|exists:produks,id',
            'jumlah'      => 'required|integer|min:1',
            'tipe'        => 'required|in:masuk,keluar',
            'keterangan'  => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $produk = Produk::lockForUpdate()->findOrFail($validatedData['produk_id']);
            $jumlah = (int)$validatedData['jumlah'];
            $keterangan = $validatedData['keterangan'];

            // 2. Lakukan aksi berdasarkan tombol yang diklik
            if ($validatedData['tipe'] === 'masuk') {
                // Catat log DULU, baru ubah stok
                $produk->recordStockChange($jumlah, 'penyesuaian', $keterangan);
                $produk->increment('stok', $jumlah);

            } else { // Jika 'keluar'
                if ($produk->stok < $jumlah) {
                    DB::rollBack();
                    return redirect()->back()->withInput()->with('error', 'Gagal! Stok saat ini ('. $produk->stok .') lebih kecil dari jumlah yang ingin dikurangi ('. $jumlah .').');
                }
                // Catat log DULU (dengan angka negatif), baru ubah stok
                $produk->recordStockChange(-$jumlah, 'penyesuaian', $keterangan);
                $produk->decrement('stok', $jumlah);
            }

            // Method increment/decrement sudah otomatis save, jadi $produk->save() tidak perlu lagi
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan. Stok gagal diupdate.');
        }

        return redirect()->route('stok-adj.index')->with('success', 'Stok berhasil diupdate!')->withInput();
    }
}
