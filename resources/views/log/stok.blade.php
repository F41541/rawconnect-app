<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="container py-1">
<div class="card shadow border-0 rounded-4 mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('log.stok') }}">
            {{-- BARIS 1: PERIODE TANGGAL --}}
            <div class="row gx-3 gy-2 align-items-end mb-1">
                <div class="col-md-5">
                    <label for="tanggal_mulai" class="form-label fw-semibold text-muted small mb-1">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" id="tanggal_mulai"
                        class="form-control form-control-sm rounded-3 shadow-sm"
                        value="{{ request('tanggal_mulai', now()->subDays(6)->toDateString()) }}">
                </div>
                <div class="col-sm-2 d-flex align-items-center justify-content-center fw-semibold text-muted small">
                    s/d
                </div>
                <div class="col-md-5">
                    <label for="tanggal_selesai" class="form-label fw-semibold text-muted small mb-1">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" id="tanggal_selesai"
                        class="form-control form-control-sm rounded-3 shadow-sm"
                        value="{{ request('tanggal_selesai', now()->toDateString()) }}">
                </div>
            </div>

            {{-- BARIS 2: JENIS PRODUK & TOMBOL --}}
            <div class="row gx-3 gy-2 align-items-end">
                <div class="col-md-6">
                    <label for="jenis_produk_id" class="form-label fw-semibold text-muted small mb-1">Jenis Produk</label>
                    <select name="jenis_produk_id" id="jenis_produk_id"
                        class="form-select form-select-sm rounded-3 shadow-sm">
                        <option value="">Semua Jenis Produk</option>
                        @foreach($jenisProduks as $jenis)
                            <option value="{{ $jenis->id }}" @selected(request('jenis_produk_id') == $jenis->id)>
                                {{ $jenis->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-6">
                    <label class="form-label invisible">Terapkan</label>
                    <button type="submit" class="btn btn-sm btn-primary w-100 rounded-3 fw-semibold shadow-sm">
                        Terapkan
                    </button>
                </div>
                <div class="col-md-3 col-6">
                    <label class="form-label invisible">Reset</label>
                    <a href="{{ route('log.stok') }}" class="btn btn-sm btn-outline-secondary w-100 rounded-3 shadow-sm">
                        Reset
                    </a>
                </div>
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
                                <th>Satuan</th>
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
                                    <td>{{ optional($log->produk)->satuan ?? '-' }}</td>   
                                    <td>
                                        <span class="badge bg-{{ $log->tipe === 'masuk' ? 'success' : 'warning' }}-subtle text-{{ $log->tipe === 'masuk' ? 'success' : 'warning' }}-emphasis">
                                            {{ ucfirst($log->tipe) }}
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold {{ $log->jumlah_berubah > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $log->jumlah_berubah > 0 ? '+' : '' }}{{ $log->jumlah_berubah }}
                                    </td>
                                    <td class="text-end">{{ number_format($log->stok_sebelum, 0, ',', '.') }}</td>
                                    <td class="text-end">{{ number_format($log->stok_sesudah, 0, ',', '.') }}</td>
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
