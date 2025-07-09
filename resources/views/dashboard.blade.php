<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="container py-4">
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('pengiriman.index') }}" class="card card-link h-100 text-decoration-none text-dark shadow-sm">
                    <div class="card-body position-relative">
                        <h5 class="text-warning d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-hourglass-split"></i> Perlu Diproses
                        </h5>
                        <p class="fs-4 fw-bold text-warning">{{ $jumlah_perlu }} Paket</p>
                    </div>
                </a>
            </div>

            <div class="col-md-6 col-lg-4">
                <a href="{{ route('pengiriman.index', ['status' => 'selesai']) }}" class="card card-link h-100 text-decoration-none text-dark shadow-sm">
                    <div class="card-body position-relative">
                        <h5 class="text-success d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-check-circle-fill"></i> Selesai
                        </h5>
                        <p class="fs-4 fw-bold text-success">{{ $jumlah_selesai }} Paket</p>
                    </div>
                </a>
            </div>

            <div class="col-md-6 col-lg-4">
                <a href="{{ route('pengiriman.index', ['status' => 'dibatalkan']) }}" class="card card-link h-100 text-decoration-none text-dark shadow-sm">
                    <div class="card-body position-relative">
                        <h5 class="text-danger d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-x-circle-fill"></i> Dibatalkan
                        </h5>
                        <p class="fs-4 fw-bold text-danger">{{ $jumlah_dibatalkan }} Paket</p>
                    </div>
                </a>
            </div>

            @can('is-admin-or-super-admin')
            <div class="col-md-6 col-lg-4">
                <a href="{{ route('pengiriman.pratinjau') }}" class="card card-link h-100 text-decoration-none text-dark shadow-sm">
                    <div class="card-body position-relative">
                        <h5 class="text-primary d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-cart-fill"></i> Keranjang Pratinjau
                        </h5>
                        <p class="fs-4 fw-bold text-primary">{{ $jumlah_keranjang }} Item</p>
                    </div>
                </a>
            </div>
            @endcan
        </div>
    </div>
</x-layout>