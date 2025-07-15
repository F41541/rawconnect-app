<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Formulir Tambah Pengguna Baru</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('superadmin.user.store') }}" method="POST">
                        @csrf
                        {{-- Input Nama --}}
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autofocus>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Input Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">Alamat Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Pilihan Role --}}
                        <div class="mb-3">
                            <label for="role" class="form-label">Peran (Role)</label>
                            <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                                <option value="pegawai" {{ old('role') == 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="super-admin" {{ old('role') == 'super-admin' ? 'selected' : '' }}>Super Admin</option>
                            </select>
                            @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- Input Password --}}
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                            <div class="form-text">Minimal 8 karakter.</div>
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('superadmin.user.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan Pengguna</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layout>