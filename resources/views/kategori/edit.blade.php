<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>
    <div class="container py-1">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Edit Kategori: {{ $kategori->name }}</div>
                    <div class="card-body">
                        <form action="{{ route('superadmin.kategori.update', $kategori->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Kategori</label>
                                {{-- PENJELASAN: Menambahkan placeholder untuk konsistensi. --}}
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $kategori->name) }}" placeholder="Contoh: Produk Jual" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('superadmin.kategori.index') }}" class="btn btn-secondary me-2">Batal</a>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
