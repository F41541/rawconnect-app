<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="container py-1">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Edit Produk: {{ $produk->nama }}</div>
                    <div class="card-body">
                        <form action="{{ route('superadmin.produk.update', $produk->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT') {{-- Method wajib untuk update --}}

                            {{-- NAMA PRODUK --}}
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Produk</label>
                                <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama', $produk->nama) }}" required>
                                @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- JENIS PRODUK (Dropdown) --}}
                            <div class="mb-3">
                                <label for="jenis_produk_id" class="form-label">Jenis Produk</label>
                                <select class="form-select @error('jenis_produk_id') is-invalid @enderror" id="jenis_produk_id" name="jenis_produk_id" required>
                                    @foreach ($jenisProduks as $jenisProduk)
                                        <option value="{{ $jenisProduk->id }}" {{ old('jenis_produk_id', $produk->jenis_produk_id) == $jenisProduk->id ? 'selected' : '' }}>
                                            {{ $jenisProduk->name }}
                                            (@foreach($jenisProduk->kategoris as $kategori){{ $kategori->name }}{{ !$loop->last ? ', ' : '' }}@endforeach)
                                        </option>
                                    @endforeach
                                </select>
                                @error('jenis_produk_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- TOKO (Dropdown) --}}
                            <div class="mb-3">
                                <label for="toko_id" class="form-label">Toko</label>
                                <select class="form-select @error('toko_id') is-invalid @enderror" id="toko_id" name="toko_id" required>
                                    @foreach ($tokos as $toko)
                                        <option value="{{ $toko->id }}" {{ old('toko_id', $produk->toko_id) == $toko->id ? 'selected' : '' }}>{{ $toko->name }}</option>
                                    @endforeach
                                </select>
                                @error('toko_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- STOK --}}
                            <div class="mb-3">
                                <label for="stok" class="form-label">Stok</label>
                                <input type="number" class="form-control @error('stok') is-invalid @enderror" id="stok" name="stok" value="{{ old('stok', $produk->stok) }}" required>
                                @error('stok') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="minimal_stok" class="form-label">Jumlah Minimal Stok</label>
                                <input type="number" name="minimal_stok" id="minimal_stok" class="form-control" value="{{ old('minimal_stok', $produk->minimal_stok ?? 10) }}" required>
                                <div class="form-text">Peringatan akan muncul jika stok produk ini di bawah atau sama dengan angka ini.</div>
                            </div>

                            {{-- SATUAN --}}
                            <div class="mb-3">
                                <label for="satuan" class="form-label">Satuan</label>
                                <select class="form-select @error('satuan') is-invalid @enderror" id="satuan" name="satuan" required>
                                    {{-- Untuk create.blade.php, gunakan old('satuan') == '...' --}}
                                    {{-- Untuk edit.blade.php, gunakan old('satuan', $produk->satuan) == '...' --}}
                                    <option value="pouch" {{ old('satuan', $produk->satuan ?? 'pouch') == 'pouch' ? 'selected' : '' }}>Pouch</option>
                                    <option value="gram" {{ old('satuan', $produk->satuan ?? '') == 'gram' ? 'selected' : '' }}>Gram</option>
                                    <option value="kg" {{ old('satuan', $produk->satuan ?? '') == 'kg' ? 'selected' : '' }}>Kilogram</option>
                                    <option value="pcs" {{ old('satuan', $produk->satuan ?? '') == 'pcs' ? 'selected' : '' }}>Pcs</option>
                                    <option value="roll" {{ old('satuan', $produk->satuan ?? '') == 'roll' ? 'selected' : '' }}>Roll</option>
                                    <option value="pack" {{ old('satuan', $produk->satuan ?? '') == 'pack' ? 'selected' : '' }}>Pack</option>
                                </select>
                                @error('satuan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- FOTO --}}
                            <div class="mb-3">
                                <label for="foto" class="form-label">Ganti Foto Produk</label>
                                @if($produk->foto)
                                <div class="mb-2">
                                    <p class="mb-1 fw-bold"><small>Foto Saat Ini:</small></p>
                                    <img src="{{ asset('uploads/foto_produk/' . $produk->foto) }}" alt="Foto saat ini" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">
                                </div>
                                @endif
                                <input class="form-control @error('foto') is-invalid @enderror" type="file" id="foto" name="foto">
                                <div class="form-text">Kosongkan jika tidak ingin mengganti foto.</div>
                                @error('foto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="d-flex justify-content-end">
                                <a href="{{ session('produk_return_url', route('superadmin.produk.index')) }}" class="btn btn-secondary me-2">Batal</a>
                                <button type="submit" class="btn btn-primary">Update Produk</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
