<x-layout>
    <x-slot:title>Pengaturan Akun</x-slot:title>

    <div class="py-1">
        <div class="row justify-content-center">
            <div class="col-lg-12">

                {{-- SECTION: Informasi Profil --}}
                <div class="bg-white shadow border-0 rounded-4 mb-4">
                    <div class="border-bottom px-4 py-3">
                        <h5 class="mb-0 fw-semibold">Informasi Profil</h5>
                        <small class="text-muted">Update nama dan alamat email akun Anda.</small>
                    </div>
                    <div class="px-4 py-4">
                        <form method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            @method('patch')

                            <div class="mb-3">
                                <label for="name" class="form-label fw-medium">Nama</label>
                                <input id="name" name="name" type="text" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required autofocus>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label fw-medium">Email</label>
                                <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="role" class="form-label fw-medium">Role</label>
                                <input id="role" name="role" type="text" class="form-control" value="{{ $user->role }}" disabled>
                            </div>

                            <div class="d-flex align-items-center gap-3">
                                <button type="submit" class="btn btn-primary px-4">Simpan</button>
                                @if (session('status') === 'profile-updated')
                                    <span class="text-success small">✓ Tersimpan</span>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

                {{-- SECTION: Update Password --}}
                <div class="bg-white shadow border-0 rounded-4">
                    <div class="border-bottom px-4 py-3">
                        <h5 class="mb-0 fw-semibold">Update Password</h5>
                        <small class="text-muted">Gunakan password yang panjang dan acak agar akun tetap aman.</small>
                    </div>
                    <div class="px-4 py-4">
                        <form method="POST" action="{{ route('password.update') }}">
                            @csrf
                            @method('put')

                            <div class="mb-3">
                                <label for="current_password" class="form-label fw-medium">Password Saat Ini</label>
                                <input id="current_password" name="current_password" type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" required>
                                @error('current_password', 'updatePassword')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-medium">Password Baru</label>
                                <input id="password" name="password" type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" required>
                                @error('password', 'updatePassword')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label fw-medium">Konfirmasi Password Baru</label>
                                <input id="password_confirmation" name="password_confirmation" type="password" class="form-control" required>
                            </div>

                            <div class="d-flex align-items-center gap-3">
                                <button type="submit" class="btn btn-primary px-4">Simpan Password</button>
                                @if (session('status') === 'password-updated')
                                    <span class="text-success small">✓ Tersimpan</span>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-layout>
