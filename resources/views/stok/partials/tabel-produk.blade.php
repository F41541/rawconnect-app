<div class="table-responsive rounded-4 shadow-sm border bg-white p-3">
    <table class="table align-middle table-hover mb-0">
        <thead class="table-light border-bottom">
            <tr>
                <th>Foto</th>
                <th>
                    @php
                        $isSorted = $sortField === 'nama';
                        $nextOrder = $isSorted && $sortOrder === 'asc' ? 'desc' : 'asc';
                        $arrow = $isSorted ? ($sortOrder === 'asc' ? '↑' : '↓') : '↕';
                    @endphp
                    <a href="{{ url()->current() }}?sort=nama&order={{ $nextOrder }}&per_page={{ request('per_page', 15) }}&active_kategori={{ request('active_kategori') }}"
                       class="text-decoration-none text-dark fw-semibold">
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
                    <a href="{{ url()->current() }}?sort=stok&order={{ $nextOrder }}&per_page={{ request('per_page', 15) }}&active_kategori={{ request('active_kategori') }}"
                       class="text-decoration-none text-dark fw-semibold">
                        Stok <span>{{ $arrow }}</span>
                    </a>
                </th>
                <th>Satuan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($produks as $produk)
                <tr>
                    <td>
                        @if($produk->foto)
                            <img src="{{ asset('uploads/foto_produk/' . $produk->foto) }}" alt="{{ $produk->nama }}"
                                 class="rounded border shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                        @else
                            <span class="text-muted fst-italic small">Tidak ada foto</span>
                        @endif
                    </td>
                    <td class="fw-medium">{{ $produk->nama }}</td>
                    <td>{{ $produk->toko->name ?? 'N/A' }}</td>
                    <td>
                        <span>{{ number_format($produk->stok, 0, ',', '.') }}</span>
                    </td>
                    <td><span?>{{ $produk->satuan }}</span></td>

                    <td>
                        @can('is-super-admin')
                            <a href="{{ route('superadmin.produk.edit', $produk->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                        @endcan
                        @can('adjust-stock')
                            <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#koreksiStokModal"
                                    data-url="{{ route('stok.koreksi', $produk->id) }}" data-nama="{{ $produk->nama }}" data-stok="{{ $produk->stok }}" title="Koreksi Stok">
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
                    <td colspan="5" class="text-center text-muted">Belum ada data produk untuk jenis ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="d-flex justify-content-between align-items-center mt-3">
        <small class="text-muted">
            Menampilkan {{ $produks->firstItem() ?? 0 }} - {{ $produks->lastItem() ?? 0 }} dari {{ $produks->total() }} hasil
        </small>
        <div class="d-flex align-items-center gap-3">
            <form action="{{ url()->current() }}" method="GET" class="d-flex align-items-center">
                <input type="hidden" name="active_kategori" value="{{ request('active_kategori') }}">
                <input type="hidden" name="sort" value="{{ request('sort', 'nama') }}">
                <input type="hidden" name="order" value="{{ request('order', 'asc') }}">
                <label for="per_page" class="me-2 small text-muted mb-0">Tampil:</label>
                <select class="form-select form-select-sm" name="per_page" id="per_page" onchange="this.form.submit()">
                    @foreach ([15, 25, 50, 100] as $limit)
                        <option value="{{ $limit }}" {{ request('per_page', 15) == $limit ? 'selected' : '' }}>{{ $limit }}</option>
                    @endforeach
                </select>
            </form>
            {{ $produks->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
