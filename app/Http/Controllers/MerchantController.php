<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Models\LayananPengiriman;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MerchantController extends Controller
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

        $merchants = Merchant::orderBy($sortField, $sortOrder)->paginate(10);
        session(['index_return_url' => request()->fullUrl()]);
        return view('merchant.index', [
            'title' => 'MANAJEMEN MERCHANT',
            'merchants' => $merchants,
            'sortField' => $sortField,
            'sortOrder' => $sortOrder,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('merchant.create', [
            'title' => 'TAMBAH MERCHANT BARU',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:merchants,name',
        ]);

        Merchant::create($validatedData);

        return redirect()->back()->with('success', 'Merchant baru berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Merchant $merchant)
    {
        return view('merchant.edit', [
            'title' => 'EDIT MERCHANT',
            'merchant' => $merchant,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Merchant $merchant)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('merchants')->ignore($merchant->id)],
        ]);

        $merchant->update($validatedData);

        return redirect(session('index_return_url', route('superadmin.merchant.index')))
                   ->with('success', 'Merchant berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Merchant $merchant)
    {
        if ($merchant->layananPengiriman()->exists()) {
            return redirect()->back()
                            ->with('error', 'Merchant "'. $merchant->name .'" tidak bisa dihapus karena masih terhubung dengan Layanan Pengiriman');
        }

        $merchant->delete();

        return redirect()->back()->with('success', 'Merchant berhasil dihapus!');
    }
}
