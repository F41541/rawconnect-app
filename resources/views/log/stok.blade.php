<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Riwayat Perubahan Stok</h5>
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