<?php

namespace App\Http\Controllers;
use \App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil semua user kecuali super admin yang sedang login, urutkan, dan paginasi
        $users = User::where('id', '!=', auth()->id())
                            ->orderBy('name', 'asc')
                            ->paginate(10);

        return view('user.index', [
            'title' => 'MANAJEMEN PENGGUNA',
            'users' => $users
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('user.create', [
            'title' => 'Tambah Pengguna Baru'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validasi input
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role' => ['required', \Illuminate\Validation\Rule::in(['super-admin', 'admin', 'pegawai'])],
            'password' => ['required', 'string', 'min:8'],
        ]);

        // 2. Buat pengguna baru, jangan lupa Hash password untuk keamanan
        User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'role' => $validatedData['role'],
            'password' => \Illuminate\Support\Facades\Hash::make($validatedData['password']),
        ]);

        // 3. Redirect kembali ke halaman daftar dengan pesan sukses
        return redirect()->back()->with('success', 'Pengguna baru berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // Laravel secara ajaib akan menemukan user berdasarkan ID dari URL.
        // Ini disebut Route Model Binding.
        return view('user.edit', [
            'title' => 'Edit Pengguna: ' . $user->name,
            'user' => $user,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // 1. Validasi input
        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // Pastikan email unik, tapi abaikan untuk user saat ini
            'email' => ['required', 'string', 'email', 'max:255', \Illuminate\Validation\Rule::unique('users')->ignore($user->id)],
            'role' => ['required', \Illuminate\Validation\Rule::in(['super-admin', 'admin', 'pegawai'])],
            // Password sekarang boleh kosong (nullable)
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        // 2. Cek apakah password diisi atau tidak
        if (empty($validatedData['password'])) {
            // Jika password kosong, hapus dari data yang akan diupdate
            unset($validatedData['password']);
        } else {
            // Jika diisi, hash password baru sebelum disimpan
            $validatedData['password'] = \Illuminate\Support\Facades\Hash::make($validatedData['password']);
        }

        // 3. Update data pengguna
        $user->update($validatedData);

        // 4. Redirect kembali ke halaman daftar dengan pesan sukses
        return redirect()->route('user.index')->with('success', 'Data pengguna berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Pengaman tambahan: pastikan Super Admin tidak bisa menghapus akunnya sendiri
        if ($user->id === auth()->id()) {
            return redirect()->route('user.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Hapus pengguna dari database
        $user->delete();

        // Redirect kembali dengan pesan sukses
        return redirect()->route('user.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
