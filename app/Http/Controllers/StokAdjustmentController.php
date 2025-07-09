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
        // 1. Validasi semua input dari form
        $validatedData = $request->validate([
            'produk_id'   => 'required|exists:produks,id',
            'jumlah'      => 'required|integer|min:1', // Jumlah harus angka positif
            'action_type' => 'required|in:tambah,kurangi', // Aksi harus 'tambah' atau 'kurangi'
        ]);

        // 2. Gunakan Database Transaction untuk keamanan data
        // Ini memastikan jika ada error, semua perubahan akan dibatalkan.
        DB::beginTransaction();

        try {
            // Cari produk yang akan diupdate, dan kunci barisnya untuk mencegah race condition
            $produk = Produk::lockForUpdate()->findOrFail($validatedData['produk_id']);
            $jumlah = $validatedData['jumlah'];

            // 3. Lakukan aksi berdasarkan tombol yang diklik
            if ($validatedData['action_type'] === 'tambah') {
                // Jika tombol 'Tambah' diklik, tambahkan stok
                $produk->stok += $jumlah;
            } else {
                // Jika tombol 'Kurangi' diklik, kurangi stok
                // Tambahkan pengaman agar stok tidak menjadi minus
                if ($produk->stok < $jumlah) {
                    // Jika stok tidak cukup, batalkan transaksi dan kirim error
                    DB::rollBack();
                    return redirect()->back()->withInput()->with('error', 'Gagal mengurangi! Stok saat ini ('. $produk->stok .') lebih kecil dari jumlah yang ingin dikurangi ('. $jumlah .').');
                }
                $produk->stok -= $jumlah;
            }

            // 4. Simpan perubahan stok ke database
            $produk->save();

            // Jika semua berhasil, konfirmasi transaksi
            DB::commit();

        } catch (\Exception $e) {
            // Jika ada error lain yang tak terduga, batalkan transaksi
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan. Stok gagal diupdate.');
        }

        // 5. Redirect kembali dengan pesan sukses
        return redirect()->route('stok-adj.index')->with('success', 'Stok untuk produk "'. $produk->nama .'" berhasil diupdate!')->withInput();
    }
}
