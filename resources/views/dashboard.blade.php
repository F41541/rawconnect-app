<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    {{-- RINGKASAN TOTAL PAKET --}}
    <div class="d-flex gap-3 mb-4 flex-wrap">
        <div class="flex-grow-1">
            <div class="dashboard-card card text-center shadow border-0 rounded-4 bg-white h-100">
                <div class="card-body">
                    <h6 class="text-muted">Perlu Diproses</h6>
                    <h2 class="fw-bold text-warning">{{ $data['jumlah_proses'] }}</h2>
                    <a href="{{ route('pengiriman.index', ['status' => 'proses']) }}" class="stretched-link"></a>
                </div>
            </div>
        </div>

        <div class="flex-grow-1">
            <div class="dashboard-card card text-center shadow border-0 rounded-4 bg-white h-100">
                <div class="card-body">
                    <h6 class="text-muted">Selesai</h6>
                    <h2 class="fw-bold text-success">{{ $data['jumlah_selesai'] }}</h2>
                    <a href="{{ route('pengiriman.index', ['status' => 'selesai']) }}" class="stretched-link"></a>
                </div>
            </div>
        </div>

        <div class="flex-grow-1">
            <div class="dashboard-card card text-center shadow border-0 rounded-4 bg-white h-100">
                <div class="card-body">
                    <h6 class="text-muted">Dibatalkan</h6>
                    <h2 class="fw-bold text-danger">{{ $data['jumlah_dibatalkan'] }}</h2>
                    <a href="{{ route('pengiriman.index', ['status' => 'dibatalkan']) }}" class="stretched-link"></a>
                </div>
            </div>
        </div>

        @can('create-shipments')
        <div class="flex-grow-1">
            <a href="{{ route('pengiriman.pratinjau') }}" class="text-decoration-none">
                <div class="dashboard-card card text-center shadow border-0 rounded-4 bg-white h-100">
                    <div class="card-body">
                        <h6 class="text-muted">Di Pratinjau</h6>
                        <h2 class="fw-bolder text-info">{{ $data['jumlah_pratinjau'] }}</h2>
                    </div>
                </div>
            </a>
        </div>
        @endcan
    </div>



    {{-- PRODUK STOK RENDAH --}}
    <div class="card shadow border-0 rounded-4 mb-4">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 fw-bold"><i class="bi bi-box-seam me-2"></i>Produk Stok Rendah</h5>
                <small class="text-muted">Stok di bawah batas minimal.</small>
            </div>
            
            {{-- TOMBOL SORTING BARU --}}
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-sort-down me-2"></i> Urutkan
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item {{ ($data['current_sort'] ?? 'stok_asc') == 'stok_asc' ? 'active' : '' }}" href="{{ route('dashboard', ['sort' => 'stok_asc']) }}">Paling Sedikit</a></li>
                    <li><a class="dropdown-item {{ ($data['current_sort'] ?? '') == 'jenis_produk' ? 'active' : '' }}" href="{{ route('dashboard', ['sort' => 'jenis_produk']) }}">Berdasarkan Jenis</a></li>
                    <li><a class="dropdown-item {{ ($data['current_sort'] ?? '') == 'toko' ? 'active' : '' }}" href="{{ route('dashboard', ['sort' => 'toko']) }}">Berdasarkan Toko</a></li>
                </ul>
            </div>
        </div>
        <div class="card-body rounded-4 p-1">
            {{-- Logika untuk menampilkan 5 item dan collapse (tidak berubah) --}}
            @if(isset($data['produk_stok_rendah']) && $data['produk_stok_rendah']->isNotEmpty())
                <ul class="list-group list-group-flush">
                    @foreach ($data['produk_stok_rendah'] as $produk)
                        @if ($loop->iteration == 6)
                            <div class="collapse" id="lihatStokRendahLainnya">
                        @endif

                        @php
                            // Ambil relasi jenisProduk dengan aman
                            $jenisProduk = $produk->jenisProduk;
                            // Ambil kategori pertama dari jenis produk tersebut, jika ada
                            $kategoriPertama = $jenisProduk ? $jenisProduk->kategoris->first() : null;
                        @endphp
                        <a href="{{ route('stok.show_by_jenis', [
                            'jenisProduk' => $produk->jenis_produk_id,
                            'active_kategori' => $kategoriPertama ? $kategoriPertama->id : ''
                            ]) }}"
                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <div>
                                <span class="d-block fw-semibold">{{ $produk->nama }}</span>
                                <small class="text-muted">{{ optional($produk->toko)->name }} &middot; {{ optional($produk->jenisProduk)->name }}</small>
                            </div>
                            <span class="badge bg-danger-subtle text-danger-emphasis rounded-pill fs-6">
                                {{ $produk->stok }} / <small>{{ $produk->minimal_stok }}</small>
                            </span>
                        </a>
                    @endforeach
                    
                    @if($data['produk_stok_rendah']->count() > 5)
                        </div>
                    @endif
                </ul>
            @else
                <div class="text-center text-muted p-4">
                    <i class="bi bi-check-circle-fill fs-4 text-success"></i>
                    <p class="mb-0 mt-2">Semua stok aman!</p>
                </div>
            @endif
        </div>

        {{-- Tombol 'Lihat Semua' (tidak berubah) --}}
        @if(isset($data['produk_stok_rendah']) && $data['produk_stok_rendah']->count() > 5)
            <div class="card-footer bg-transparent text-center">
                <a class="btn btn-sm btn-link text-decoration-none p-0 collapse-trigger" 
                data-bs-toggle="collapse" 
                href="#lihatStokRendahLainnya" 
                role="button" 
                aria-expanded="false">
                    <span class="collapse-text">Lihat {{ $data['produk_stok_rendah']->count() - 5 }} item lainnya</span>
                    <i class="bi bi-chevron-down small collapse-icon"></i>
                </a>
            </div>
        @endif
    </div>

    @can('is-super-admin')
    {{-- FILTER PERIODE --}}
    <div class="card shadow border-0 rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('dashboard') }}" class="d-flex flex-wrap align-items-end gap-2">
                <div style="flex:1; min-width: 150px;">
                    <label class="form-label fw-semibold">Periode Mulai</label>
                    <input type="date" name="tanggal_mulai" class="form-control" value="{{ request('tanggal_mulai', now()->subDays(6)->toDateString()) }}">
                </div>
                <div class="d-flex align-items-center justify-content-center fw-bold" style="min-width: 40px;">
                    s/d
                </div>
                <div style="flex:1; min-width: 150px;">
                    <label class="form-label fw-semibold">Periode Selesai</label>
                    <input type="date" name="tanggal_selesai" class="form-control" value="{{ request('tanggal_selesai', now()->toDateString()) }}">
                </div>
                <div style="min-width: 120px;">
                    <button type="submit" class="btn btn-primary w-100 fw-semibold mt-3 mt-md-0">Terapkan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow border-0 rounded-4 mb-4">
                <div class="card-header rounded-top-4 bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Item Terjual (7 Hari Terakhir)</h5>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" style="max-height: 320px;"></canvas>
                </div>
            </div>

            <div class="card shadow border-0 rounded-4">
                <div class="card-header rounded-top-4 bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-bar-chart-line-fill me-2"></i>Stok Masuk vs Keluar</h5>
                </div>
                <div class="card-body">
                    <canvas id="stockMovementChart" style="max-height: 320px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow border-0 rounded-4 mb-4">
                <div class="card-header rounded-top-4 bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-cash-coin me-2"></i>Ringkasan Penjualan</h5>
                </div>
                <div class="card-body text-center">
                    <div class="row">
                        <div class="col-6 border-end">
                            <h6 class="text-muted">Hari Ini</h6>
                            <h4 class="fw-bold text-primary">{{ $data['penjualan_hari_ini'] }}</h4>
                        </div>
                        <div class="col-6">
                            <h6 class="text-muted">Bulan Ini</h6>
                            <h4 class="fw-bold text-primary">{{ $data['penjualan_bulan_ini'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow border-0 rounded-4">
                <div class="card-header rounded-top-4 bg-white border-bottom">
                    <h5 class="mb-0"><i class="bi bi-pie-chart-fill me-2"></i>Penjualan per Merchant</h5>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center">
                    <canvas id="merchantPieChart" style="max-height: 250px; max-width: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endcan

    {{-- DATA UNTUK CHART JS --}}
    <div id="page-data"
         data-sales-chart-labels='@json($data['chartLabels'] ?? [])'
         data-sales-chart-values='@json($data['chartData'] ?? [])'
         data-stock-chart-labels='@json($data['stockChartLabels'] ?? [])'
         data-stock-chart-masuk='@json($data['stockMasukData'] ?? [])'
         data-stock-chart-keluar='@json($data['stockKeluarData'] ?? [])'
         data-merchant-chart-labels='@json($data['merchantLabels'] ?? [])'
         data-merchant-chart-data='@json($data['merchantData'] ?? [])'
         hidden>
    </div>

        {{-- CSS Kustom untuk efek hover --}}
    @push('styles')
    <style>
        .dashboard-card {
            transition: all 0.2s ease-in-out;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        }
    </style>
    @endpush

</x-layout>
