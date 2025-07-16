<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    {{-- Salam Pembuka --}}
    <h3 class="mb-4">Selamat Datang, {{ auth()->user()->name }}!</h3>

    {{-- BARIS 1: RINGKASAN TOTAL PAKET --}}
    <div class="row g-4">
        <div class="col-lg-4 col-md-6">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Perlu Diproses (Total)</h6>
                    <h2 class="card-title text-warning fw-bold">{{ $data['jumlah_proses'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Selesai (Total)</h6>
                    <h2 class="card-title text-success fw-bold">{{ $data['jumlah_selesai'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-6">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Dibatalkan (Total)</h6>
                    <h2 class="card-title text-danger fw-bold">{{ $data['jumlah_dibatalkan'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    <hr class="my-4">

                {{-- Kartu Stok Rendah (Untuk Pegawai & Admin) --}}
                <div class="row">
                    <div class="col-lg col-md-8 mx-auto">
                        <div class="card shadow-sm mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-box-seam me-2"></i>Produk Stok Rendah
                                </h5>
                                <small class="text-muted">
                                    Stok di bawah atau sama dengan batas minimal.
                                </small>
                            </div>
                            <div class="card-body">
                                @if(isset($data['produk_stok_rendah']))
                                    @forelse ($data['produk_stok_rendah'] as $produk)
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <span class="d-block">{{ $produk->nama }}</span>
                                                <small class="text-muted">{{ optional($produk->toko)->name }}</small>
                                            </div>
                                            <span class="badge bg-danger rounded-pill">
                                                {{ $produk->stok }}
                                                <small class="fw-normal">/ {{ $produk->minimal_stok }}</small>
                                            </span>
                                        </div>
                                    @empty
                                        <p class="text-muted text-center my-2">✓ Semua stok aman!</p>
                                    @endforelse
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

    {{-- BARIS 2: KONTEN UTAMA DENGAN DUA KOLOM --}}
    <div class="row g-4">
        {{-- KOLOM KIRI: GRAFIK-GRAFIK UTAMA --}}
        <div class="col-lg-8">
            {{-- Grafik Penjualan 7 Hari --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-graph-up me-2"></i>Item Terjual (7 Hari Terakhir)</h5>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" style="max-height: 320px;"></canvas>
                </div>
            </div>
            {{-- Grafik Stok Masuk vs Keluar --}}
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-arrow-down-up me-2"></i>Stok Masuk vs Keluar (7 Hari Terakhir)</h5>
                </div>
                <div class="card-body">
                    <canvas id="stockMovementChart" style="max-height: 320px;"></canvas>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: KARTU-KARTU INFORMASI --}}
        <div class="col-lg-4">          

            {{-- Kartu khusus untuk Super Admin --}}
            @can('is-super-admin')
                {{-- Ringkasan Penjualan --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-calculator-fill me-2"></i>Ringkasan Penjualan</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="row">
                            <div class="col-6 border-end">
                                <h6 class="card-subtitle mb-2 text-muted">Hari Ini</h6>
                                <h4 class="card-title text-primary fw-bold">{{ $data['penjualan_hari_ini'] }}</h4>
                            </div>
                            <div class="col-6">
                                <h6 class="card-subtitle mb-2 text-muted">Bulan Ini</h6>
                                <h4 class="card-title text-primary fw-bold">{{ $data['penjualan_bulan_ini'] }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Penjualan per Merchant --}}
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-pie-chart-fill me-2"></i>Penjualan per Merchant</h5>
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center">
                        <div style="position: relative; height:250px; width:250px">
                             <canvas id="merchantPieChart"></canvas>
                        </div>
                    </div>
                </div>
            @endcan
        </div>
    </div>

    {{-- Jembatan Data untuk JavaScript (Tidak diubah) --}}
    <div id="page-data" 
         data-sales-chart-labels='@json($data['chartLabels'] ?? [])'
         data-sales-chart-values='@json($data['chartData'] ?? [])'
         data-stock-chart-labels='@json($data['stockChartLabels'] ?? [])'
         data-stock-chart-masuk='@json($data['stockMasukData'] ?? [])'
         data-stock-chart-keluar='@json($data['stockKeluarData'] ?? [])'
         data-merchant-chart-labels='@json($data['merchantLabels'] ?? [])'
         data-merchant-chart-data='@json($data['merchantData'] ?? [])'
         style="display: none;">
    </div>
</x-layout>