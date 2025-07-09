<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Manajemen Layanan Pengiriman</h2>
            <a href="{{ route('layanan-pengiriman.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Tambah Layanan
            </a>
        </div>
        @forelse ($groupedLayanan as $namaToko => $byToko)
            <div class="card mb-4">
                <div class="card-header fw-bold fs-5">
                    {{-- Loop Terluar: Menampilkan Nama Toko --}}
                    {{ $namaToko }}
                </div>
                <ul class="list-group list-group-flush">
                    @foreach ($byToko as $namaMerchant => $byMerchant)
                        {{-- Loop Tengah: Menampilkan Nama Merchant --}}
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="fw-medium">{{ $namaMerchant }}</span>
                            
                            {{-- Loop Terdalam: Menampilkan Ekspedisi sebagai badge --}}
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($byMerchant as $layanan)
                                    <div class="d-inline-flex align-items-center gap-1 border rounded-pill px-2 py-1 bg-light">
                                        <span class="fw-bold" style="font-size: 0.85rem;">{{ $layanan->ekspedisi->name }}</span>
                                        {{-- Form hapus untuk setiap ekspedisi individu --}}
                                        <form action="{{ route('layanan-pengiriman.destroy', $layanan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus layanan {{ $layanan->ekspedisi->name }} dari merchant ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-close" aria-label="Close" style="width: 0.5em; height: 0.5em;"></button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @empty
            <div class="alert alert-info text-center">
                Belum ada layanan pengiriman yang dikonfigurasi.
            </div>
        @endforelse
    </div>
</x-layout>
