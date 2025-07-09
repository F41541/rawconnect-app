<div class="sidebar" id="sidebar">
    <div class="text-center py-3">
        <img src="{{ asset('images/logo.png') }}" alt="Raw Tisane" width="80" height="80" />
        <h6 class="mt-2">{{ auth()->user()->name ?? 'Tamu' }}</h6>
    </div>

    <ul class="nav flex-column">
        {{-- Link Utama --}}
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="bi bi-house-door me-2"></i>Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('pengiriman.*') ? 'active' : '' }}" href="{{ route('pengiriman.index') }}">
                <i class="bi bi-truck me-2"></i>Pengiriman
            </a>
        </li>

        {{-- PEMISAH VISUAL --}}
        @can ('is-super-admin')

        <li class="nav-item">
            <h6 class="sidebar-heading text-muted">Administrasi</h6>
        </li>

        <li class="nav-item">
            @php $isSuperAdminActive = request()->routeIs(['user.*', 'produk.*', 'kategori.*', 'jenis-produk.*', 'toko.*', 'merchant.*', 'ekspedisi.*', 'layanan-pengiriman.*']); @endphp
            <button class="btn text-start d-flex align-items-center justify-content-between" data-bs-toggle="collapse" data-bs-target="#fitursuperadminMenu" aria-expanded="{{ $isSuperAdminActive ? 'true' : 'false' }}">
                <span class="d-flex align-items-center"><i class="bi bi-gear me-2"></i> Fitur Super Admin</span>
                <i class="bi bi-chevron-down rotate-icon"></i>
            </button>
            <div class="collapse {{ $isSuperAdminActive ? 'show' : '' }}" id="fitursuperadminMenu" data-bs-parent="#sidebar">
                <ul class="list-unstyled mb-0 sidebar-submenu">
                    <li><a class="nav-link {{ request()->routeIs('user.*') ? 'active' : '' }}" href="{{ route('user.index') }}"><i class="bi bi-people-fill me-2"></i>Manajemen Pengguna</a></li>
                    <li><a class="nav-link {{ request()->routeIs('produk.*') ? 'active' : '' }}" href="{{ route('produk.index') }}"><i class="bi bi-boxes me-2"></i>Manajemen Produk</a></li>
                    <li><a class="nav-link {{ request()->routeIs('kategori.*') ? 'active' : '' }}" href="{{ route('kategori.index') }}"><i class="bi bi-bookmark-star me-2"></i>Manajemen Kategori</a></li>
                    <li><a class="nav-link {{ request()->routeIs('jenis-produk.*') ? 'active' : '' }}" href="{{ route('jenis-produk.index') }}"><i class="bi bi-tags me-2"></i>Manajemen Jenis Produk</a></li>
                    <li><a class="nav-link {{ request()->routeIs('toko.*') ? 'active' : '' }}" href="{{ route('toko.index') }}"><i class="bi bi-shop me-2"></i>Manajemen Toko</a></li>
                    <li><a class="nav-link {{ request()->routeIs('merchant.*') ? 'active' : '' }}" href="{{ route('merchant.index') }}"><i class="bi bi-person-badge me-2"></i>Manajemen Merchant</a></li>
                    <li><a class="nav-link {{ request()->routeIs('ekspedisi.*') ? 'active' : '' }}" href="{{ route('ekspedisi.index') }}"><i class="bi bi-truck-flatbed me-2"></i>Manajemen Ekspedisi</a></li>
                    <li><a class="nav-link {{ request()->routeIs('layanan-pengiriman.*') ? 'active' : '' }}" href="{{ route('layanan-pengiriman.index') }}"><i class="bi bi-diagram-3 me-2"></i>Konfigurasi Layanan</a></li>                
                </ul>
            </div>
        </li>
        @endcan


        {{-- PEMISAH VISUAL --}}
        <li class="nav-item">
            <h6 class="sidebar-heading text-muted">STOK PRODUK</h6>
        </li>

        @php
            // Ambil objek kategori 'Produk Jual' dari koleksi
            $kategoriProdukJual = $sidebarKategoris->firstWhere('name', 'Produk Jual');
        @endphp

        {{-- 1. Tampilkan Menu Statis untuk 'Produk Jual' JIKA ADA --}}
        @if ($kategoriProdukJual && $kategoriProdukJual->jenisProduks->isNotEmpty())
            <li class="nav-item">
                @php
                    $isProdukJualActive = request('active_kategori') == $kategoriProdukJual->id;
                @endphp
                <button class="btn text-start d-flex align-items-center justify-content-between"
                    data-bs-toggle="collapse" data-bs-target="#kategoriMenu{{ $kategoriProdukJual->id }}" aria-expanded="{{ $isProdukJualActive ? 'true' : 'false' }}">
                    <span class="d-flex align-items-center">
                        {{-- IKON KHUSUS UNTUK PRODUK JUAL --}}
                        <i class="bi bi-tags me-2"></i> 
                        {{ $kategoriProdukJual->name }}
                    </span>
                    <i class="bi bi-chevron-down rotate-icon"></i>
                </button>
                <div class="collapse {{ $isProdukJualActive ? 'show' : '' }}" id="kategoriMenu{{ $kategoriProdukJual->id }}" data-bs-parent="#sidebar">
                    <ul class="list-unstyled mb-0 sidebar-submenu">
                        @foreach($kategoriProdukJual->jenisProduks as $jenisProduk)
                        <li>
                            @php
                                $isActiveLink = (request()->route('jenisProduk') && request()->route('jenisProduk')->id == $jenisProduk->id);
                            @endphp
                            <a class="nav-link d-flex justify-content-between align-items-center {{ $isActiveLink ? 'active' : '' }}" 
                               href="{{ route('stok.show_by_jenis', ['jenisProduk' => $jenisProduk->id, 'active_kategori' => $kategoriProdukJual->id]) }}">
                                <span><i class="bi bi-dot me-2"></i> {{ $jenisProduk->name }}</span>
                                <span class="badge rounded-pill bg-light text-dark">{{ $jenisProduk->produks_count }}</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </li>
        @endif


        {{-- 2. Tampilkan semua kategori LAINNYA secara dinamis --}}
        @foreach($sidebarKategoris->where('name', '!=', 'Produk Jual') as $kategori)
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


        {{-- Dropdown Edit Stok (Statis) --}}
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('stok-adj.*') ? 'active' : '' }}" href="{{ route('stok-adj.index') }}">
                <i class="bi bi-pencil-square me-2"></i>Tambah/Kurangi Stok
            </a>
        </li>

        <li class="nav-item">
            <h6 class="sidebar-heading text-muted">AKUN</h6>
        </li>

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                <i class="bi bi-person me-2"></i>Pengaturan Akun
            </a>
        </li>
    </ul>

    <div class="mt-auto p-3">
        @auth
            {{-- Form ini diperlukan agar logout aman (menggunakan method POST) --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <a href="{{ route('logout') }}" 
                class="btn btn-danger btn-sm w-100 d-flex align-items-center justify-content-center"
                onclick="event.preventDefault(); this.closest('form').submit();">
                    <span>Logout</span>
                    <i class="bi bi-box-arrow-right ms-2"></i>
                </a>
            </form>
        @endauth
    </div>
</div>
<div class="overlay" id="overlay"></div>