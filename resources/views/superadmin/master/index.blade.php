<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="container py-1">
        <div class="row g-4">

            @php
                $cards = [
                    [
                        'route' => 'superadmin.kategori.index',
                        'icon' => 'bi-tags',
                        'title' => 'Manajemen Kategori',
                        'desc' => 'Kelola semua kategori.',
                    ],
                    [
                        'route' => 'superadmin.jenis-produk.index',
                        'icon' => 'bi-box',
                        'title' => 'Manajemen Jenis Produk',
                        'desc' => 'Kelola semua jenis produk.',
                    ],
                    [
                        'route' => 'superadmin.toko.index',
                        'icon' => 'bi-shop',
                        'title' => 'Manajemen Toko',
                        'desc' => 'Kelola semua data toko atau cabang.',
                    ],
                    [
                        'route' => 'superadmin.produk.index',
                        'icon' => 'bi-box-seam',
                        'title' => 'Manajemen Produk',
                        'desc' => 'Kelola semua produk.',
                    ],
                    [
                        'route' => 'superadmin.merchant.index',
                        'icon' => 'bi-person-badge',
                        'title' => 'Manajemen Merchant',
                        'desc' => 'Kelola daftar merchant.',
                    ],
                    [
                        'route' => 'superadmin.ekspedisi.index',
                        'icon' => 'bi-truck',
                        'title' => 'Manajemen Ekspedisi',
                        'desc' => 'Kelola jasa pengiriman.',
                    ],
                    [
                        'route' => 'superadmin.layanan-pengiriman.index',
                        'icon' => 'bi-diagram-3',
                        'title' => 'Konfigurasi Layanan',
                        'desc' => 'Atur kombinasi Merchant & Ekspedisi untuk setiap Toko.',
                    ],
                ];
            @endphp

            @foreach ($cards as $card)
                <div class="col-md-6 col-lg-4">
                    <a href="{{ route($card['route']) }}" class="text-decoration-none">
                        <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                            <div class="card-body d-flex flex-column justify-content-center">
                                <div class="mb-3">
                                    <div class="bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 48px; height: 48px;">
                                        <i class="bi {{ $card['icon'] }} fs-5"></i>
                                    </div>
                                </div>
                                <h5 class="card-title fw-semibold text-dark">{{ $card['title'] }}</h5>
                                <p class="card-text text-muted small">{{ $card['desc'] }}</p>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach

        </div>
    </div>

    @push ('styles')
        <style>
            .hover-shadow:hover {
                box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.1) !important;
                transform: translateY(-2px);
            }
            .transition-all {
                transition: all 0.2s ease-in-out;
            }
        </style>
    @endpush

    @push ('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const cards = document.querySelectorAll('.card');
                cards.forEach(card => {
                    card.addEventListener('mouseenter', () => {
                        card.classList.add('shadow-lg');
                    });
                    card.addEventListener('mouseleave', () => {
                        card.classList.remove('shadow-lg');
                    });
                });
            });
        </script>
    @endpush
</x-layout>
