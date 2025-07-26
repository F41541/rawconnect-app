<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="container py-1">
        {{-- Top Bar --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('superadmin.master.index') }}" class="btn btn-light d-flex align-items-center gap-2 border shadow-sm">
                <i class="bi bi-arrow-left"></i> <span>Kembali</span>
            </a>
            <a href="{{ route('superadmin.merchant.create') }}" class="btn btn-primary shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle"></i> <span>Tambah Merchant</span>
            </a>
        </div>

        {{-- Card --}}
        <div class="card shadow border-0">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th>No.</th>
                                <th>
                                    <a href="{{ route('superadmin.merchant.index', ['sort' => 'name', 'order' => $sortField === 'name' && $sortOrder === 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none text-dark d-flex align-items-center gap-1">
                                        Nama Merchant 
                                        <span class="text-muted small">{{ $sortField === 'name' ? ($sortOrder === 'asc' ? '↑' : '↓') : '↕' }}</span>
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('superadmin.merchant.index', ['sort' => 'created_at', 'order' => $sortField === 'created_at' && $sortOrder === 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none text-dark d-flex align-items-center gap-1">
                                        Tanggal Dibuat 
                                        <span class="text-muted small">{{ $sortField === 'created_at' ? ($sortOrder === 'asc' ? '↑' : '↓') : '↕' }}</span>
                                    </a>
                                </th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($merchants as $merchant)
                                <tr>
                                    <td>{{ $loop->iteration + $merchants->firstItem() - 1 }}</td>
                                    <td>{{ $merchant->name }}</td>
                                    <td>{{ $merchant->created_at->format('d M Y') }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('superadmin.merchant.edit', $merchant->id) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('superadmin.merchant.destroy', $merchant->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus merchant ini?')">
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
                                    <td colspan="4" class="text-center text-muted py-4">Belum ada data merchant.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-3 d-flex justify-content-center">
                    {{ $merchants->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layout>
