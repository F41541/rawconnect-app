<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JenisProduk;
use App\Models\Kategori;
use Illuminate\Validation\Rule;

class JenisProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sortField = $request->query('sort', 'created_at');
        $sortOrder = $request->query('order', 'asc');
        $allowedSorts = ['name', 'created_at'];
        if (!in_array($sortField, $allowedSorts)) {
            $sortField = 'created_at';
        }
        $jenisProduks = JenisProduk::with('kategoris')->orderBy($sortField, $sortOrder)->paginate(10);
        session(['index_return_url' => request()->fullUrl()]);

        return view('jenis-produk.index', [
            'title' => 'MANAJEMEN JENIS PRODUK',
            'jenisProduks' => $jenisProduks,
            'sortField' => $sortField,
            'sortOrder' => $sortOrder,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('jenis-produk.create', [
            'title' => 'TAMBAH JENIS PRODUK BARU',
            'kategoris' => Kategori::orderBy('name')->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:jenis_produks,name',
            'kategoris' => 'nullable|array', 
            'kategoris.*' => 'exists:kategoris,id'
        ]);

        $jenisProduk = JenisProduk::create(['name' => $validatedData['name']]);
    
        $jenisProduk->kategoris()->sync($request->input('kategoris'));

        return redirect()->back()->with('success', 'Jenis Produk baru berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JenisProduk $jenisProduk)
    {
        return view('jenis-produk.edit', [
            'title' => 'EDIT JENIS PRODUK',
            'jenisProduk' => $jenisProduk,
            'kategoris' => Kategori::orderBy('name')->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JenisProduk $jenisProduk)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('jenis_produks')->ignore($jenisProduk->id)],
            'kategoris' => 'nullable|array', 
            'kategoris.*' => 'exists:kategoris,id'
        ]);

        $jenisProduk->update(['name' => $validatedData['name']]);
        
        $jenisProduk->kategoris()->sync($request->input('kategoris'));

        return redirect(session('index_return_url', route('superadmin.jenis-produk.index')))
                   ->with('success', 'Jenis Produk berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JenisProduk $jenisProduk)
    {

        if ($jenisProduk->produks()->exists()) {
            return redirect()->back()
                            ->with('error', 'Jenis Produk "'. $jenisProduk->name .'" tidak bisa dihapus karena masih digunakan oleh produk.');
        }

        if ($jenisProduk->kategoris()->exists()) {
            return redirect()->back()
                            ->with('error', 'Jenis Produk "'. $jenisProduk->name .'" tidak bisa dihapus karena masih terhubung dengan Kategori.');
        }
        
        $jenisProduk->delete();

        return redirect()->back()->with('success', 'Jenis Produk berhasil dihapus!');
    }
}