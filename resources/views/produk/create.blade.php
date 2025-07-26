<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="container py-1">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Tambah Produk Baru</div>
                    <div class="card-body">
                        <form action="{{ route('superadmin.produk.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            {{-- NAMA PRODUK (Input Teks) --}}
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Produk</label>
                                <input type="text" class="form-control @error('nama') is-invalid @enderror" id="nama" name="nama" value="{{ old('nama') }}" placeholder="Masukan Nama Produk" required>
                                @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- JENIS PRODUK (Dropdown) --}}
                            <div class="mb-3">
                                <label for="jenis_produk_id" class="form-label">Jenis Produk</label>
                                <select class="form-select @error('jenis_produk_id') is-invalid @enderror" id="jenis_produk_id" name="jenis_produk_id" required>
                                    <option value="" selected disabled>Pilih Jenis Produk...</option>
                                    @foreach ($jenisProduks as $jenisProduk)
                                        <option value="{{ $jenisProduk->id }}" {{ old('jenis_produk_id') == $jenisProduk->id ? 'selected' : '' }}>
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
                                    <option value="" selected disabled>Pilih Toko...</option>
                                    @foreach ($tokos as $toko)
                                        <option value="{{ $toko->id }}" {{ old('toko_id') == $toko->id ? 'selected' : '' }}>{{ $toko->name }}</option>
                                    @endforeach
                                </select>
                                @error('toko_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- STOK AWAL (Input Angka) --}}
                            <div class="mb-3">
                                <label for="stok" class="form-label">Stok Awal</label>
                                <input type="number" class="form-control @error('stok') is-invalid @enderror" id="stok" name="stok" value="{{ old('stok') }}" placeholder="0" required>
                                @error('stok') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="minimal_stok" class="form-label">Jumlah Minimal Stok</label>
                                <input type="number" name="minimal_stok" id="minimal_stok" class="form-control" value="{{ old('minimal_stok', $produk->minimal_stok ?? 10) }}" required>
                                <div class="form-text">Peringatan akan muncul jika stok produk ini di bawah atau sama dengan angka ini.</div>
                            </div>                           

                            <div class="mb-3">
                                <label for="satuan" class="form-label">Satuan</label>
                                <select class="form-select @error('satuan') is-invalid @enderror" id="satuan" name="satuan" required>
                                    <option value="" selected disabled>Pilih Satuan...</option>
                                    <option value="pouch" {{ old('satuan') == 'pouch' ? 'selected' : '' }}>Pouch</option>
                                    <option value="gram" {{ old('satuan') == 'gram' ? 'selected' : '' }}>Gram</option>
                                    <option value="kg" {{ old('satuan') == 'kg' ? 'selected' : '' }}>Kilogram</option>
                                    <option value="pcs" {{ old('satuan') == 'pcs' ? 'selected' : '' }}>Pcs</option>
                                    <option value="roll" {{ old('satuan') == 'roll' ? 'selected' : '' }}>Roll</option>
                                    <option value="pack" {{ old('satuan') == 'pack' ? 'selected' : '' }}>Pack</option>
                                </select>
                                @error('satuan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- FOTO (Input File) --}}
                            <div class="mb-3">
                                <label for="foto" class="form-label">Foto Produk</label>
                                <input class="form-control @error('foto') is-invalid @enderror" type="file" id="foto" name="foto">
                                @error('foto') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="d-flex justify-content-end">
                                <a href="{{ route('superadmin.produk.index') }}" class="btn btn-secondary me-2">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan Produk</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>