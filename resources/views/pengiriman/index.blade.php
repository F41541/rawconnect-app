<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>
    <div class="container">
        {{-- Tombol Tambah (FAB) dengan Gate yang benar --}}
        @can('create-shipments')
            <a href="{{ route('pengiriman.create') }}" 
               class="btn btn-light position-fixed" 
               style="bottom: 32px; right: 32px; z-index: 1029; border-radius: 50%; width: 56px; height: 56px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.46);">
                <i class="bi bi-plus" style="font-size: 3rem; color: blue;"></i>
            </a>
        @endcan

    <div class="nav-wrapper border-bottom mb-3" style="overflow-x: auto;">
        <ul class="nav nav-tabs nav-fill flex-nowrap gap-2 px-2" id="pengirimanTab" role="tablist" style="min-width: max-content;">
            <li class="nav-item" role="presentation">
                <button class="nav-link active px-3 py-2 d-flex align-items-center justify-content-between text-nowrap w-100" 
                    id="proses-tab" data-bs-toggle="tab" data-bs-target="#proses" type="button" role="tab">
                    <span class="d-flex align-items-center gap-2">Perlu Diproses</span>
                    <span class="badge bg-warning text-dark fs-6">{{ $paket_proses->total() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link px-3 py-2 d-flex align-items-center justify-content-between text-nowrap w-100" 
                    id="selesai-tab" data-bs-toggle="tab" data-bs-target="#selesai" type="button" role="tab">
                    <span class="d-flex align-items-center gap-2">Selesai</span>
                    <span class="badge bg-success fs-6">{{ $paket_selesai->total() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link px-3 py-2 d-flex align-items-center justify-content-between text-nowrap w-100" 
                    id="dibatalkan-tab" data-bs-toggle="tab" data-bs-target="#dibatalkan" type="button" role="tab">
                    <span class="d-flex align-items-center gap-2">Dibatalkan</span>
                    <span class="badge bg-danger fs-6">{{ $paket_dibatalkan->total() }}</span>
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
                        {{ $paket_selesai->links() }}
                    </div>
            </div>

            {{-- TAB UNTUK STATUS 'DIBATALKAN' --}}
            <div class="tab-pane fade" id="dibatalkan" role="tabpanel">
                @include('pengiriman.partials.paket-list', ['pakets' => $paket_dibatalkan])
                    <div class="d-flex justify-content-center mt-3">
                        {{ $paket_dibatalkan->links() }}
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

            const tabButtons = document.querySelectorAll('#pengirimanTab button[data-bs-toggle="tab"]');
            tabButtons.forEach(tabButton => {
                // 'shown.bs.tab' adalah event dari Bootstrap yang berjalan SETELAH tab baru ditampilkan
                tabButton.addEventListener('shown.bs.tab', event => {
                    // Ambil status dari target (misal: '#selesai' -> 'selesai')
                    const newStatus = event.target.getAttribute('data-bs-target').substring(1);
                    
                    // Buat objek URL baru
                    const newUrl = new URL(window.location.href);
                    newUrl.searchParams.set('status', newStatus);

                    // Ganti URL di address bar tanpa reload halaman
                    // Ini akan mengubah URL menjadi misal: /pengiriman?status=selesai
                    history.pushState({}, '', newUrl);
                });
            });

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