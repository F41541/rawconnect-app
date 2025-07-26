<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>
    <div class="container py-1">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    {{-- PENJELASAN (BUG #4): Mengganti judul dinamis menjadi statis --}}
                    <div class="card-header">Tambah Jenis Produk Baru</div>
                    <div class="card-body">
                        <form action="{{ route('superadmin.jenis-produk.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Jenis Produk</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: 10 Tea Bag" required autofocus>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Pilih Kategori (bisa lebih dari satu):</label>
                                @error('kategoris') 
                                    <div class="alert alert-danger p-2" style="font-size: 0.9rem;">{{ $message }}</div> 
                                @enderror
                                <div class="border rounded p-2" style="max-height: 150px; overflow-y: auto;">
                                    @foreach ($kategoris as $kategori)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="kategoris[]" value="{{ $kategori->id }}" id="kategori-create-{{ $kategori->id }}"
                                                {{ in_array($kategori->id, old('kategoris', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="kategori-create-{{ $kategori->id }}">
                                                {{ $kategori->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('superadmin.jenis-produk.index') }}" class="btn btn-secondary me-2">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
