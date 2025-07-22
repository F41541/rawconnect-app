<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="container py-3">
        <div class="card shadow-sm rounded-4 border">
            <div class="card-body">
                <form method="GET" action="{{ route('log.stok') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="tanggal_mulai" class="form-label fw-semibold">Periode Mulai</label>
                        <input type="date" class="form-control rounded-3 shadow-sm" name="tanggal_mulai" id="tanggal_mulai" value="{{ request('tanggal_mulai', now()->subDays(6)->toDateString()) }}">
                    </div>
                    <div class="col-md-1 d-flex align-items-center justify-content-center pt-3 fw-bold">
                        s/d
                    </div>
                    <div class="col-md-4">
                        <label for="tanggal_selesai" class="form-label fw-semibold">Periode Selesai</label>
                        <input type="date" class="form-control rounded-3 shadow-sm" name="tanggal_selesai" id="tanggal_selesai" value="{{ request('tanggal_selesai', now()->toDateString()) }}">
                    </div>
                    <div class="col-md-3 pt-2">
                        <button type="submit" class="btn btn-primary w-100 fw-semibold shadow-sm rounded-3">
                            <i class="bi bi-funnel me-1"></i> Terapkan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card mt-4 shadow-sm rounded-4 border overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle table-hover m-0">
                        <thead class="table-light">
                            <tr>
                                <th>Waktu</th>
                                <th>Produk</th>
                                <th>Tipe</th>
                                <th class="text-end">Perubahan</th>
                                <th class="text-end">Stok Awal</th>
                                <th class="text-end">Stok Akhir</th>
                                <th>Oleh</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($logs as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('d M Y, H:i') }}</td>
                                    <td>{{ optional($log->produk)->nama ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $log->tipe === 'masuk' ? 'success' : 'warning' }}-subtle text-{{ $log->tipe === 'masuk' ? 'success' : 'warning' }}-emphasis">
                                            {{ ucfirst($log->tipe) }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold {{ $log->jumlah_berubah > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $log->jumlah_berubah > 0 ? '+' : '' }}{{ $log->jumlah_berubah }}
                                    </td>
                                    <td class="text-end">{{ $log->stok_sebelum }}</td>
                                    <td class="text-end">{{ $log->stok_sesudah }}</td>
                                    <td>{{ optional($log->user)->name ?? 'Sistem' }}</td>
                                    <td>{{ $log->keterangan ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        Belum ada riwayat perubahan stok.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layout>
