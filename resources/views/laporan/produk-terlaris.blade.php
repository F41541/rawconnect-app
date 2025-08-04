<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    {{-- Filter --}}
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body bg-light rounded-4">
            <form method="GET" action="{{ route('laporan.produk-terlaris') }}" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="jenis_produk_id" class="form-label fw-semibold">Jenis Produk</label>
                    <select name="jenis_produk_id" id="jenis_produk_id" class="form-select shadow-sm rounded-3">
                        <option value="">-- Semua Jenis Produk --</option>
                        @foreach($jenisProduks as $jenis)
                            <option value="{{ $jenis->id }}" @selected(request('jenis_produk_id') == $jenis->id)>
                                {{ $jenis->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-5">
                    <label for="periode" class="form-label fw-semibold">Periode</label>
                    <select name="periode" id="periode" class="form-select shadow-sm rounded-3">
                        <option value="1d" @selected($periode_aktif == '1d')>Hari Ini</option>
                        <option value="7d" @selected($periode_aktif == '7d')>7 Hari Terakhir</option>
                        <option value="1m" @selected($periode_aktif == '1m')>Bulan Ini</option>
                        <option value="1y" @selected($periode_aktif == '1y')>Tahun Ini</option>
                    </select>
                </div>

                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary shadow-sm rounded-3">Terapkan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Hasil Tabel --}}
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light border-bottom">
                        <tr>
                            <th style="width: 100px;">Peringkat</th>
                            <th>Nama Produk</th>
                            <th class="text-end">Total Terjual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($produks as $index => $produk)
                            <tr>
                                <td class="fw-semibold text-muted">
                                    {{ $produks->firstItem() + $index }}
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $produk->nama }}</div>
                                    <div class="text-muted small">{{ $produk->jenis_produk->name ?? '' }}</div>
                                </td>
                                <td class="text-end fw-semibold text-primary">
                                    {{ number_format($produk->total_terjual) }} {{ $produk->satuan }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">
                                    Tidak ada data penjualan pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $produks->links() }}
            </div>
        </div>
    </div>
</x-layout>
