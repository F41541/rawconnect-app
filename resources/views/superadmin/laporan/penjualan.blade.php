<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Laporan Penjualan</h5>
        </div>
        <div class="card-body">
            {{-- Form Filter Tanggal --}}
            <form method="GET" action="{{ route('superadmin.laporan.penjualan') }}" class="row g-3 mb-4 align-items-end">
                <div class="col-md-3">
                    <label for="tanggal_mulai" class="form-label">Dari Tanggal</label>
                    <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}">
                </div>
                <div class="col-md-3">
                    <label for="tanggal_selesai" class="form-label">Sampai Tanggal</label>
                    <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('superadmin.laporan.penjualan') }}" class="btn btn-secondary w-100">Reset</a>
                </div>
            </form>

            {{-- Tabel Laporan --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama Produk</th>
                            <th>Varian</th>
                            <th>Jumlah Keluar</th>
                            <th>Stok Sisa</th>
                            <th>Diproses Oleh</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($laporan as $log)
                            <tr>
                                <td>{{ $log->created_at->format('d M Y, H:i') }}</td>
                                <td>{{ optional($log->produk)->nama }}</td>
                                <td>{{ $log->keterangan }}</td>
                                <td class="text-danger fw-bold">{{ $log->jumlah_berubah }}</td>
                                <td>{{ $log->stok_sesudah }}</td>
                                <td>{{ optional($log->user)->name }}</td>
                                <td>Penjualan</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data penjualan pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $laporan->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</x-layout>