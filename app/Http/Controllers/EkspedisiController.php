<?php

namespace App\Http\Controllers;

use App\Models\Ekspedisi;
use App\Models\LayananPengiriman;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EkspedisiController extends Controller
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
            $sortField = 'name';
        }

        $ekspedisis = Ekspedisi::orderBy($sortField, $sortOrder)->paginate(10);
        session(['index_return_url' => request()->fullUrl()]);

        return view('ekspedisi.index', [
            'title' => 'MANAJEMEN EKSPEDISI',
            'ekspedisis' => $ekspedisis,
            'sortField' => $sortField,
            'sortOrder' => $sortOrder,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('ekspedisi.create', [
            'title' => 'TAMBAH EKSPEDISI BARU',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:ekspedisis,name',
        ]);

        Ekspedisi::create($validatedData);

        return redirect()->back()->with('success', 'Ekspedisi baru berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ekspedisi $ekspedisi)
    {
        return view('ekspedisi.edit', [
            'title' => 'EDIT EKSPEDISI',
            'ekspedisi' => $ekspedisi,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ekspedisi $ekspedisi)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('ekspedisis')->ignore($ekspedisi->id)],
        ]);

        $ekspedisi->update($validatedData);

        return redirect(session('index_return_url', route('superadmin.ekspedisi.index')))
                   ->with('success', 'Ekspedisi berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ekspedisi $ekspedisi)
    {
        if ($ekspedisi->layananPengiriman()->exists()) {
            return redirect()->back()
                            ->with('error', 'Ekspedisi "'. $ekspedisi->name .'" tidak bisa dihapus karena masih terhubung dengan Layanan Pengiriman.');
        }

        $ekspedisi->delete();
        
        return redirect()->back()->with('success', 'Ekspedisi berhasil dihapus!');
    }
}
