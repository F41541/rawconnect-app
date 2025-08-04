<?php
namespace App\Http\Controllers\Analisis;

use App\Http\Controllers\Controller;
use App\Models\JenisProduk;
use App\Models\Produk;
use Illuminate\Http\Request;

class LaporanStokRendahController extends Controller
{
    public function __invoke(Request $request)
    {
        $query = Produk::with(['toko', 'jenisProduk'])
            ->whereRaw('stok <= minimal_stok')
            ->where('stok', '>', 0);

        // Filter berdasarkan Jenis Produk
        if ($request->filled('jenis_produk_id')) {
            $query->where('jenis_produk_id', $request->jenis_produk_id);
        }

        // Sorting
        $sort = $request->input('sort', 'stok_asc');
        if ($sort === 'stok_asc') {
            $query->orderBy('stok', 'asc');
        } elseif ($sort === 'nama_asc') {
            $query->orderBy('nama', 'asc');
        }

        $produkStokRendah = $query->paginate(20)->withQueryString();
        $jenisProdukList = JenisProduk::orderBy('name')->get();

        return view('laporan.stok-rendah', [
            'title' => 'LAPORAN STOK RENDAH',
            'produks' => $produkStokRendah,
            'jenisProduks' => $jenisProdukList,
        ]);
    }
}