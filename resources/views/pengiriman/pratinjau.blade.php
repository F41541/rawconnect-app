<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="container">
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
            @php $firstItem = $group->first(); @endphp

            <div class="card mb-3 shadow-sm rounded-4 border" style="background: #f6f8fa;">
                <div class="card-header d-flex align-items-center gap-2 bg-white rounded-top-4 border-bottom-0">
                    <img src="{{ optional(optional($firstItem)->toko)->logo ? asset('uploads/logo_toko/' . $firstItem->toko->logo) : asset('images/no-image.png') }}" 
                        alt="Logo {{ optional($firstItem->toko)->name }}" 
                        class="rounded-circle border" style="width: 36px; height: 36px; object-fit: contain;">
                    <span class="fw-semibold">{{ optional($firstItem->toko)->name }}</span>
                    <span class="text-muted mx-1">|</span>
                    <span class="fw-semibold">{{ optional($firstItem->merchant)->name }}</span>
                    <span class="text-muted mx-1">|</span>
                    <span class="fw-semibold">{{ optional($firstItem->ekspedisi)->name }}</span>
                </div>

                <div class="card-body p-0">
                    @php
                        $groupedByJenis = $group->groupBy(fn($item) => optional(optional($item->produk)->jenisProduk)->name ?? 'Lain-lain');
                    @endphp

                    <ul class="list-group list-group-flush">
                        @foreach ($groupedByJenis as $jenisNama => $itemsByJenis)
                            <li class="list-group-item px-3 py-2 bg-light-subtle border-0">
                                <strong class="text-dark-emphasis">{{ $jenisNama }}</strong>
                            </li>

                            @php
                                $firstItem = $itemsByJenis->first();
                                $restItems = $itemsByJenis->slice(1);
                                $collapseId = 'group-'.$groupKey.'-'.Str::slug($jenisNama);
                            @endphp

                            {{-- Item pertama --}}
                            @if($firstItem)
                                <li class="list-group-item d-flex justify-content-between align-items-center ps-4 border-0">
                                    <div>
                                        <strong>{{ optional($firstItem->produk)->nama ?? 'Produk Dihapus' }}</strong>
                                        @if($firstItem->deskripsi_varian)
                                            <small class="text-primary fw-bold d-block">{{ $firstItem->deskripsi_varian }}</small>
                                        @endif
                                    </div>
                                    <span class="badge bg-primary rounded-pill shadow-sm">x {{ $firstItem->jumlah }}</span>
                                </li>
                            @endif

                            {{-- Sisanya pakai collapse --}}
                            @if($restItems->isNotEmpty())
                                <li class="list-group-item p-0 border-0">
                                    <div class="collapse" id="{{ $collapseId }}">
                                        <ul class="list-group list-group-flush">
                                            @foreach($restItems as $item)
                                                <li class="list-group-item d-flex justify-content-between align-items-center ps-4 border-0">
                                                    <div>
                                                        <strong>{{ optional($item->produk)->nama ?? 'Produk Dihapus' }}</strong>
                                                        @if($item->deskripsi_varian)
                                                            <small class="text-primary fw-bold d-block">{{ $item->deskripsi_varian }}</small>
                                                        @endif
                                                    </div>
                                                    <span class="badge bg-primary rounded-pill shadow-sm">x {{ $item->jumlah }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </li>
                                <li class="list-group-item ps-4 py-2 border-0">
                                    <a class="btn btn-sm btn-link text-decoration-none p-0 fw-bold collapse-trigger" data-bs-toggle="collapse" href="#{{ $collapseId }}" role="button">
                                        <span class="collapse-text">Lihat {{ $restItems->count() }} item lainnya</span>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>

                <div class="card-footer bg-light rounded-bottom-4 text-muted d-flex justify-content-between align-items-center small">
                    <span>{{ $firstItem->created_at->format('H:i') }} | {{ optional($firstItem->user)->name ?? 'N/A' }}</span>
                </div>
            </div>
        @empty
            <div class="alert alert-info text-center">
                <p class="mb-0">Keranjang pratinjau masih kosong.</p>
                <a href="{{ route('pengiriman.create') }}" class="fw-medium">Tambahkan item baru</a> untuk memulai.
            </div>
        @endforelse
    </div>



    {{-- JavaScript Anda tidak perlu diubah, sudah benar --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Definisikan timer di sini agar bisa diakses oleh semua event listener
                let debounceTimer;

                // Fungsi untuk mengirim update jumlah via AJAX (tidak berubah)
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