<div class="table-responsive">
    <table class="table align-middle table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>Foto</th>
                <th>
                    @php
                        $isSorted = $sortField === 'nama';
                        $nextOrder = $isSorted && $sortOrder === 'asc' ? 'desc' : 'asc';
                        $arrow = $isSorted ? ($sortOrder === 'asc' ? '↑' : '↓') : '↕';
                    @endphp
                    {{-- PERBAIKAN: Link sorting sekarang membawa parameter 'per_page' --}}
                    <a href="{{ url()->current() }}?sort=nama&order={{ $nextOrder }}&per_page={{ request('per_page', 15) }}&active_kategori={{ request('active_kategori') }}" class="text-decoration-none text-dark">
                        Nama Produk <span>{{ $arrow }}</span>
                    </a>
                </th>
                <th>Toko</th>
                <th>
                    @php
                        $isSorted = $sortField === 'stok';
                        $nextOrder = $isSorted && $sortOrder === 'asc' ? 'desc' : 'asc';
                        $arrow = $isSorted ? ($sortOrder === 'asc' ? '↑' : '↓') : '↕';
                    @endphp
                    {{-- PERBAIKAN: Link sorting sekarang membawa parameter 'per_page' --}}
                    <a href="{{ url()->current() }}?sort=stok&order={{ $nextOrder }}&per_page={{ request('per_page', 15) }}&active_kategori={{ request('active_kategori') }}" class="text-decoration-none text-dark">
                        Stok <span>{{ $arrow }}</span>
                    </a>
                </th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($produks as $produk)
                <tr>
                    <td>
                        @if($produk->foto)
                            <img src="{{ asset('uploads/foto_produk/' . $produk->foto) }}" alt="{{ $produk->nama }}" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                        @else
                            <span class="text-muted fst-italic" style="font-size: 0.8rem;">Tidak ada foto</span>
                        @endif
                    </td>
                    <td>{{ $produk->nama }}</td>
                    <td>{{ $produk->toko->name ?? 'N/A' }}</td>
                    <td>{{ $produk->stok }}</td>
                    <td>
                        @can('is-super-admin')
                            <a href="{{ route('superadmin.produk.edit', $produk->id) }}" class="btn btn-sm btn-outline-primary" title="Edit Detail Produk"><i class="bi bi-pencil"></i></a>
                        @endcan

                        @can('adjust-stock')
                            <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#koreksiStokModal" data-url="{{ route('stok.koreksi', $produk->id) }}" data-nama="{{ $produk->nama }}" data-stok="{{ $produk->stok }}" title="Koreksi Stok">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                        @endcan

                        @can('is-super-admin')
                            <form action="{{ route('superadmin.produk.destroy', $produk->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus produk ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                            </form>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr>
                    {{-- PERBAIKAN: Colspan disesuaikan menjadi 5 kolom --}}
                    <td colspan="5" class="text-center text-muted">Belum ada data produk untuk jenis ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
<div class="d-flex justify-content-between align-items-center mt-3">
    {{-- Bagian Kiri: Informasi jumlah data --}}
    <div class="text-muted" style="font-size: 0.9rem;">
        Menampilkan {{ $produks->firstItem() ?? 0 }} - {{ $produks->lastItem() ?? 0 }} dari {{ $produks->total() }} hasil
    </div>

    {{-- Bagian Kanan: Gabungan tombol halaman dan dropdown --}}
    <div class="d-flex align-items-center">
        
        <form action="{{ url()->current() }}" method="GET" class="d-flex align-items-center me-3">
            <input type="hidden" name="active_kategori" value="{{ request('active_kategori') }}">
            <input type="hidden" name="sort" value="{{ request('sort', 'nama') }}">
            <input type="hidden" name="order" value="{{ request('order', 'asc') }}">
            <label for="per_page" class="form-label me-2 mb-0 text-muted"><small>Tampil:</small></label>
            
            {{-- LOGIKA BARU YANG SUDAH DIPERBAIKI --}}
            @php
                // Ambil nilai 'per_page' dari URL sekali saja, dengan default 15
                $perPage = request('per_page', 15);
            @endphp
            <select class="form-select form-select-sm" style="width: auto;" name="per_page" id="per_page" onchange="this.form.submit()">
                <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
                <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
            </select>
        </form>

        {{-- Tombol Halaman (Next/Previous) --}}
        {{ $produks->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
</div>
</div>