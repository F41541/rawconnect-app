<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="container py-1">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('superadmin.master.index') }}" class="btn btn-light d-flex align-items-center border shadow-sm gap-2">
                <i class="bi bi-arrow-left me-2"></i> Kembali
            </a>
            <a href="{{ route('superadmin.kategori.create') }}" class="btn btn-primary shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle me-2"></i>Tambah Kategori
            </a>
        </div>

        <div class="card shadow rounded-4 border-0">
            <div class="card-body px-4 py-3">
                <h5 class="mb-4 fw-semibold">Daftar Kategori</h5>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No.</th>
                                <th>
                                    @php
                                        $isSortedByName = $sortField === 'name';
                                        $nextOrder = $isSortedByName && $sortOrder === 'asc' ? 'desc' : 'asc';
                                        $arrow = $isSortedByName ? ($sortOrder === 'asc' ? '↑' : '↓') : '↕';
                                    @endphp
                                    <a href="{{ route('superadmin.kategori.index', ['sort' => 'name', 'order' => $nextOrder]) }}" class="text-decoration-none text-dark">
                                        Nama Kategori <small>{{ $arrow }}</small>
                                    </a>
                                </th>
                                <th>
                                    @php
                                        $isSortedByDate = $sortField === 'created_at';
                                        $nextOrder = $isSortedByDate && $sortOrder === 'asc' ? 'desc' : 'asc';
                                        $arrow = $isSortedByDate ? ($sortOrder === 'asc' ? '↑' : '↓') : '↕';
                                    @endphp
                                    <a href="{{ route('superadmin.kategori.index', ['sort' => 'created_at', 'order' => $nextOrder]) }}" class="text-decoration-none text-dark">
                                        Tanggal Dibuat <small>{{ $arrow }}</small>
                                    </a>
                                </th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($kategoris as $kategori)
                                <tr>
                                    <td>{{ $loop->iteration + $kategoris->firstItem() - 1 }}</td>
                                    <td>{{ $kategori->name }}</td>
                                    <td>{{ $kategori->created_at->format('d M Y') }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('superadmin.kategori.edit', $kategori->id) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('superadmin.kategori.destroy', $kategori->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
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
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada data kategori.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $kategoris->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layout>
