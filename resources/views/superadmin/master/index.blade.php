<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="row g-4">
        {{-- Kartu untuk Manajemen Produk --}}
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('produk.index') }}" class="card h-100 shadow-sm text-decoration-none card-link">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title text-primary"><i class="bi bi-boxes me-2"></i>Manajemen Produk</h5>
                    <p class="card-text text-muted small">Kelola semua produk, jenis produk, dan kategori.</p>
                </div>
            </a>
        </div>

        {{-- Kartu untuk Manajemen Toko --}}
        <div class="col-md-6 col-lg-4">
             <a href="{{ route('toko.index') }}" class="card h-100 shadow-sm text-decoration-none card-link">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title text-primary"><i class="bi bi-shop me-2"></i>Manajemen Toko</h5>
                    <p class="card-text text-muted small">Kelola semua data toko atau cabang.</p>
                </div>
            </a>
        </div>


        {{-- Kartu untuk Master Lainnya (Merchant & Ekspedisi) --}}
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('merchant.index') }}" class="card h-100 shadow-sm text-decoration-none card-link">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title text-primary"><i class="bi bi-person-badge me-2"></i>Manajemen Merchant</h5>
                    <p class="card-text text-muted small">Kelola daftar merchant.</p>
                </div>
            </a>
        </div>

        <div class="col-md-6 col-lg-4">
            <a href="{{ route('merchant.index') }}" class="card h-100 shadow-sm text-decoration-none card-link">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title text-primary"><i class="bi bi-person-badge me-2"></i>Manajemen Ekspedisi</h5>
                    <p class="card-text text-muted small">Kelola jasa pengiriman.</p>
                </div>
            </a>
        </div>

                {{-- Kartu untuk Konfigurasi Layanan --}}
        <div class="col-md-6 col-lg-4">
            <a href="{{ route('layanan-pengiriman.index') }}" class="card h-100 shadow-sm text-decoration-none card-link">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title text-primary"><i class="bi bi-diagram-3 me-2"></i>Konfigurasi Layanan</h5>
                    <p class="card-text text-muted small">Atur kombinasi Merchant & Ekspedisi untuk setiap Toko.</p>
                </div>
            </a>
        </div>

    </div>
</x-layout>