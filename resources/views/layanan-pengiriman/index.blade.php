<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="container py-1">
        {{-- Tombol Aksi Atas --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('superadmin.master.index') }}" class="btn btn-light d-flex align-items-center border shadow-sm gap-2">
                <i class="bi bi-arrow-left me-2"></i> Kembali
            </a>
            <a href="{{ route('superadmin.layanan-pengiriman.create') }}" class="btn btn-primary shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle me-2"></i> Tambah Layanan
            </a>
        </div>

        {{-- Daftar Group Toko --}}
        @forelse ($groupedLayanan as $namaToko => $byToko)
            <div class="card shadow mb-4 border-0">
                <div class="card-header bg-light fw-semibold fs-5 border-bottom">
                    {{ $namaToko }}
                </div>
                <ul class="list-group list-group-flush">
                    @foreach ($byToko as $namaMerchant => $byMerchant)
                        <li class="list-group-item d-flex justify-content-between align-items-start flex-column flex-md-row gap-2">
                            <div class="fw-medium text-dark mb-2 mb-md-0">{{ $namaMerchant }}</div>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($byMerchant as $layanan)
                                    <div class="d-flex align-items-center gap-1 bg-body-secondary px-3 py-1 rounded-pill border border-light shadow-sm">
                                        <span class="fw-semibold small">{{ $layanan->ekspedisi->name }}</span>
                                        <form action="{{ route('superadmin.layanan-pengiriman.destroy', $layanan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus layanan {{ $layanan->ekspedisi->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-close btn-close-sm ms-1" aria-label="Close" style="filter: brightness(0.5);"></button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @empty
            <div class="alert alert-info text-center shadow-sm">
                Belum ada layanan pengiriman yang dikonfigurasi.
            </div>
        @endforelse
    </div>
</x-layout>
