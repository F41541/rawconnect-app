<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('superadmin.master.index') }}" class="btn btn-outline-secondary me-2" title="Kembali">
                <i class="bi bi-arrow-left"></i>
            </a>
            <a href="{{ route('superadmin.ekspedisi.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Tambah Ekspedisi
            </a>
        </div>
        <div class="card">
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>
                                <a href="{{ route('superadmin.ekspedisi.index', ['sort' => 'name', 'order' => $sortField === 'name' && $sortOrder === 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none text-dark">
                                    Nama Ekspedisi <span>{{ $sortField === 'name' ? ($sortOrder === 'asc' ? '↑' : '↓') : '↕' }}</span>
                                </a>
                            </th>
                            <th>
                                <a href="{{ route('superadmin.ekspedisi.index', ['sort' => 'created_at', 'order' => $sortField === 'created_at' && $sortOrder === 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none text-dark">
                                    Tanggal Dibuat <span>{{ $sortField === 'created_at' ? ($sortOrder === 'asc' ? '↑' : '↓') : '↕' }}</span>
                                </a>
                            </th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($ekspedisis as $ekspedisi)
                            <tr>
                                <td>{{ $loop->iteration + $ekspedisis->firstItem() - 1 }}</td>
                                <td>{{ $ekspedisi->name }}</td>
                                <td>{{ $ekspedisi->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('superadmin.ekspedisi.edit', $ekspedisi->id) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('superadmin.ekspedisi.destroy', $ekspedisi->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus ekspedisi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada data ekspedisi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-3">{{ $ekspedisis->appends(request()->query())->links() }}</div>
            </div>
        </div>
    </div>
</x-layout>
