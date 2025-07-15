<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            {{-- PENJELASAN: Menambahkan judul utama halaman untuk konsistensi. --}}
            <a href="{{ route('superadmin.master.index') }}" class="btn btn-outline-secondary me-2" title="Kembali">
                <i class="bi bi-arrow-left"></i>
            </a>
            <a href="{{ route('superadmin.kategori.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Tambah Kategori
            </a>
        </div>
        <div class="card">
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>
                                @php
                                    $isSortedByName = $sortField === 'name';
                                    $nextOrder = $isSortedByName && $sortOrder === 'asc' ? 'desc' : 'asc';
                                    $arrow = $isSortedByName ? ($sortOrder === 'asc' ? '↑' : '↓') : '↕';
                                @endphp
                                <a href="{{ route('superadmin.kategori.index', ['sort' => 'name', 'order' => $nextOrder]) }}" class="text-decoration-none text-dark">
                                    Nama Kategori <span>{{ $arrow }}</span>
                                </a>
                            </th>
                            <th>
                                @php
                                    $isSortedByDate = $sortField === 'created_at';
                                    $nextOrder = $isSortedByDate && $sortOrder === 'asc' ? 'desc' : 'asc';
                                    $arrow = $isSortedByDate ? ($sortOrder === 'asc' ? '↑' : '↓') : '↕';
                                @endphp
                                <a href="{{ route('superadmin.kategori.index', ['sort' => 'created_at', 'order' => $nextOrder]) }}" class="text-decoration-none text-dark">
                                    Tanggal Dibuat <span>{{ $arrow }}</span>
                                </a>
                            </th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- PENJELASAN: Menggunakan nama variabel lengkap ($kategori) untuk konsistensi. --}}
                        @forelse ($kategoris as $kategori)
                            <tr>
                                <td>{{ $loop->iteration + $kategoris->firstItem() - 1 }}</td>
                                <td>{{ $kategori->name }}</td>
                                <td>{{ $kategori->created_at->format('d M Y') }}</td>
                                <td>
                                    {{-- PENJELASAN: Menggunakan ikon untuk tombol aksi agar ringkas & konsisten. --}}
                                    <a href="{{ route('superadmin.kategori.edit', $kategori->id) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('superadmin.kategori.destroy', $kategori->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada data kategori.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{-- PENJELASAN: Paginasi dibuat lebih pintar agar 'mengingat' sorting. --}}
                <div class="mt-3">
                    {{ $kategoris->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layout>
