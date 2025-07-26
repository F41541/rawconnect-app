<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KategoriController extends Controller
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

        $kategoris = Kategori::orderBy($sortField, $sortOrder)->paginate(10);
        session(['index_return_url' => request()->fullUrl()]);
        return view('kategori.index', [
            'title' => 'MANAJEMEN KATEGORI',
            'kategoris' => $kategoris,
            'sortField' => $sortField,
            'sortOrder' => $sortOrder,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('kategori.create', [
            'title' => 'TAMBAH KATEGORI BARU',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:kategoris,name',
        ]);
        Kategori::create($validatedData);
        return redirect()->back()->with('success', 'Kategori baru berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kategori $kategori)
    {
        return view('kategori.edit', [
            'title' => 'EDIT KATEGORI',
            'kategori' => $kategori,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kategori $kategori)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('kategoris')->ignore($kategori->id)],
        ]);
        $kategori->update($validatedData);
        return redirect(session('index_return_url', route('superadmin.kategori.index')))
                   ->with('success', 'Kategori berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kategori $kategori)
    {
        if ($kategori->jenisProduks()->exists()) {
            return redirect()->back()
                            ->with('error', 'Kategori "'. $kategori->name .'" tidak bisa dihapus karena masih digunakan oleh Jenis Produk.');
        }

        $kategori->delete();
        
        return redirect()->back()->with('success', 'Kategori berhasil dihapus!');
    }
}
