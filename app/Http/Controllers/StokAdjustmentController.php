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
            $keterangan = $validatedData['keterangan'] ?? null; 

            if ($validatedData['tipe'] === 'masuk') {
                $produk->recordStockChange($jumlah, 'penyesuaian', $keterangan);
                $produk->increment('stok', $jumlah);
                $actionText = 'ditambahkan';
            } else { 
                if ($produk->stok < $jumlah) {
                    DB::rollBack();
                    return redirect()->back()->withInput()->with('error', 'Gagal! Stok saat ini ('. $produk->stok .') lebih kecil dari jumlah yang ingin dikurangi ('. $jumlah .').');
                }
                $produk->recordStockChange(-$jumlah, 'penyesuaian', $keterangan);
                $produk->decrement('stok', $jumlah);
                $actionText = 'dikurangi';
            }
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }

        return redirect()->route('stok-adj.index')->with('success', 'Stok untuk "'. $produk->nama .'" berhasil '. $actionText .'!');
    }
}
