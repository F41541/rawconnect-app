<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="container py-1">

        {{-- FILTER SECTION --}}
        <div class="card border-0 shadow mb-4 justify-content-between">
            <div class="card-body py-3 justify-content-between">
                <form method="GET" action="{{ route('superadmin.laporan.penjualan') }}" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label mb-1">Dari Tanggal</label>
                        <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="form-control form-control-sm shadow-sm">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label mb-1">Sampai Tanggal</label>
                        <input type="date" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}" class="form-control form-control-sm shadow-sm">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="bi bi-funnel me-1"></i>Filter
                        </button>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('superadmin.laporan.penjualan') }}" class="btn btn-sm btn-outline-secondary w-100">
                            <i class="bi bi-x-circle me-1"></i>Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- TABLE SECTION --}}
        <div class="card mt-4 shadow-sm rounded-4 border px-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Produk</th>
                                <th>Varian</th>
                                <th class="text-danger">Keluar</th>
                                <th>Stok Sisa</th>
                                <th>Diproses Oleh</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($laporan as $log)
                                <tr>
                                    <td class="text-muted">{{ $log->created_at->format('d M Y H:i') }}</td>
                                    <td>{{ optional($log->produk)->nama ?? '-' }}</td>
                                    <td>{{ $log->keterangan }}</td>
                                    <td class="fw-bold text-danger">{{ $log->jumlah_berubah }}</td>
                                    <td>{{ $log->stok_sesudah }}</td>
                                    <td>{{ optional($log->user)->name ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="bi bi-folder-x fs-3 mb-2"></i>
                                        <p class="mb-0">Tidak ada data dalam periode ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- PAGINATION --}}
            @if ($laporan->hasPages())
                <div class="card-footer bg-white border-0 py-3 d-flex justify-content-end">
                    {{ $laporan->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layout>
