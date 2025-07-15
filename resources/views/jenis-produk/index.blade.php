<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('superadmin.master.index') }}" class="btn btn-outline-secondary me-2" title="Kembali">
                <i class="bi bi-arrow-left"></i>
            </a>
            <a href="{{ route('superadmin.jenis-produk.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Tambah Jenis Produk
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
                                <a href="{{ route('superadmin.jenis-produk.index', ['sort' => 'name', 'order' => $nextOrder]) }}" class="text-decoration-none text-dark">
                                    Nama Jenis Produk <span>{{ $arrow }}</span>
                                </a>
                            </th>
                            <th>Kategori</th>
                            <th>
                                @php
                                    $isSortedByDate = $sortField === 'created_at';
                                    $nextOrder = $isSortedByDate && $sortOrder === 'asc' ? 'desc' : 'asc';
                                    $arrow = $isSortedByDate ? ($sortOrder === 'asc' ? '↑' : '↓') : '↕';
                                @endphp
                                <a href="{{ route('superadmin.jenis-produk.index', ['sort' => 'created_at', 'order' => $nextOrder]) }}" class="text-decoration-none text-dark">
                                    Tanggal Dibuat <span>{{ $arrow }}</span>
                                </a>
                            </th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- PENJELASAN: Menggunakan nama variabel lengkap ($jenisProduk) untuk konsistensi. --}}
                        @forelse ($jenisProduks as $jenisProduk)
                            <tr>
                                <td>{{ $loop->iteration + $jenisProduks->firstItem() - 1 }}</td>
                                <td>{{ $jenisProduk->name }}</td>
                                <td>
                                    {{-- PENJELASAN: Menampilkan semua kategori yang terhubung sebagai badge. --}}
                                    @forelse($jenisProduk->kategoris as $kategori)
                                        <span class="badge bg-secondary me-1">{{ $kategori->name }}</span>
                                    @empty
                                        {{-- PENJELASAN: Menerapkan aturan konsistensi untuk data kosong. --}}
                                        <span class="text-muted fst-italic" style="font-size: 0.8rem;">Tidak Ada Kategori</span>
                                    @endforelse
                                </td>
                                <td>{{ $jenisProduk->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('superadmin.jenis-produk.edit', $jenisProduk->id) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('superadmin.jenis-produk.destroy', $jenisProduk->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin menghapus jenis produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada data jenis produk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-3">
                    {{ $jenisProduks->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</x-layout>
