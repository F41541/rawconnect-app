<?php

namespace App\Http\Controllers;

use App\Models\Ekspedisi;
use App\Models\Merchant;
use App\Models\Toko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TokoController extends Controller
{
    public function index(Request $request)
    {
        $sortField = $request->query('sort', 'created_at');
        $sortOrder = $request->query('order', 'asc');
        $allowedSorts = ['name', 'created_at'];
        if (!in_array($sortField, $allowedSorts)) {
            $sortField = 'name';
        }
        
        $tokos = Toko::orderBy($sortField, $sortOrder)->paginate(10);
        session(['index_return_url' => request()->fullUrl()]);
        return view('toko.index', [
            'title' => 'MANAJEMEN TOKO',
            'tokos' => $tokos,
            'sortField' => $sortField,
            'sortOrder' => $sortOrder,
        ]);
    }

    public function create()
    {
        return view('toko.create', [
            'title' => 'TAMBAH TOKO BARU',
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:tokos,name',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);
        if ($request->hasFile('logo')) {
            $extension = $request->file('logo')->getClientOriginalExtension();
            $newFileName = now()->format('Ymd_His') . '_' . uniqid() . '.' . $extension;
            $request->file('logo')->storeAs('logo_toko', $newFileName, 'uploads');
            $validatedData['logo'] = $newFileName;
        }
        Toko::create($validatedData);
        return redirect()->back()->with('success', 'Toko baru berhasil ditambahkan!');
    }

    public function edit(Toko $toko)
    {
        return view('toko.edit', [
            'title' => 'EDIT TOKO',
            'toko' => $toko,
        ]);
    }

    public function update(Request $request, Toko $toko)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('tokos')->ignore($toko->id)],
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($toko->logo) {
                Storage::disk('uploads')->delete('logo_toko/' . $toko->logo);
            }
            $extension = $request->file('logo')->getClientOriginalExtension();
            $newFileName = now()->format('Ymd_His') . '_' . uniqid() . '.' . $extension;
            $request->file('logo')->storeAs('logo_toko', $newFileName, 'uploads');
            $validatedData['logo'] = $newFileName;
        }

        $toko->update($validatedData);

        return redirect(session('index_return_url', route('superadmin.toko.index')))
                   ->with('success', 'Toko berhasil diperbarui!');
    }

    public function destroy(Toko $toko)
    {
        if ($toko->produks()->exists()) {
            return redirect()->back()->with('error', 'Toko "'. $toko->name .'" tidak bisa dihapus karena masih digunakan oleh Produk');
        }

        if ($toko->layananPengiriman()->exists()) {
            return redirect()->back()->with('error', 'Toko "'. $toko->name .'" tidak bisa dihapus karena masih digunakan  Layanan Pengiriman');
        }


        if ($toko->logo) {
            Storage::disk('uploads')->delete('logo_toko/' . $toko->logo);
        }
        $toko->delete();
        return redirect()->back()->with('success', 'Toko berhasil dihapus!');
    }
}
