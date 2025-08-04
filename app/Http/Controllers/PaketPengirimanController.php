<?php

namespace App\Http\Controllers;

use App\Models\Toko;
use App\Models\Kategori;
use App\Models\JenisProduk;
use App\Models\Produk;
use App\Models\LayananPengiriman;
use App\Models\PratinjauItem;
use App\Models\PaketPengiriman;
use App\Models\Ekspedisi;
use App\Models\Merchant;

use Illuminate\Http\Request;
use App\Services\PengirimanService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;



class PaketPengirimanController extends Controller
{
    /**
     * Menampilkan halaman utama pengiriman (daftar paket).
     */
    public function index()
    {
        // Relasi yang akan di-load untuk setiap paket
        $relations = ['toko', 'merchant', 'ekspedisi', 'user', 'items.produk.jenisProduk'];

        // 1. Mengambil DAFTAR PAKET untuk ditampilkan di konten setiap tab (dengan paginasi)
        $paket_proses = PaketPengiriman::where('status', 'proses')
                                       ->with($relations)
                                       ->latest()
                                       ->paginate(10, ['*'], 'prosesPage');

        $paket_selesai = PaketPengiriman::where('status', 'selesai')
                                      ->with($relations)
                                      ->latest()
                                      ->paginate(10, ['*'], 'selesaiPage');

        $paket_dibatalkan = PaketPengiriman::where('status', 'dibatalkan')
                                         ->with($relations)
                                         ->latest()
                                         ->paginate(10, ['*'], 'dibatalkanPage');

        // 2. Mengambil HITUNGAN spesifik untuk ditampilkan di BADGE tab
        $jumlah_proses_total = PaketPengiriman::where('status', 'proses')->count();
        $jumlah_selesai_hari_ini = PaketPengiriman::where('status', 'selesai')->whereDate('updated_at', today())->count();
        $jumlah_dibatalkan_hari_ini = PaketPengiriman::where('status', 'dibatalkan')->whereDate('updated_at', today())->count();

        // 3. Kirim semua data yang dibutuhkan ke view
        return view('pengiriman.index', [
            'title' => 'DAFTAR PENGIRIMAN',
            'paket_proses' => $paket_proses,
            'paket_selesai' => $paket_selesai,
            'paket_dibatalkan' => $paket_dibatalkan,
            'jumlah_proses' => $jumlah_proses_total,
            'jumlah_selesai' => $jumlah_selesai_hari_ini,
            'jumlah_dibatalkan' => $jumlah_dibatalkan_hari_ini,
        ]);
    }

    public function create()
    {
        $tokos = Toko::orderBy('name')->get();
        return view('pengiriman.create', [
            'title' => 'BUAT PENGIRIMAN BARU',
            'tokos' => $tokos,
        ]);
    }

        public function pratinjau()
    {
        $pratinjauItems = PratinjauItem::with(['toko', 'merchant', 'ekspedisi', 'produk.jenisProduk'])
        ->oldest()
        ->get();
            
        $validItems = $pratinjauItems->filter(fn($item) => $item->produk !== null);
        $sortedItems = $validItems->sortBy(fn($item) => $item->produk->nama);
        $groupedItems = $sortedItems->groupBy(fn($item) => $item->user_id . '-' . $item->toko_id . '-' . $item->merchant_id . '-' . $item->ekspedisi_id);

        return view('pengiriman.pratinjau', [
            'title' => 'PRATINJAU PENGIRIMAN',
            'groupedItems' => $groupedItems,
        ]);
    }

    public function tambahKePratinjau(Request $request, PengirimanService $service)
    {
        $validatedData = $request->validate([
            'toko_id'       => 'required|exists:tokos,id',
            'merchant_id'   => 'required|exists:merchants,id',
            'ekspedisi_id'  => 'required|exists:ekspedisis,id',
            'produk_id'     => 'required|exists:produks,id',
            'jumlah'        => 'required|integer|min:1',
            'berat_varian'  => 'nullable|numeric|min:0',
            'action_type'   => 'required|in:pratinjau,langsung',
        ]);
        return $service->tambahKePratinjau($validatedData, $request);
    }

    public function prosesPratinjau(Request $request, PengirimanService $service)
    {
        return $service->prosesPratinjau($request);
    }

    public function updateJumlahPratinjau(Request $request, PratinjauItem $pratinjauItem, PengirimanService $service)
    {
        return $service->updateJumlahPratinjau($request, $pratinjauItem);
    }

    public function hapusDariPratinjau(Request $request, PratinjauItem $pratinjauItem, PengirimanService $service)
    {
        return $service->hapusDariPratinjau($request, $pratinjauItem);
    }

    public function updateStatusPaket(Request $request, PaketPengiriman $paketPengiriman, PengirimanService $service)
    {
        return $service->updateStatusPaket($request, $paketPengiriman);
    }

    public function getJenisProdukByFilters(Request $request)
    {
        $request->validate(['toko_id' => 'required|exists:tokos,id']);
        $kategoriProdukJual = Kategori::firstOrCreate(['name' => 'Produk Jual']);
        $tokoId = $request->input('toko_id');
        $kategoriId = $kategoriProdukJual->id; 
        $jenisProduks = JenisProduk::query()
            ->whereHas('kategoris', fn($q) => $q->where('kategori_id', $kategoriId))
            ->whereHas('produks', fn($q) => $q->where('toko_id', $tokoId))
            ->orderBy('name')->get();
        return response()->json($jenisProduks);
    }

    public function getMerchantsByToko(Request $request)
    {
        $request->validate(['toko_id' => 'required|exists:tokos,id']);
        $merchantIds = LayananPengiriman::where('toko_id', $request->input('toko_id'))->pluck('merchant_id')->unique();
        $merchants = Merchant::whereIn('id', $merchantIds)->orderBy('name')->get();
        return response()->json($merchants);
    }

    public function getEkspedisisByToko(Request $request)
    {
        $request->validate(['toko_id' => 'required|exists:tokos,id', 'merchant_id' => 'required|exists:merchants,id']);
        $ekspedisiIds = LayananPengiriman::where('toko_id', $request->input('toko_id'))
            ->where('merchant_id', $request->input('merchant_id'))
            ->pluck('ekspedisi_id')->unique();
        $ekspedisis = Ekspedisi::whereIn('id', $ekspedisiIds)->orderBy('name')->get();
        return response()->json($ekspedisis);
    }

    public function searchProdukByFilters(Request $request)
    {
        $request->validate([
            'toko_id' => 'required|exists:tokos,id',
            'jenis_produk_id' => 'required|exists:jenis_produks,id',
            'q' => 'required|string',
        ]);
        $produks = Produk::query()
            ->where('toko_id', $request->input('toko_id'))
            ->where('jenis_produk_id', $request->input('jenis_produk_id'))
            ->where('nama', 'LIKE', "%{$request->input('q')}%")
            ->select('id', 'nama as text', 'satuan')->limit(10)->get();
        return response()->json($produks);
    }
}