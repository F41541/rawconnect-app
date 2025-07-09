<x-layout>
    <x-slot:title>Pengaturan Akun</x-slot:title>

    <div class="py-4">
        {{-- Menggunakan satu kolom terpusat untuk kerapian --}}
        <div class="row justify-content-center">
            <div class="col-lg-10">

                {{-- KARTU 1: INFORMASI PROFIL --}}
                {{-- mb-4 digunakan untuk memberi jarak ke kartu di bawahnya --}}
                <div class="card mb-2">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Informasi Profil</h5>
                        <p class="card-text text-muted small mt-1">Update nama dan alamat email akun Anda.</p>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('profile.update') }}">
                            @csrf
                            @method('patch')

                            <div class="mb-3">
                                <label for="name" class="form-label">Nama</label>
                                <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required autofocus>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                                 @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex align-items-center gap-4">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                @if (session('status') === 'profile-updated')
                                    <p class="mb-0 text-success small" role="alert">✓ Tersimpan.</p>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                {{-- KARTU 2: UPDATE PASSWORD --}}
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Update Password</h5>
                        <p class="card-text text-muted small mt-1">Pastikan akun Anda menggunakan password yang panjang dan acak agar tetap aman.</p>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('password.update') }}">
                            @csrf
                            @method('put')

                            <div class="mb-3">
                                <label for="current_password" class="form-label">Password Saat Ini</label>
                                <input id="current_password" name="current_password" type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" required>
                                @error('current_password', 'updatePassword')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password Baru</label>
                                <input id="password" name="password" type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" required>
                                @error('password', 'updatePassword')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                                <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required>
                            </div>

                            <div class="d-flex align-items-center gap-4">
                                <button type="submit" class="btn btn-primary">Simpan Password</button>
                                @if (session('status') === 'password-updated')
                                    <p class="mb-0 text-success small" role="alert">✓ Tersimpan.</p>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-layout>