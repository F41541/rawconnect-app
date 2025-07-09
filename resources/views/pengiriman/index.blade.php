<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>
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

        @can ('is-admin-or-super-admin')
        <a href="{{ route('pengiriman.create') }}" 
           class="btn btn-primary position-fixed" 
           style="bottom: 32px; right: 32px; z-index: 1029; border-radius: 50%; width: 56px; height: 56px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
            <i class="bi bi-plus-circle" style="font-size: 1.8rem;"></i>
        </a>
        @endcan

        <!-- Navigasi Tab -->

        <!-- Konten Tab -->
        <div class="tab-content" id="pengirimanTabContent">
            {{-- TAB UNTUK STATUS 'PROSES' --}}
            <div class="tab-pane fade show active" id="proses" role="tabpanel">
                @include('pengiriman.partials.paket-list', ['pakets' => $paket_proses])
            </div>

            {{-- TAB UNTUK STATUS 'SELESAI' --}}
            <div class="tab-pane fade" id="selesai" role="tabpanel">
                @include('pengiriman.partials.paket-list', ['pakets' => $paket_selesai])
            </div>

            {{-- TAB UNTUK STATUS 'DIBATALKAN' --}}
            <div class="tab-pane fade" id="dibatalkan" role="tabpanel">
                @include('pengiriman.partials.paket-list', ['pakets' => $paket_dibatalkan])
            </div>
        </div>


    @push('scripts')
    <script>
        // Jalankan skrip setelah semua halaman dimuat
        document.addEventListener('DOMContentLoaded', function () {
            
            // 1. Ambil parameter dari URL
            const urlParams = new URLSearchParams(window.location.search);
            
            // 2. Cari parameter yang bernama 'status'
            const status = urlParams.get('status');

            // 3. Jika parameter 'status' ada di URL (misal: 'selesai' atau 'dibatalkan')
            if (status) {
                
                // 4. Cari tombol tab yang sesuai. Contoh: jika status='selesai', cari tombol dengan id 'selesai-tab'
                const tabToActivate = document.querySelector('#' + status + '-tab');

                // 5. Jika tombolnya ditemukan, picu klik pada tombol tersebut
                if (tabToActivate) {
                    tabToActivate.click();
                }
            }
        });
            // Skrip baru untuk mengubah teks & ikon tombol 'Lihat Detail'
    const collapseTriggers = document.querySelectorAll('.collapse-trigger');
    
    collapseTriggers.forEach(trigger => {
        const targetId = trigger.getAttribute('href');
        const targetElement = document.querySelector(targetId);
        const textElement = trigger.querySelector('.collapse-text');

        if (targetElement && textElement) {
            const originalText = textElement.textContent.trim();

            // Saat collapse mulai DITAMPILKAN
            targetElement.addEventListener('show.bs.collapse', event => {
                textElement.textContent = 'Lihat lebih sedikit';
            });

            // Saat collapse mulai DI SEMBUNYIKAN
            targetElement.addEventListener('hide.bs.collapse', event => {
                textElement.textContent = originalText;
            });
        }
    });
    </script>
    @endpush
</x-layout>
