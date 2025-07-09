<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            {{-- PENJELASAN: Menambahkan judul utama halaman untuk konsistensi. --}}
            <h2 class="mb-0">Manajemen Toko</h2>
            <a href="{{ route('toko.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Tambah Toko Baru
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Logo</th>
                            <th>
                                @php
                                    $isSortedByName = $sortField === 'name';
                                    $nextOrder = $isSortedByName && $sortOrder === 'asc' ? 'desc' : 'asc';
                                    $arrow = $isSortedByName ? ($sortOrder === 'asc' ? '↑' : '↓') : '↕';
                                @endphp
                                <a href="{{ route('toko.index', ['sort' => 'name', 'order' => $nextOrder]) }}" class="text-decoration-none text-dark">
                                    Nama Toko <span>{{ $arrow }}</span>
                                </a>
                            </th>
                            <th>
                                @php
                                    $isSortedByDate = $sortField === 'created_at';
                                    $nextOrder = $isSortedByDate && $sortOrder === 'asc' ? 'desc' : 'asc';
                                    $arrow = $isSortedByDate ? ($sortOrder === 'asc' ? '↑' : '↓') : '↕';
                                @endphp
                                <a href="{{ route('toko.index', ['sort' => 'created_at', 'order' => $nextOrder]) }}" class="text-decoration-none text-dark">
                                    Tanggal Dibuat <span>{{ $arrow }}</span>
                                </a>
                            </th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @forelse ($tokos as $toko)
                            <tr>
                                <td>{{ $loop->iteration + $tokos->firstItem() - 1 }}</td>
                                <td>
                                    @if ($toko->logo)
                                        <img src="{{ asset('uploads/logo_toko/' . $toko->logo) }}" alt="Logo {{ $toko->name }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                    @else
                                        {{-- PENJELASAN: Menerapkan aturan konsistensi untuk data kosong. --}}
                                        <span class="text-muted fst-italic" style="font-size: 0.8rem;">Tidak ada logo</span>
                                    @endif
                                </td>
                                <td>{{ $toko->name }}</td>
                                <td>{{ $toko->created_at->format('d M Y') }}</td>
                                <td>
                                    {{-- PENJELASAN: Menggunakan ikon untuk tombol aksi agar ringkas & konsisten. --}}
                                    <a href="{{ route('toko.edit', $toko->id) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('toko.destroy', $toko->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Sangat Yakin? Menghapus toko ini akan menghapus SEMUA produk, ekspedisi, dan merchant yang terhubung dengannya!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada data toko.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- PENJELASAN: Paginasi dibuat lebih pintar agar 'mengingat' sorting. --}}
                <div class="mt-3">
                    {{ $tokos->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layout>
