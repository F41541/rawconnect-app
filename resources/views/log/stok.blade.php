<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="card">
        <div class="mb-4 p-3 rounded shadow-sm bg-secondary bg-opacity-10">
            <form method="GET" action="{{ route('log.stok') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="tanggal_mulai" class="form-label fw-semibold">Periode Mulai</label>
                <input type="date" class="form-control" name="tanggal_mulai" id="tanggal_mulai" value="{{ request('tanggal_mulai', now()->subDays(6)->toDateString()) }}">
            </div>
            <div class="col-md-1 d-flex align-items-center justify-content-center pt-3">
                <span class="fw-bold">s/d</span>
            </div>
            <div class="col-md-4">
                <label for="tanggal_selesai" class="form-label fw-semibold">Periode Selesai</label>
                <input type="date" class="form-control" name="tanggal_selesai" id="tanggal_selesai" value="{{ request('tanggal_selesai', now()->toDateString()) }}">
            </div>
            <div class="col-md-3 pt-2">
                <button type="submit" class="btn btn-primary w-100 fw-semibold">
                Terapkan
                </button>
            </div>
            </form>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
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
                                <td>{{ optional($log->produk)->nama }}</td>
                                <td><span class="badge bg-info-subtle text-info-emphasis">{{ $log->tipe }}</span></td>
                                <td class="text-end fw-bold {{ $log->jumlah_berubah > 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $log->jumlah_berubah > 0 ? '+' : '' }}{{ $log->jumlah_berubah }}
                                </td>
                                <td class="text-end">{{ $log->stok_sebelum }}</td>
                                <td class="text-end">{{ $log->stok_sesudah }}</td>
                                <td>{{ optional($log->user)->name ?? 'Sistem' }}</td>
                                <td>{{ $log->keterangan }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Belum ada riwayat perubahan stok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</x-layout>