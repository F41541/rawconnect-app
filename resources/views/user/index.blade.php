<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="container py-1">
        {{-- Tombol Tambah --}}
        <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('superadmin.user.create') }}" class="btn btn-primary shadow-sm d-flex align-items-center gap-2">
                <i class="bi bi-plus-circle"></i>
                <span>Tambah Pengguna</span>
            </a>
        </div>

        {{-- Tabel Pengguna --}}
        <div class="card shadow border-0 rounded-4">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-nowrap">No.</th>
                                <th class="text-nowrap">Nama</th>
                                <th class="text-nowrap">Email</th>
                                <th class="text-nowrap">Peran</th>
                                <th class="text-nowrap">Tgl. Dibuat</th>
                                <th class="text-nowrap text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration + $users->firstItem() - 1 }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-secondary text-capitalize">{{ $user->role }}</span>
                                    </td>
                                    <td>{{ $user->created_at->format('d M Y') }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('superadmin.user.edit', $user->id) }}" class="btn btn-sm btn-outline-primary me-1 px-3" title="Edit">
                                            <i class="bi bi-pencil me-1"></i>
                                        </a>
                                        <form action="{{ route('superadmin.user.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Anda yakin ingin menghapus pengguna ini secara permanen?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger px-3" title="Hapus">
                                                <i class="bi bi-trash me-1"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">Belum ada data pengguna.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layout>
