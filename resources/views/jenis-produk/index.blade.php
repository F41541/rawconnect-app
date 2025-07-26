<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="container py-1">
        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('superadmin.master.index') }}" class="btn btn-light d-flex d-flex align-items-center border shadow-sm gap-2">
                <i class="bi bi-arrow-left me-2"></i> Kembali
            </a>
                <a href="{{ route('superadmin.jenis-produk.create') }}" class="btn btn-primary shadow-sm d-flex align-items-center gap-2">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Jenis Produk
                </a>
        </div>

        {{-- CARD WRAPPER --}}
        <div class="card shadow border-0 rounded-4">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="align-middle">
                                <th style="width: 50px;">No.</th>
                                <th>
                                    @php
                                        $isSortedByName = $sortField === 'name';
                                        $nextOrder = $isSortedByName && $sortOrder === 'asc' ? 'desc' : 'asc';
                                        $arrow = $isSortedByName ? ($sortOrder === 'asc' ? '↑' : '↓') : '↕';
                                    @endphp
                                    <a href="{{ route('superadmin.jenis-produk.index', ['sort' => 'name', 'order' => $nextOrder]) }}" class="text-decoration-none text-dark fw-semibold">
                                        Nama Jenis Produk <span class="ms-1">{{ $arrow }}</span>
                                    </a>
                                </th>
                                <th>Kategori</th>
                                <th>
                                    @php
                                        $isSortedByDate = $sortField === 'created_at';
                                        $nextOrder = $isSortedByDate && $sortOrder === 'asc' ? 'desc' : 'asc';
                                        $arrow = $isSortedByDate ? ($sortOrder === 'asc' ? '↑' : '↓') : '↕';
                                    @endphp
                                    <a href="{{ route('superadmin.jenis-produk.index', ['sort' => 'created_at', 'order' => $nextOrder]) }}" class="text-decoration-none text-dark fw-semibold">
                                        Tanggal Dibuat <span class="ms-1">{{ $arrow }}</span>
                                    </a>
                                </th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($jenisProduks as $jenisProduk)
                                <tr>
                                    <td>{{ $loop->iteration + $jenisProduks->firstItem() - 1 }}</td>
                                    <td class="fw-medium">{{ $jenisProduk->name }}</td>
                                    <td>
                                        @forelse($jenisProduk->kategoris as $kategori)
                                            <span class="badge bg-secondary me-1">{{ $kategori->name }}</span>
                                        @empty
                                            <span class="text-muted fst-italic" style="font-size: 0.85rem;">Tidak Ada Kategori</span>
                                        @endforelse
                                    </td>
                                    <td>{{ $jenisProduk->created_at->format('d M Y') }}</td>
                                    <td class="text-end">
                                        <a href="{{ route('superadmin.jenis-produk.edit', $jenisProduk->id) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('superadmin.jenis-produk.destroy', $jenisProduk->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin menghapus jenis produk ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        Belum ada data jenis produk.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                <div class="mt-4">
                    {{ $jenisProduks->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layout>
