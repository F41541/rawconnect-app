<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="container py-1">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header">Tambah Konfigurasi Layanan Baru</div>
                    <div class="card-body">
                        <form action="{{ route('superadmin.layanan-pengiriman.store') }}" method="POST">
                            @csrf
                            
                            {{-- Pilih Toko --}}
                            <div class="mb-3">
                                <label for="toko_id" class="form-label">Toko</label>
                                <select name="toko_id" id="toko_id" class="form-select @error('toko_id') is-invalid @enderror" required>
                                    <option value="" disabled selected>Pilih Toko...</option>
                                    @foreach($tokos as $toko)
                                        <option value="{{ $toko->id }}" {{ old('toko_id') == $toko->id ? 'selected' : '' }}>{{ $toko->name }}</option>
                                    @endforeach
                                </select>
                                @error('toko_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Pilih Merchant --}}
                            <div class="mb-3">
                                <label for="merchant_id" class="form-label">Merchant</label>
                                <select name="merchant_id" id="merchant_id" class="form-select @error('merchant_id') is-invalid @enderror" required>
                                    <option value="" disabled selected>Pilih Merchant...</option>
                                    @foreach($merchants as $merchant)
                                        <option value="{{ $merchant->id }}" {{ old('merchant_id') == $merchant->id ? 'selected' : '' }}>{{ $merchant->name }}</option>
                                    @endforeach
                                </select>
                                @error('merchant_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Pilih Ekspedisi (Checkbox) --}}
                            <div class="mb-3">
                                <label class="form-label">Pilih Ekspedisi (bisa lebih dari satu):</label>
                                {{-- Menampilkan error validasi array --}}
                                @error('ekspedisi_ids') 
                                    <div class="alert alert-danger p-2" style="font-size: 0.9rem;">{{ $message }}</div> 
                                @enderror
                                <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                    @foreach ($ekspedisis as $ekspedisi)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="ekspedisi_ids[]" value="{{ $ekspedisi->id }}" id="ekspedisi-{{ $ekspedisi->id }}"
                                                {{-- Logika untuk mempertahankan centang jika validasi gagal --}}
                                                {{ in_array($ekspedisi->id, old('ekspedisi_ids', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="ekspedisi-{{ $ekspedisi->id }}">
                                                {{ $ekspedisi->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end mt-4">
                                <a href="{{ route('superadmin.layanan-pengiriman.index') }}" class="btn btn-secondary me-2">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan Layanan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
