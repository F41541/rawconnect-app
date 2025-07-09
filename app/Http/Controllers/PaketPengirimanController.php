<?php

namespace App\Http\Controllers;

// Mengimpor semua model yang kita butuhkan
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaketPengirimanController extends Controller
{
    /**
     * Menampilkan halaman utama pengiriman (daftar paket).
     */
    public function index()
    {
        $semuaPaket = PaketPengiriman::with(['toko', 'merchant', 'ekspedisi', 'user', 'items.produk.jenisProduk'])->latest()->get();
        $paketByStatus = $semuaPaket->groupBy('status');
        return view('pengiriman.index', [
            'title' => 'Daftar Pengiriman',
            'paket_proses' => $paketByStatus->get('proses', collect()),
            'paket_selesai' => $paketByStatus->get('selesai', collect()),
            'paket_dibatalkan' => $paketByStatus->get('dibatalkan', collect()),
        ]);
    }

    public function dashboard()
    {
        return view('dashboard', [
            'jumlah_perlu'      => PaketPengiriman::where('status', 'proses')->count(),
            'jumlah_selesai'    => PaketPengiriman::where('status', 'selesai')->count(),
            'jumlah_dibatalkan' => PaketPengiriman::where('status', 'dibatalkan')->count(),
            'jumlah_keranjang'  => PratinjauItem::count(),
            'title'             => 'DASHBOARD',
        ]);
    }

    /**
     * Menampilkan form untuk menambah item ke pratinjau.
     */
    public function create()
    {
        $tokos = Toko::orderBy('name')->get();
        return view('pengiriman.create', [
            'title' => 'Buat Pengiriman',
            'tokos' => $tokos,
        ]);
    }

    // --- API Endpoints untuk Form Pengiriman Pintar ---

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
        $merchantIds = LayananPengiriman::where('toko_id', $request->toko_id)->pluck('merchant_id')->unique();
        $merchants = Merchant::whereIn('id', $merchantIds)->orderBy('name')->get();
        return response()->json($merchants);
    }

    public function getEkspedisisByToko(Request $request)
    {
        $request->validate(['toko_id' => 'required|exists:tokos,id', 'merchant_id' => 'required|exists:merchants,id']);
        $ekspedisiIds = LayananPengiriman::where('toko_id', $request->toko_id)
            ->where('merchant_id', $request->merchant_id)
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
            ->where('toko_id', $request->toko_id)
            ->where('jenis_produk_id', $request->jenis_produk_id)
            ->where('nama', 'LIKE', "%{$request->q}%")
            ->select('id', 'nama as text', 'satuan')->limit(10)->get();
        return response()->json($produks);
    }
    
    // --- Method untuk Pratinjau & Proses ---

    ## ================== VERSI FINAL DENGAN SEMUA PERBAIKAN ==================
    public function tambahKePratinjau(Request $request)
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

        $produk = Produk::find($validatedData['produk_id']);
        
        // FIX: Ambil berat varian dengan aman menggunakan null coalescing operator (??)
        $beratVarian = $validatedData['berat_varian'] ?? null;
        
        $stokDiminta = ($beratVarian > 0) ? $beratVarian * $validatedData['jumlah'] : $validatedData['jumlah'];

        if ($produk->stok < $stokDiminta) {
            return redirect()->back()->withInput()->with('error', 'Gagal! Stok untuk "'. $produk->nama .'" tidak mencukupi.');
        }

        $deskripsiVarian = ($beratVarian > 0) ? $beratVarian . ' ' . $produk->satuan : null;
        $userId = Auth::id() ?? 1;

        DB::beginTransaction();
        try {
            if ($validatedData['action_type'] === 'pratinjau') {
                $existingItem = PratinjauItem::where('toko_id', $validatedData['toko_id'])
                    ->where('merchant_id', $validatedData['merchant_id'])
                    ->where('ekspedisi_id', $validatedData['ekspedisi_id'])
                    ->where('produk_id', $validatedData['produk_id'])
                    ->where('berat_per_item', $beratVarian) // Gunakan variabel yang aman
                    ->where('user_id', $userId)->first();

                if ($existingItem) {
                    $existingItem->increment('jumlah', $validatedData['jumlah']);
                } else {
                    PratinjauItem::create([
                        'toko_id' => $validatedData['toko_id'],
                        'merchant_id' => $validatedData['merchant_id'],
                        'ekspedisi_id' => $validatedData['ekspedisi_id'],
                        'produk_id' => $validatedData['produk_id'],
                        'jumlah' => $validatedData['jumlah'],
                        'berat_per_item' => $beratVarian,
                        'deskripsi_varian' => $deskripsiVarian,
                        'user_id' => $userId,
                    ]);
                }
                $redirect = redirect()->route('pengiriman.create')
                                  ->with('success', 'Item berhasil ditambahkan ke pratinjau!')
                                  ->withInput($request->except(['produk_id', 'jumlah', 'berat_varian']));
            } else { // Proses Langsung
                $paket = PaketPengiriman::create([
                    'toko_id'       => $validatedData['toko_id'],
                    'merchant_id'   => $validatedData['merchant_id'],
                    'ekspedisi_id'  => $validatedData['ekspedisi_id'],
                    'status'        => 'proses',
                    'user_id'       => $userId,
                ]);

                $paket->items()->create([
                    'produk_id' => $validatedData['produk_id'],
                    'jumlah'    => $validatedData['jumlah'],
                    'berat_per_item' => $beratVarian,
                    'deskripsi_varian' => $deskripsiVarian,
                ]);
                $redirect = redirect()->route('pengiriman.index')->with('success', 'Satu item berhasil diproses langsung!');
            }
            DB::commit();
            return $redirect;

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function pratinjau()
    {
        $pratinjauItems = PratinjauItem::with(['toko', 'merchant', 'ekspedisi', 'produk.jenisProduk'])
        ->oldest()
        ->get();
            
        // FIX: Urutkan berdasarkan nama produk A-Z
        $sortedItems = $pratinjauItems->sortBy(fn($item) => optional($item->produk)->nama);
        $groupedItems = $sortedItems->groupBy(fn($item) => $item->toko_id . '-' . $item->merchant_id . '-' . $item->ekspedisi_id);
        
        return view('pengiriman.pratinjau', [
            'title' => 'Pratinjau Pengiriman',
            'groupedItems' => $groupedItems,
        ]);
    }

    public function prosesPratinjau(Request $request)
    {
        // Langkah 1: Ambil data jumlah terbaru dari input form yang tersembunyi
        $updates = json_decode($request->input('updates'), true);

        // Jika karena suatu hal tidak ada data update, hentikan.
        if (empty($updates)) {
            return redirect()->route('pengiriman.pratinjau')->with('error', 'Tidak ada item untuk diproses.');
        }

        // Ambil ID dari data update untuk mengambil model lengkap dari DB
        $itemIds = collect($updates)->pluck('id');
        $pratinjauItems = PratinjauItem::whereIn('id', $itemIds)->where('user_id', auth()->id() ?? 1)->with('produk')->get();
        
        // Buat pemetaan (map) dari ID ke jumlah baru untuk pencarian yang cepat
        $jumlahMap = collect($updates)->keyBy('id');

        // Langkah 2: Pengecekan Stok dengan jumlah TERBARU dari layar
        foreach ($pratinjauItems as $item) {
            // Jika item tidak ditemukan di map, lewati (sebagai pengaman)
            if (!isset($jumlahMap[$item->id])) continue;

            $jumlahBaru = $jumlahMap[$item->id]['jumlah'];
            $stokDiminta = ($item->berat_per_item > 0) ? $item->berat_per_item * $jumlahBaru : $jumlahBaru;
            
            if (optional($item->produk)->stok < $stokDiminta) {
                return redirect()->route('pengiriman.pratinjau')
                    ->with('error', 'Proses dibatalkan! Stok untuk "'. optional($item->produk)->nama .'" tidak mencukupi.');
            }
        }

        // Langkah 3: Proses pembuatan paket dengan data yang sudah divalidasi
        DB::beginTransaction();
        try {
            $groupedItems = $pratinjauItems->groupBy(fn ($item) => $item->toko_id . '-' . $item->merchant_id . '-' . $item->ekspedisi_id);

            foreach ($groupedItems as $group) {
                $firstItem = $group->first();
                $paket = PaketPengiriman::create([
                    'toko_id'       => $firstItem->toko_id,
                    'merchant_id'   => $firstItem->merchant_id,
                    'ekspedisi_id'  => $firstItem->ekspedisi_id,
                    'status'        => 'proses',
                    'user_id'       => auth()->id() ?? 1,
                ]);

                foreach ($group as $item) {
                    // Gunakan jumlah TERBARU dari map saat membuat item paket
                    $jumlahBaru = $jumlahMap[$item->id]['jumlah'];
                    
                    $paket->items()->create([
                        'produk_id'        => $item->produk_id,
                        'jumlah'           => $jumlahBaru, // <-- Menggunakan jumlah baru
                        'berat_per_item'   => $item->berat_per_item,
                        'deskripsi_varian' => $item->deskripsi_varian,
                    ]);
                }
            }

            // Hapus semua item dari keranjang pratinjau setelah berhasil diproses
            PratinjauItem::query()->delete();
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('pengiriman.pratinjau')->with('error', 'Terjadi kesalahan. Proses pengiriman dibatalkan.');
        }

        return redirect()->route('pengiriman.index')->with('success', 'Semua item berhasil diproses menjadi paket pengiriman!');
    }

    ## ================== VERSI FINAL DENGAN LOGIKA STOK YANG BENAR ==================
    public function updateStatusPaket(Request $request, PaketPengiriman $paketPengiriman)
    {
        $validated = $request->validate(['status' => 'required|in:proses,selesai,dibatalkan']);
        $oldStatus = $paketPengiriman->status;
        $newStatus = $validated['status'];

        if ($oldStatus === $newStatus) { return redirect()->back()->with('info', 'Status paket tidak berubah.'); }

        DB::beginTransaction();
        try {
            if ($newStatus === 'selesai' && $oldStatus !== 'selesai') {
                foreach ($paketPengiriman->items as $item) {
                    $jumlahPengurang = ($item->berat_per_item > 0) ? $item->berat_per_item * $item->jumlah : $item->jumlah;
                    if(optional($item->produk)->stok < $jumlahPengurang) {
                        throw new \Exception('Stok untuk produk "'. optional($item->produk)->nama .'" tidak mencukupi saat akan diselesaikan.');
                    }
                    $item->produk()->lockForUpdate()->decrement('stok', $jumlahPengurang);
                }
            } 
            else if ($oldStatus === 'selesai' && $newStatus !== 'selesai') {
                foreach ($paketPengiriman->items as $item) {
                    $jumlahPenambah = ($item->berat_per_item > 0) ? $item->berat_per_item * $item->jumlah : $item->jumlah;
                    $item->produk()->lockForUpdate()->increment('stok', $jumlahPenambah);
                }
            }
            $paketPengiriman->update(['status' => $newStatus]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }
        return redirect()->back()->with('success', 'Status paket berhasil diperbarui!');
    }

        /**
     * Mengupdate jumlah item di pratinjau via AJAX.
     */
    public function updateJumlahPratinjau(Request $request, PratinjauItem $pratinjauItem)
    {
        // 1. Validasi input jumlah yang dikirim oleh JavaScript
        $validated = $request->validate([
            'jumlah' => 'required|integer|min:1',
        ]);

        // 2. Keamanan: Pastikan user hanya bisa mengupdate item miliknya sendiri
        if ($pratinjauItem->user_id !== (auth()->id() ?? 1)) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        // 3. Update jumlah item di database
        $pratinjauItem->update([
            'jumlah' => $validated['jumlah']
        ]);

        // 4. Kirim respons sukses dalam format JSON agar bisa dibaca oleh Toastr
        return response()->json(['success' => true, 'message' => 'Jumlah diperbarui!']);
    }
    
    public function hapusDariPratinjau(PratinjauItem $pratinjauItem)
    {
        if ($pratinjauItem->user_id !== (Auth::id() ?? 1)) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }
        $pratinjauItem->delete();
        return redirect()->route('pengiriman.pratinjau')->with('success', 'Item berhasil dihapus dari pratinjau.');
    }
}