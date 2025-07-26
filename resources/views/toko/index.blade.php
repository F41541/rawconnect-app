<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="container py-1">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('superadmin.master.index') }}" class="btn btn-light d-flex align-items-center border shadow-sm gap-2">
                <i class="bi bi-arrow-left me-2"></i> Kembali
            </a>
            <a href="{{ route('superadmin.toko.create') }}" class="btn btn-primary shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle me-2"></i> Tambah Toko Baru
            </a>
        </div>

        <div class="card shadow border-0 rounded-4">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-nowrap">#</th>
                                <th class="text-nowrap">Logo</th>
                                <th class="text-nowrap">
                                    @php
                                        $isSortedByName = $sortField === 'name';
                                        $nextOrder = $isSortedByName && $sortOrder === 'asc' ? 'desc' : 'asc';
                                        $arrow = $isSortedByName ? ($sortOrder === 'asc' ? '↑' : '↓') : '↕';
                                    @endphp
                                    <a href="{{ route('superadmin.toko.index', ['sort' => 'name', 'order' => $nextOrder]) }}" class="text-decoration-none text-dark fw-semibold">
                                        Nama Toko <span>{{ $arrow }}</span>
                                    </a>
                                </th>
                                <th class="text-nowrap">
                                    @php
                                        $isSortedByDate = $sortField === 'created_at';
                                        $nextOrder = $isSortedByDate && $sortOrder === 'asc' ? 'desc' : 'asc';
                                        $arrow = $isSortedByDate ? ($sortOrder === 'asc' ? '↑' : '↓') : '↕';
                                    @endphp
                                    <a href="{{ route('superadmin.toko.index', ['sort' => 'created_at', 'order' => $nextOrder]) }}" class="text-decoration-none text-dark fw-semibold">
                                        Tanggal Dibuat <span>{{ $arrow }}</span>
                                    </a>
                                </th>
                                <th class="text-nowrap">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($tokos as $toko)
                                <tr>
                                    <td class="text-muted">{{ $loop->iteration + $tokos->firstItem() - 1 }}</td>
                                    <td>
                                        @if ($toko->logo)
                                            <img src="{{ asset('uploads/logo_toko/' . $toko->logo) }}" alt="Logo {{ $toko->name }}" class="rounded-2 shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            <span class="text-muted fst-italic small">Tidak ada logo</span>
                                        @endif
                                    </td>
                                    <td class="fw-medium">{{ $toko->name }}</td>
                                    <td class="text-muted">{{ $toko->created_at->format('d M Y') }}</td>
                                    <td class="text-nowrap">
                                        <a href="{{ route('superadmin.toko.edit', $toko->id) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('superadmin.toko.destroy', $toko->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Sangat Yakin? Menghapus toko ini akan menghapus SEMUA produk, ekspedisi, dan merchant yang terhubung dengannya!')">
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
                                    <td colspan="5" class="text-center text-muted py-4">Belum ada data toko.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $tokos->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layout>
