<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="container py-1">
        {{-- Header Aksi --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('superadmin.master.index') }}" class="btn btn-light shadow-sm d-flex border align-items-center gap-2">
                <i class="bi bi-arrow-left"></i> <span>Kembali</span>
            </a>
            <a href="{{ route('superadmin.ekspedisi.create') }}" class="btn btn-primary shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle"></i> <span>Tambah Ekspedisi</span>
            </a>
        </div>

        {{-- Card Table --}}
        <div class="card border-0 shadow rounded-4">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table align-middle table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>
                                    <a href="{{ route('superadmin.ekspedisi.index', ['sort' => 'name', 'order' => $sortField === 'name' && $sortOrder === 'asc' ? 'desc' : 'asc']) }}"
                                       class="text-decoration-none text-dark fw-semibold">
                                        Nama Ekspedisi
                                        <span>
                                            {{ $sortField === 'name' ? ($sortOrder === 'asc' ? '↑' : '↓') : '↕' }}
                                        </span>
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('superadmin.ekspedisi.index', ['sort' => 'created_at', 'order' => $sortField === 'created_at' && $sortOrder === 'asc' ? 'desc' : 'asc']) }}"
                                       class="text-decoration-none text-dark fw-semibold">
                                        Tanggal Dibuat
                                        <span>
                                            {{ $sortField === 'created_at' ? ($sortOrder === 'asc' ? '↑' : '↓') : '↕' }}
                                        </span>
                                    </a>
                                </th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($ekspedisis as $ekspedisi)
                                <tr>
                                    <td>{{ $loop->iteration + $ekspedisis->firstItem() - 1 }}</td>
                                    <td class="fw-medium">{{ $ekspedisi->name }}</td>
                                    <td>{{ $ekspedisi->created_at->format('d M Y') }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('superadmin.ekspedisi.edit', $ekspedisi->id) }}"
                                           class="btn btn-sm btn-outline-primary me-1 shadow-sm" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('superadmin.ekspedisi.destroy', $ekspedisi->id) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('Yakin ingin menghapus ekspedisi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger shadow-sm" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada data ekspedisi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="d-flex justify-content-center mt-4">
                    {{ $ekspedisis->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layout>
