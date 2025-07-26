<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="container py-1">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('pengiriman.create') }}" class="btn btn-outline-secondary" title="Kembali">
                <i class="bi bi-arrow-left"></i>
            </a>

            @if($groupedItems->isNotEmpty())
                <form id="prosesForm" action="{{ route('pengiriman.proses') }}" method="POST" class="d-inline" onsubmit="return confirm('Anda yakin ingin memproses semua item ini?')">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-all me-2"></i>Proses Semua
                    </button>
                </form>
            @endif
        </div>

        @forelse ($groupedItems as $groupKey => $group)
            @php $firstItemInGroup = $group->first(); @endphp

            <div class="card mb-3 shadow-sm rounded-4 border">
                <div class="card-header d-flex align-items-center gap-1 bg-white rounded-top-4 border-bottom-0 ">
                    <img src="{{ optional(optional($firstItemInGroup)->toko)->logo ? asset('uploads/logo_toko/' . $firstItemInGroup->toko->logo) : asset('images/no-image.png') }}" 
                        alt="Logo {{ optional($firstItemInGroup->toko)->name }}" 
                        class="rounded-circle border" style="width: 36px; height: 36px; object-fit: contain;">
                    <span class="fw-semibold">{{ optional($firstItemInGroup->toko)->name }}</span>
                    <span class="text-muted mx-1">|</span>
                    <span class="fw-semibold">{{ optional($firstItemInGroup->merchant)->name }}</span>
                    <span class="text-muted mx-1">|</span>
                    <span class="fw-semibold">{{ optional($firstItemInGroup->ekspedisi)->name }}</span>
                </div>

                <ul class="list-group list-group-flush">
                    @php
                        $groupedByJenis = $group->groupBy(fn($item) => optional(optional($item->produk)->jenisProduk)->name ?? 'Lain-lain');
                    @endphp

                    @foreach ($groupedByJenis as $jenisNama => $itemsByJenis)
                        <li class="list-group-item px-3 py-2 bg-light">
                            <strong class="text-dark-emphasis">{{ $jenisNama }}</strong>
                        </li>

                        @foreach($itemsByJenis as $item)
                            {{-- PERBAIKAN: Tata letak item dengan input jumlah & tombol hapus --}}
                            <li class="list-group-item ps-4">
                                <div style="display: grid; grid-template-columns: 1fr auto auto; align-items: center; gap: 1rem;">
                                    {{-- Kolom 1: Nama Produk & Varian --}}
                                    <div>
                                        <strong>{{ optional($item->produk)->nama ?? 'Produk Dihapus' }}</strong>
                                        @if($item->deskripsi_varian)
                                            <small class="text-primary fw-bold d-block">{{ $item->deskripsi_varian }}</small>
                                        @endif
                                    </div>

                                    {{-- Kolom 2: Input Jumlah Interaktif --}}
                                    <div class="d-flex align-items-center">
                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-update-jumlah" data-item-id="{{ $item->id }}" data-step="-1">-</button>
                                        <input type="number" value="{{ $item->jumlah }}" class="form-control form-control-sm text-center mx-2 jumlah-input" data-item-id="{{ $item->id }}" min="1" style="width: 70px;">
                                        <button type="button" class="btn btn-sm btn-outline-secondary btn-update-jumlah" data-item-id="{{ $item->id }}" data-step="1">+</button>
                                    </div>
                                    
                                    {{-- Kolom 3: Tombol Hapus --}}
                                    <form action="{{ route('pengiriman.hapus', $item->id) }}" method="POST" onsubmit="return confirm('Hapus item ini dari pratinjau?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm text-danger" title="Hapus Item"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    @endforeach
                </ul>

                <div class="card-footer bg-white rounded-bottom-4 text-muted d-flex justify-content-between align-items-center small">
                    <span>Dibuat Oleh: {{ optional($firstItemInGroup->user)->name ?? 'N/A' }}</span>
                    <span>{{ optional ($firstItemInGroup->created_at)->format('d M Y, H:i') }}</span>
                </div>
            </div>
        @empty
            <div class="alert alert-info text-center">
                <p class="mb-0">Keranjang pratinjau masih kosong.</p>
                <a href="{{ route('pengiriman.create') }}" class="fw-medium">Tambahkan item baru</a> untuk memulai.
            </div>
        @endforelse
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                let debounceTimer;

                function updateJumlah(itemId, newJumlah) {
                    if (newJumlah < 1) return;
                    const url = `/pengiriman/pratinjau/update-jumlah/${itemId}`;
                    fetch(url, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ jumlah: newJumlah })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            toastr.success(data.message || 'Jumlah diperbarui!');
                        } else {
                            toastr.error(data.message || 'Gagal memperbarui jumlah.');
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }

                // ===================================================================
                // == EVENT LISTENER UNTUK TOMBOL +/- (INI BAGIAN YANG DIUBAH) ==
                // ===================================================================
                document.querySelectorAll('.btn-update-jumlah').forEach(button => {
                    button.addEventListener('click', function () {
                        const itemId = this.dataset.itemId;
                        const step = parseInt(this.dataset.step, 10);
                        const input = document.querySelector(`.jumlah-input[data-item-id="${itemId}"]`);
                        let currentValue = parseInt(input.value, 10);
                        
                        let newValue = currentValue + step;
                        
                        // Batasi nilai minimal 1
                        if (newValue < 1) {
                            newValue = 1;
                        }
                        
                        // 1. Update tampilan di layar secara instan untuk feedback cepat
                        input.value = newValue;

                        // 2. Terapkan debounce: Hapus timer lama, buat timer baru
                        clearTimeout(debounceTimer);
                        debounceTimer = setTimeout(() => {
                            // Kirim update ke server setelah jeda 500ms
                            updateJumlah(itemId, newValue);
                        }, 500); // Jeda setengah detik
                    });
                });

                // Event listener untuk input manual (tidak berubah, sudah menggunakan debounce)
                document.querySelectorAll('.jumlah-input').forEach(input => {
                    input.addEventListener('input', function () {
                        clearTimeout(debounceTimer);
                        debounceTimer = setTimeout(() => {
                            const itemId = this.dataset.itemId;
                            const newValue = parseInt(this.value, 10);
                            // Hanya kirim jika nilainya valid
                            if (newValue >= 1) {
                                updateJumlah(itemId, newValue);
                            }
                        }, 750);
                    });
                });


                const prosesForm = document.getElementById('prosesForm');
                if (prosesForm) {
                    prosesForm.addEventListener('submit', function(event) {
                        // Hentikan sementara pengiriman form asli
                        event.preventDefault();

                        // Siapkan array untuk menampung data terbaru
                        const updates = [];
                        const jumlahInputs = document.querySelectorAll('.jumlah-input');

                        // Kumpulkan semua ID item dan jumlah terbarunya dari layar
                        jumlahInputs.forEach(input => {
                            updates.push({
                                id: input.dataset.itemId,
                                jumlah: input.value
                            });
                        });

                        // Buat input tersembunyi untuk membawa data array ini
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'updates';
                        hiddenInput.value = JSON.stringify(updates);

                        // Tambahkan input tersembunyi ke dalam form
                        this.appendChild(hiddenInput);

                        // Sekarang, lanjutkan pengiriman form
                        this.submit();
                    });
                }
            });
        </script>
    @endpush
</x-layout>