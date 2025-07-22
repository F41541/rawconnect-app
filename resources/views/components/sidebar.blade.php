<div class="sidebar" id="sidebar">
    <div class="text-center py-3">
        <img src="{{ asset('images/logo.png') }}" alt="Raw Tisane" width="80" height="80" />
        <h6 class="mt-2">{{ auth()->user()->name ?? 'Tamu' }}</h6>
    </div>

    <ul class="nav flex-column">
        {{-- =================================== --}}
        {{-- == MENU UNTUK SEMUA PERAN LOGIN === --}}
        {{-- =================================== --}}
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="bi bi-house-door me-2"></i>Dashboard
            </a>
        </li>

        {{-- ======================================================= --}}
        {{-- == MENU OPERASIONAL (PEGAWAI, ADMIN, SUPER ADMIN) == --}}
        {{-- ======================================================= --}}
        <li class="nav-item">
            <h6 class="sidebar-heading text-muted">OPERASIONAL</h6>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('pengiriman.index') ? 'active' : '' }}" href="{{ route('pengiriman.index') }}">
                <i class="bi bi-truck me-2"></i>Pengiriman
            </a>
        </li>

        {{-- Tombol Buat Pengiriman hanya untuk Admin & Super Admin --}}
        @can('manage-shipments')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('pengiriman.create') ? 'active' : '' }}" href="{{ route('pengiriman.create') }}">
                    <i class="bi bi-plus-circle me-2"></i>Buat Pengiriman
                </a>
            </li>
        @endcan

        {{-- Tombol Penyesuaian Stok untuk Pegawai & Admin --}}
        @can('adjust-stock')
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('stok-adj.*') ? 'active' : '' }}" href="{{ route('stok-adj.index') }}">
                    <i class="bi bi-pencil-square me-2"></i>Penyesuaian Stok
                </a>
            </li>
        @endcan

        {{-- ============================================== --}}
        {{-- == LIHAT STOK (UNTUK SEMUA PERAN) == --}}
        {{-- ============================================== --}}
        <li class="nav-item">
            <h6 class="sidebar-heading text-muted">LIHAT STOK</h6>
        </li>
        @if(isset($sidebarKategoris))
            @foreach($sidebarKategoris as $kategori)
                    @if($kategori->jenisProduks->isNotEmpty())
                        <li class="nav-item">
                            @php
                                $isKategoriActive = request('active_kategori') == $kategori->id;
                            @endphp
                            <button class="btn text-start d-flex align-items-center justify-content-between"
                                data-bs-toggle="collapse" data-bs-target="#kategoriMenu{{ $kategori->id }}" aria-expanded="{{ $isKategoriActive ? 'true' : 'false' }}">
                                <span class="d-flex align-items-center">
                                    {{-- IKON DEFAULT FOLDER --}}
                                    <i class="bi bi-folder2-open me-2"></i>
                                    {{ $kategori->name }}
                                </span>
                                <i class="bi bi-chevron-down rotate-icon"></i>
                            </button>
                            <div class="collapse {{ $isKategoriActive ? 'show' : '' }}" id="kategoriMenu{{ $kategori->id }}" data-bs-parent="#sidebar">
                                <ul class="list-unstyled mb-0 sidebar-submenu">
                                    @foreach($kategori->jenisProduks as $jenisProduk)
                                    <li>
                                        @php
                                            $isActiveLink = (request()->route('jenisProduk') && request()->route('jenisProduk')->id == $jenisProduk->id);
                                        @endphp
                                        <a class="nav-link d-flex justify-content-between align-items-center {{ $isActiveLink ? 'active' : '' }}" 
                                        href="{{ route('stok.show_by_jenis', ['jenisProduk' => $jenisProduk->id, 'active_kategori' => $kategori->id]) }}">
                                            <span><i class="bi bi-dot me-2"></i> {{ $jenisProduk->name }}</span>
                                            <span class="badge rounded-pill bg-light text-dark">{{ $jenisProduk->produks_count }}</span>
                                        </a>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </li>
                    @endif
            @endforeach
        @endif
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('log.stok') ? 'active' : '' }}" href="{{ route('log.stok') }}">
                <i class="bi bi-clock-history me-2"></i>Riwayat Stok
            </a>
        </li>


        {{-- ============================================== --}}
        {{-- == PANEL SUPER ADMIN (HANYA SUPER ADMIN) === --}}
        {{-- ============================================== --}}
        @can('is-super-admin')
            <li class="nav-item">
                <h6 class="sidebar-heading text-muted">PANEL SUPER ADMIN</h6>
            </li>
            <li class="nav-item">
                {{-- Pengecekan routeIs sekarang mencakup semua rute di bawah 'superadmin' --}}
                @php $isAdminActive = request()->routeIs('superadmin.*'); @endphp
                
                <button class="btn text-start d-flex align-items-center justify-content-between {{ $isAdminActive ? 'active' : '' }}" data-bs-toggle="collapse" data-bs-target="#adminMenu" aria-expanded="{{ $isAdminActive ? 'true' : 'false' }}">
                    <span class="d-flex align-items-center"><i class="bi bi-gear-wide-connected me-2"></i> Administrasi</span>
                    <i class="bi bi-chevron-down rotate-icon {{ $isAdminActive ? 'rotated' : '' }}"></i>
                </button>

                <div class="collapse {{ $isAdminActive ? 'show' : '' }}" id="adminMenu">
                    <ul class="list-unstyled mb-0 sidebar-submenu">
                        <li>
                            <a class="nav-link {{ request()->routeIs('superadmin.user.*') ? 'active' : '' }}" href="{{ route('superadmin.user.index') }}">
                                <i class="bi bi-people-fill me-2"></i>Manajemen Pengguna
                            </a>
                        </li>
                        <li>
                            <a class="nav-link {{ request()->routeIs('superadmin.master.index') || request()->routeIs('superadmin.toko.*') || request()->routeIs('superadmin.produk.*') || request()->routeIs('superadmin.kategori.*') || request()->routeIs('superadmin.jenis-produk.*') || request()->routeIs('superadmin.merchant.*')  || request()->routeIs('superadmin.ekspedisi.*') || request()->routeIs('superadmin.layanan-pengiriman.*') ? 'active' : '' }}" href="{{ route('superadmin.master.index') }}">
                                <i class="bi bi-hdd-stack-fill me-2"></i>Manajemen Master
                            </a>
                        </li>
                        <li>
                            <a class="nav-link {{ request()->routeIs('superadmin.laporan.penjualan') ? 'active' : '' }}" href="{{ route('superadmin.laporan.penjualan') }}">
                                <i class="bi bi-file-earmark-bar-graph-fill me-2"></i>Laporan Penjualan
                            </a>
                        </li>
                        
                    </ul>
                </div>
            </li>
        @endcan


        {{-- BAGIAN AKUN DI PALING BAWAH (UNTUK SEMUA) --}}
        <li class="nav-item"> 
            <h6 class="sidebar-heading">
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('profile.edit') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                <i class="bi bi-person-circle me-2"></i>Pengaturan Akun
            </a>
        </li>
        <li class="nav-item">
            @auth
            <form method="POST" action="{{ route('logout') }}" class="m-2">
                @csrf
                <button type="submit" class="nav-link btn btn-sm btn-outline-danger d-flex align-items-center justify-content-start">
                <i class="bi bi-box-arrow-right me-2"></i>Logout
                </button>
            </form>
            @endauth
        </li>
    </ul>
</div>
<div class="overlay" id="overlay"></div>


