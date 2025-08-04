<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="bg-light-subtle p-3 rounded-4 shadow-sm mb-4">

        <div class="d-flex align-items-center mb-1">
            <h2 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 1.4rem;">
                <span class="border-start border-4 border-primary ps-3">
                    <i class="bi bi-truck text-primary me-2"></i> PENGIRIMAN PESANAN
                </span>
            </h2>
        </div>
        <hr class="mt-n2 mb-4 border-primary opacity-25">

        <div class="d-flex gap-3 mb-4 flex-wrap">
            <div class="flex-grow-1">
                <div class="dashboard-card card text-center shadow-sm border-0 rounded-4 bg-white h-100">
                    <div class="card-body">
                        <h6 class="text-muted">PERLU DIPROSES</h6>
                        <h2 class="fw-bold text-warning">{{ $data['jumlah_proses'] }}</h2>
                        <a href="{{ route('pengiriman.index', ['status' => 'proses']) }}" class="stretched-link"></a>
                    </div>
                </div>
            </div>

            <div class="flex-grow-1">
                <div class="dashboard-card card text-center shadow-sm border-0 rounded-4 bg-white h-100">
                    <div class="card-body">
                        <h6 class="text-muted">SELESAI</h6>
                        <h2 class="fw-bold text-success">{{ $data['jumlah_selesai'] }}</h2>
                        <a href="{{ route('pengiriman.index', ['status' => 'selesai']) }}" class="stretched-link"></a>
                    </div>
                </div>
            </div>

            <div class="flex-grow-1">
                <div class="dashboard-card card text-center shadow-sm border-0 rounded-4 bg-white h-100">
                    <div class="card-body">
                        <h6 class="text-muted">DIBATALKAN</h6>
                        <h2 class="fw-bold text-danger">{{ $data['jumlah_dibatalkan'] }}</h2>
                        <a href="{{ route('pengiriman.index', ['status' => 'dibatalkan']) }}" class="stretched-link"></a>
                    </div>
                </div>
            </div>

            @can('create-shipments')
            <div class="flex-grow-1">
                <a href="{{ route('pengiriman.pratinjau') }}" class="text-decoration-none">
                    <div class="dashboard-card card text-center shadow-sm border-0 rounded-4 bg-white h-100">
                        <div class="card-body">
                            <h6 class="text-muted">DI PRATINJAU</h6>
                            <h2 class="fw-bolder text-info">{{ $data['jumlah_pratinjau'] }}</h2>
                        </div>
                    </div>
                </a>
            </div>
            @endcan
        </div>
    </div>

    <div class="bg-light-subtle p-3 rounded-4 shadow-sm mb-4">
        {{-- BAGIAN HEADER UTAMA --}}
        <div class="d-flex align-items-center mb-3">
            <h2 class="mb-0 fw-semibold text-dark d-flex align-items-center gap-2" style="font-size: 1.4rem;">
                <span class="border-start border-4 border-success ps-3 d-flex align-items-center gap-2">
                    <i class="bi bi-box-seam text-success me-2"></i> INFORMASI STOK
                </span>
            </h2>
        </div>
        <hr class="mt-0 mb-3 border-success opacity-25">

        {{-- KARTU PRODUK STOK RENDAH --}}
        <div class="card border-0 bg-transparent justify-content-center d-flex">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center px-0">
                <div>
                    <h5 class="mb-0 fw-semibold"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Produk Stok Rendah</h5>
                    <small class="text-muted">Menampilkan 5 produk paling kritis.</small> 
                </div>
                {{-- Tombol sorting tidak kita perlukan di sini lagi karena daftar lengkap ada di halaman lain --}}
            </div>

            <div class="card-body px-0 pt-2 pb-0">
                @if(isset($data['produk_stok_rendah']) && $data['produk_stok_rendah']->isNotEmpty())
                    <ul class="list-group list-group-flush">
                        {{-- PERBAIKAN: Hanya menampilkan 5 item pertama --}}
                        @foreach ($data['produk_stok_rendah']->take(5) as $produk)
                            @php
                                $jenisProduk = $produk->jenisProduk;
                                $kategoriPertama = $jenisProduk ? $jenisProduk->kategoris->first() : null;
                            @endphp

                            <a href="{{ route('stok.show_by_jenis', [
                                'jenisProduk' => $produk->jenis_produk_id,
                                'active_kategori' => $kategoriPertama ? $kategoriPertama->id : ''
                            ]) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-semibold">{{ $produk->nama }}</span><br>
                                    <small class="text-muted">{{ optional($produk->toko)->name }} &middot; {{ optional($produk->jenisProduk)->name }}</small>
                                </div>
                                <span class="badge bg-danger-subtle text-danger-emphasis rounded-pill px-3 py-2 fs-6">
                                    {{ $produk->stok }} / <small>{{ $produk->minimal_stok }} {{ $produk->satuan }}</small>
                                </span>
                            </a>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center text-muted p-4">
                        <i class="bi bi-check-circle-fill fs-4 text-success"></i>
                        <p class="mb-0 mt-2">Semua stok aman!</p>
                    </div>
                @endif
            </div>

            {{-- PERBAIKAN: Tombol footer sekarang menjadi link ke halaman laporan --}}
            @if(isset($data['produk_stok_rendah']) && $data['produk_stok_rendah']->count() > 5)
                <div class="card-footer bg-transparent text-center px-0 pt-3 d-flex align-items-center justify-content-center">
                    <a href="{{ route('laporan.stok-rendah') }}" class="btn btn-sm btn-outline-primary fw-semibold d-flex w-30 align-items-center justify-content-center gap-2">
                        Lihat {{ $data['produk_stok_rendah']->count() - 5 }} item lainnya
                        <i class="bi bi-arrow-right-short"></i>
                    </a>
                </div>
            @endif
        </div>
    </div>

    @can('is-super-admin')
    <div class="bg-light-subtle p-3 rounded-4 shadow-sm mb-5">
        <div class="d-flex align-items-center mb-4">
            <h2 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2" style="font-size: 1.4rem;">
                <span class="border-start border-4 border-warning ps-3">
                    <i class="bi bi-bar-chart-line-fill text-warning me-2"></i> INFORMASI ANALITIK
                </span>
            </h2>
        </div>
        <hr class="mt-n2 mb-4 border-warning opacity-25">

        {{-- FILTER PERIODE --}}
        <div class="card shadow border-0 rounded-4 mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('dashboard') }}" class="d-flex flex-wrap align-items-end gap-3">
                    <div style="flex: 1 1 200px;">
                        <label class="form-label text-muted fw-semibold small">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="form-control form-control-sm shadow-sm"
                            value="{{ request('tanggal_mulai', now()->subDays(6)->toDateString()) }}">
                    </div>

                    <div class="d-flex align-items-center justify-content-center text-muted fw-semibold" style="min-width: 40px;">
                        s/d
                    </div>

                    <div style="flex: 1 1 200px;">
                        <label class="form-label text-muted fw-semibold small">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" class="form-control form-control-sm shadow-sm"
                            value="{{ request('tanggal_selesai', now()->toDateString()) }}">
                    </div>

                    <div style="flex: 0 0 120px;">
                        <label class="form-label invisible">_</label>
                        <button type="submit" class="btn btn-sm btn-primary w-100 fw-semibold shadow-sm">Terapkan</button>
                    </div>

                    <div style="flex: 0 0 120px;">
                        <label class="form-label invisible">_</label>
                        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary w-100 shadow-sm">Reset</a>
                    </div>
                </form>
            </div>
        </div>


        <div class="row g-4">
            <div class="col-lg-12">
                <div class="card shadow border-0 rounded-4 mb-1">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-semibold text-dark"><i class="bi bi-trophy me-2"></i>Produk Terlaris</h5>
                        <a href="{{ route('laporan.produk-terlaris') }}" class="btn btn-sm btn-outline-secondary shadow-sm"">
                            Lihat Semua
                        </a>
                    </div>
                    <div class="card-body">
                        <canvas id="topProductsChart" style="min-height: 250px;"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow border-0 rounded-4 mb-4">
                    <div class="card-header rounded-top-4 bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold text-dark"><i class="bi bi-graph-up me-2"></i>Item Terjual</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="salesChart" style="max-height: 320px;"></canvas>
                    </div>
                </div>

                <div class="card shadow border-0 rounded-4">
                    <div class="card-header rounded-top-4 bg-white border-bottom">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h5 class="mb-0 fw-semibold text-dark">
                                <i class="bi bi-arrow-down-up me-2"></i>Stok Masuk vs Keluar
                            </h5>

                            <form method="GET" action="{{ route('dashboard') }}" class="d-flex align-items-center gap-2">
                                <div class="form-group mb-0">
                                    <select name="jenis_produk_stok" class="form-select form-select-sm shadow-sm" onchange="this.form.submit()">
                                        <option value="">-- Semua Jenis Produk --</option>
                                        @foreach($data['jenis_produk_list'] as $jenis)
                                            <option value="{{ $jenis->id }}" @selected(request('jenis_produk_stok') == $jenis->id)>
                                                {{ $jenis->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <input type="hidden" name="tanggal_mulai" value="{{ request('tanggal_mulai', now()->subDays(6)->toDateString()) }}">
                                <input type="hidden" name="tanggal_selesai" value="{{ request('tanggal_selesai', now()->toDateString()) }}">
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="stockMovementChart" style="max-height: 320px;"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow border-0 rounded-4 mb-4">
                    <div class="card-header rounded-top-4 bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold text-dark"><i class="bi bi-cash-coin me-2"></i>Ringkasan Penjualan</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="row">
                            <div class="col-6 border-end">
                                <h6 class="text-muted">Hari Ini</h6>
                                <h4 class="fw-bold text-primary">{{ $data['penjualan_hari_ini'] ?? 0 }}</h4>
                            </div>
                            <div class="col-6">
                                <h6 class="text-muted">Bulan Ini</h6>
                                <h4 class="fw-bold text-primary">{{ $data['penjualan_bulan_ini'] ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow border-0 rounded-4">
                    <div class="card-header rounded-top-4 bg-white border-bottom">
                        <h5 class="mb-0 fw-semibold text-dark"><i class="bi bi-pie-chart-fill me-2"></i>Penjualan per Merchant</h5>
                    </div>
                    <div class="card-body d-flex justify-content-center align-items-center">
                        <canvas id="merchantPieChart" style="max-height: 250px; max-width: 250px;"></canvas>
                    </div>
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
         data-top-produk-labels='@json($data['topProdukLabels'] ?? [])'
         data-top-produk-data='@json($data['topProdukData'] ?? [])'
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
