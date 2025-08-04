<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    {{-- FILTER PANEL --}}
    <div class="mb-4">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">
                <form method="GET" action="{{ route('laporan.stok-rendah') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="jenis_produk_id" class="form-label fw-semibold">Jenis Produk</label>
                        <select name="jenis_produk_id" id="jenis_produk_id" class="form-select shadow-sm">
                            <option value="">Semua Jenis Produk</option>
                            @foreach($jenisProduks as $jenis)
                                <option value="{{ $jenis->id }}" @selected(request('jenis_produk_id') == $jenis->id)>
                                    {{ $jenis->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="sort" class="form-label fw-semibold">Urutkan</label>
                        <select name="sort" id="sort" class="form-select shadow-sm">
                            <option value="stok_asc" @selected(request('sort', 'stok_asc') == 'stok_asc')>Stok Paling Sedikit</option>
                            <option value="nama_asc" @selected(request('sort') == 'nama_asc')>Nama Produk (A-Z)</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100 shadow-sm">Terapkan</button>
                    </div>

                    <div class="col-md-2">
                        <a href="{{ route('laporan.stok-rendah') }}" class="btn btn-outline-secondary w-100 shadow-sm">Reset</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- HASIL LAPORAN --}}
    <div class="card shadow-sm border-0 rounded-4">

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Produk</th>
                            <th>Toko</th>
                            <th>Jenis Produk</th>
                            <th class="text-end">Stok / Batas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($produks as $produk)
                            <tr>
                                <td><strong>{{ $produk->nama }}</strong></td>
                                <td>{{ optional($produk->toko)->name }}</td>
                                <td>{{ optional($produk->jenisProduk)->name }}</td>
                                <td class="text-end">
                                    <span class="badge bg-danger-subtle text-danger-emphasis rounded-pill px-3 py-2 fs-6">
                                        {{ $produk->stok }} / <small>{{ $produk->minimal_stok }} {{ $produk->satuan }}</small>
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Tidak ada produk yang stoknya rendah.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            <div class="mt-4">
                {{ $produks->links() }}
            </div>
        </div>
    </div>
</x-layout>
