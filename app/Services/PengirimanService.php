<?php

namespace App\Services;

// Mengimpor semua model yang kita butuhkan

use App\Models\Produk;
use App\Models\PratinjauItem;
use App\Models\PaketPengiriman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;



class PengirimanService
{
    public function tambahKePratinjau(array $validatedData, Request $request)
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

    public function prosesPratinjau(Request $request)
    {
        $updates = json_decode($request->input('updates'), true);

        if (empty($updates)) {
            return redirect()->route('pengiriman.pratinjau')->with('error', 'Tidak ada item untuk diproses.');
        }

        $itemIds = collect($updates)->pluck('id');
        $pratinjauItems = PratinjauItem::whereIn('id', $itemIds)->with('produk')->get();
        
        $jumlahMap = collect($updates)->keyBy('id');

        foreach ($pratinjauItems as $item) {
            if (!$item->produk) {
                DB::rollBack();
                return redirect()->route('pengiriman.pratinjau')->with('error', 'Proses dibatalkan! Salah satu item di pratinjau tidak memiliki produk yang valid.');
            }

            if (!isset($jumlahMap[$item->id])) continue;

            $jumlahBaru = $jumlahMap[$item->id]['jumlah'];
            $stokDiminta = ($item->berat_per_item > 0) ? $item->berat_per_item * $jumlahBaru : $jumlahBaru;
            
            if ($item->produk->stok < $stokDiminta) {
                return redirect()->route('pengiriman.pratinjau')
                    ->with('error', 'Proses dibatalkan! Stok untuk "'. $item->produk->nama .'" tidak mencukupi.');
            }
        }

        DB::beginTransaction();
        try {
            $groupedItems = $pratinjauItems->groupBy(fn ($item) => $item->user_id . '-' . $item->toko_id . '-' . $item->merchant_id . '-' . $item->ekspedisi_id);
            foreach ($groupedItems as $group) {
                $firstItem = $group->first();
                $paket = PaketPengiriman::create([
                    'toko_id'       => $firstItem->toko_id,
                    'merchant_id'   => $firstItem->merchant_id,
                    'ekspedisi_id'  => $firstItem->ekspedisi_id,
                    'status'        => 'proses',
                    'user_id'       => $firstItem->user_id,
                ]);

                foreach ($group as $item) {
                    $jumlahBaru = $jumlahMap[$item->id]['jumlah'];
                    
                    $paket->items()->create([
                        'produk_id'        => $item->produk_id,
                        'jumlah'           => $jumlahBaru,
                        'berat_per_item'   => $item->berat_per_item,
                        'deskripsi_varian' => $item->deskripsi_varian,
                    ]);
                }
            }

            PratinjauItem::query()->delete();
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('pengiriman.pratinjau')->with('error', 'Terjadi kesalahan. Proses pengiriman dibatalkan.');
        }

        return redirect()->route('pengiriman.index')->with('success', 'Semua item berhasil diproses menjadi paket pengiriman!');
    }

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
                    // PERBAIKAN: Tambahkan pengecekan ini
                    if (!$item->produk) {
                        throw new \Exception('Gagal menyelesaikan paket karena salah satu produknya telah dihapus.');
                    }
                    
                    $jumlahPengurang = ($item->berat_per_item > 0) ? $item->berat_per_item * $item->jumlah : $item->jumlah;
                    if($item->produk->stok < $jumlahPengurang) {
                        throw new \Exception('Stok untuk produk "'. $item->produk->nama .'" tidak mencukupi saat akan diselesaikan.');
                    }
                    $item->produk->recordStockChange(-$jumlahPengurang, 'penjualan', 'Paket ID: ' . $paketPengiriman->id);
                    $item->produk()->lockForUpdate()->decrement('stok', $jumlahPengurang);
                }
            } 
            else if ($oldStatus === 'selesai' && $newStatus !== 'selesai') {
                foreach ($paketPengiriman->items as $item) {
                    $jumlahPenambah = ($item->berat_per_item > 0) ? $item->berat_per_item * $item->jumlah : $item->jumlah;
                    $item->produk->recordStockChange($jumlahPenambah, 'retur/batal', 'Paket ID: ' . $paketPengiriman->id);
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
        // if ($pratinjauItem->user_id !== (auth()->id() ?? 1)) {
        //     return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        // }

        // 3. Update jumlah item di database
        $pratinjauItem->update([
            'jumlah' => $validated['jumlah']
        ]);

        // 4. Kirim respons sukses dalam format JSON agar bisa dibaca oleh Toastr
        return response()->json(['success' => true, 'message' => 'Jumlah diperbarui!']);
    }
    
    public function hapusDariPratinjau(Request $request, PratinjauItem $pratinjauItem)
    {
        // if ($pratinjauItem->user_id !== (Auth::id() ?? 1)) {
        //     return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        // }
        $pratinjauItem->delete();
        return redirect()->route('pengiriman.pratinjau')->with('success', 'Item berhasil dihapus dari pratinjau.');
    }
}