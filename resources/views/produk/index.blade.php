    <x-layout>
        <x-slot:title>{{ $title }}</x-slot:title>
        <div class="container py-1">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="{{ route('superadmin.master.index') }}" class="btn btn-light d-flex align-items-center border shadow-sm gap-2">
                    <i class="bi bi-arrow-left me-2"></i> Kembali
                </a>
                <a href="{{ route('superadmin.produk.create') }}" class="btn btn-primary shadow-sm d-flex align-items-center gap-2">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Produk
                </a>
            </div>

            {{-- Loop terluar: untuk setiap KATEGORI --}}
            @php
                $hasProduk = false;
            @endphp

            @foreach ($kategoris as $kategori)
                @if($kategori->jenisProduks->isNotEmpty())
                    @php $hasProduk = true; @endphp
                    <div class="mb-3">
                        <div class="accordion" id="accordionKategori{{ $kategori->id }}">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingKategori{{ $kategori->id }}">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseKategori{{ $kategori->id }}" aria-expanded="false" aria-controls="collapseKategori{{ $kategori->id }}">
                                        {{ $kategori->name }}
                                    </button>
                                </h2>
                                <div id="collapseKategori{{ $kategori->id }}" class="accordion-collapse collapse" aria-labelledby="headingKategori{{ $kategori->id }}" data-bs-parent="#accordionKategori{{ $kategori->id }}">
                                    <div class="accordion-body">
                                        <div class="row g-4">
                                            @foreach ($kategori->jenisProduks as $jenisProduk)
                                                <div class="col-md-6 col-lg-4">
                                                    <a href="{{ route('stok.show_by_jenis', ['jenisProduk' => $jenisProduk->id, 'active_kategori' => $kategori->id]) }}" class="card card-link text-decoration-none text-dark shadow-sm h-100">
                                                        <div class="card-body">
                                                            <div class="d-flex justify-content-between align-items-start">
                                                                <h5 class="card-title mb-0">{{ $jenisProduk->name }}</h5>
                                                                <span class="badge bg-primary rounded-pill">{{ $jenisProduk->produks_count }} Produk</span>
                                                            </div>
                                                            <p class="card-text text-muted mt-2">Lihat dan kelola stok untuk jenis produk ini.</p>
                                                        </div>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach

            @if(!$hasProduk)
                <div class="col-12">
                    <div class="alert alert-warning text-center">
                        Belum ada Kategori yang dibuat atau semua kategori kosong. Silakan tambahkan terlebih dahulu <a href="{{ route('superadmin.produk.create') }}">Tambah Produk</a>.
                    </div>
                </div>
            @endif
        </div>
    </x-layout>