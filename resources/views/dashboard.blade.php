<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    {{-- Salam Pembuka --}}
    <h3 class="mb-4">Selamat Datang, {{ auth()->user()->name }}!</h3>

    {{-- Bagian 1: Ringkasan Total Paket (Untuk Semua Peran) --}}
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Perlu Diproses (Total)</h6>
                    {{-- PERBAIKAN: Menggunakan nama variabel yang benar --}}
                    <h2 class="card-title text-warning fw-bold">{{ $data['jumlah_proses'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Selesai (Total)</h6>
                    {{-- PERBAIKAN: Menggunakan nama variabel yang benar --}}
                    <h2 class="card-title text-success fw-bold">{{ $data['jumlah_selesai'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">Dibatalkan (Total)</h6>
                    {{-- PERBAIKAN: Menggunakan nama variabel yang benar --}}
                    <h2 class="card-title text-danger fw-bold">{{ $data['jumlah_dibatalkan'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    <hr class="my-4">

    {{-- Bagian 2: Grafik Penjualan (Untuk Semua Peran) --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0"><i class="bi bi-graph-up me-2"></i>Item Terjual (7 Hari Terakhir)</h5>
        </div>
        <div class="card-body">
            <canvas id="salesChart" style="max-height: 300px;"></canvas>
        </div>
    </div>


    {{-- Bagian 3: Kartu Spesifik Berdasarkan Peran --}}
    <div class="row g-4">
        {{-- Kartu ini hanya untuk Peran Operasional (Pegawai & Admin) --}}
        @can('adjust-stock')
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-box-seam me-2"></i>Produk Stok Rendah</h5>
                        <small class="text-muted">Menampilkan hingga 5 produk dengan stok 10 atau kurang.</small>
                    </div>
                    <div class="card-body">
                        @if(isset($data['produk_stok_rendah']))
                            @forelse ($data['produk_stok_rendah'] as $produk)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>{{ $produk->nama }}</span>
                                    <span class="badge bg-danger rounded-pill">{{ $produk->stok }} {{ $produk->satuan }}</span>
                                </div>
                            @empty
                                <p class="text-muted text-center my-4">✓ Semua stok aman!</p>
                            @endforelse
                        @endif
                    </div>
                </div>
            </div>
        @endcan

        {{-- Kartu ini hanya untuk Super Admin --}}
        @can('is-super-admin')
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-calculator-fill me-2"></i>Ringkasan Penjualan</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="row">
                            <div class="col-6 border-end">
                                <h6 class="card-subtitle mb-2 text-muted">Item Terjual Hari Ini</h6>
                                <h2 class="card-title text-primary fw-bold">{{ $data['penjualan_hari_ini'] }}</h2>
                            </div>
                            <div class="col-6">
                                <h6 class="card-subtitle mb-2 text-muted">Item Terjual Bulan Ini</h6>
                                <h2 class="card-title text-primary fw-bold">{{ $data['penjualan_bulan_ini'] }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-arrow-down-up me-2"></i>Stok Masuk vs Keluar (7 Hari Terakhir)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="stockMovementChart" style="max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>
            {{-- KARTU BARU: Grafik Penjualan per Merchant --}}
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-pie-chart-fill me-2"></i>Penjualan per Merchant</h5>
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center">
                        {{-- Kita batasi tinggi kanvasnya agar proporsional --}}
                        <div style="position: relative; height:300px; width:300px">
                            <canvas id="merchantPieChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    </div>

    <div id="page-data" 
        {{-- Data Notifikasi --}}
        data-session-status="{{ session('success') ? 'success' : (session('error') ? 'error' : '') }}"
        data-session-message="{{ session('success') ?? session('error') ?? '' }}"

        {{-- Data Grafik Penjualan --}}
        data-sales-chart-labels='@json($data['chartLabels'] ?? [])'
        data-sales-chart-values='@json($data['chartData'] ?? [])'

        {{-- Data Grafik Stok Masuk vs Keluar --}}
        data-stock-chart-labels='@json($data['stockChartLabels'] ?? [])'
        data-stock-chart-masuk='@json($data['stockMasukData'] ?? [])'
        data-stock-chart-keluar='@json($data['stockKeluarData'] ?? [])'

        {{-- TAMBAHKAN DATA BARU INI --}}
        data-merchant-chart-labels='@json($data['merchantLabels'] ?? [])'
        data-merchant-chart-data='@json($data['merchantData'] ?? [])'

        style="display: none;">
    </div>
</x-layout>