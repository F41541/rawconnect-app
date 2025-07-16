<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="container py-4">
        {{-- Tombol Tambah (FAB) dengan Gate yang benar --}}
        @can('create-shipments')
            <a href="{{ route('pengiriman.create') }}" 
               class="btn btn-primary position-fixed" 
               style="bottom: 32px; right: 32px; z-index: 1029; border-radius: 50%; width: 56px; height: 56px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
                <i class="bi bi-plus-circle" style="font-size: 1.8rem;"></i>
            </a>
        @endcan

        <div class="nav-wrapper" style="overflow-x: auto;">
            <ul class="nav nav-tabs nav-fill" id="pengirimanTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active text-nowrap" id="proses-tab" data-bs-toggle="tab" data-bs-target="#proses" type="button" role="tab">
                        Perlu Diproses <span class="badge bg-warning ms-1">{{ $paket_proses->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link text-nowrap" id="selesai-tab" data-bs-toggle="tab" data-bs-target="#selesai" type="button" role="tab">
                        Selesai <span class="badge bg-success ms-1">{{ $paket_selesai->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link text-nowrap" id="dibatalkan-tab" data-bs-toggle="tab" data-bs-target="#dibatalkan" type="button" role="tab">
                        Dibatalkan <span class="badge bg-danger ms-1">{{ $paket_dibatalkan->count() }}</span>
                    </button>
                </li>
            </ul>
        </div>

        <div class="tab-content mt-3" id="pengirimanTabContent">
            {{-- TAB UNTUK STATUS 'PROSES' --}}
            <div class="tab-pane fade show active" id="proses" role="tabpanel">
                @include('pengiriman.partials.paket-list', ['pakets' => $paket_proses])
                    <div class="d-flex justify-content-center mt-3">
                        {{ $paket_proses->links() }}
                    </div>
            </div>

            {{-- TAB UNTUK STATUS 'SELESAI' --}}
            <div class="tab-pane fade" id="selesai" role="tabpanel">
                @include('pengiriman.partials.paket-list', ['pakets' => $paket_selesai])
                    <div class="d-flex justify-content-center mt-3">
                        {{ $paket_proses->links() }}
                    </div>
            </div>

            {{-- TAB UNTUK STATUS 'DIBATALKAN' --}}
            <div class="tab-pane fade" id="dibatalkan" role="tabpanel">
                @include('pengiriman.partials.paket-list', ['pakets' => $paket_dibatalkan])
                    <div class="d-flex justify-content-center mt-3">
                        {{ $paket_proses->links() }}
                    </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            
            // Skrip untuk auto-open tab dari URL (sudah benar)
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            if (status) {
                const tabToActivate = document.querySelector('#' + status + '-tab');
                if (tabToActivate) {
                    // Gunakan Bootstrap 5's Tab constructor untuk mengaktifkan tab
                    const tab = new bootstrap.Tab(tabToActivate);
                    tab.show();
                }
            }

            // PERBAIKAN: Skrip untuk mengubah teks & ikon tombol 'Lihat Detail'
            const collapseTriggers = document.querySelectorAll('.collapse-trigger');
            collapseTriggers.forEach(trigger => {
                const targetId = trigger.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                const textElement = trigger.querySelector('.collapse-text');
                const iconElement = trigger.querySelector('.collapse-icon'); // Tambahkan pencarian ikon

                if (targetElement && textElement && iconElement) {
                    const originalText = textElement.textContent.trim();

                    // Saat collapse mulai DITAMPILKAN
                    targetElement.addEventListener('show.bs.collapse', event => {
                        textElement.textContent = 'Lihat lebih sedikit';
                        iconElement.classList.add('rotated'); // Tambahkan kelas untuk memutar
                    });

                    // Saat collapse mulai DI SEMBUNYIKAN
                    targetElement.addEventListener('hide.bs.collapse', event => {
                        textElement.textContent = originalText;
                        iconElement.classList.remove('rotated'); // Hapus kelas untuk mengembalikan
                    });
                }
            });
        });
    </script>
    @endpush
</x-layout>