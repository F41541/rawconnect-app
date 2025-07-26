<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="container py-1">
        <div class="d-flex justify-content-between align-items-center mb-4">
            @can('is-super-admin')
                <a href="{{ route('superadmin.master.index') }}" class="btn btn-light border shadow-sm d-flex align-items-center gap-2" title="Kembali">
                    <i class="bi bi-arrow-left">Kembali</i>
                </a>
            @endcan

            <form action="{{ url()->current() }}" method="GET" class="d-flex align-items-center gap-2">
                <input type="hidden" name="sort" value="{{ request('sort', 'nama') }}">
                <input type="hidden" name="order" value="{{ request('order', 'asc') }}">
                <input type="hidden" name="active_kategori" value="{{ request('active_kategori') }}">

                <label for="per_page_top" class="form-label mb-0 small text-muted">Tampil:</label>
                @php $perPage = request('per_page', 15); @endphp
                <select class="form-select form-select-sm" name="per_page" id="per_page_top" style="width: auto;" onchange="this.form.submit()">
                    @foreach ([15, 25, 50, 100] as $option)
                        <option value="{{ $option }}" {{ $perPage == $option ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        @include('stok.partials.tabel-produk', ['produks' => $produks, 'sortField' => $sortField, 'sortOrder' => $sortOrder])
    </div>

    <!-- Modal Koreksi Stok -->
    <div class="modal fade" id="koreksiStokModal" tabindex="-1" aria-labelledby="koreksiStokModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow rounded-4">
                <div class="modal-header bg-light border-bottom-0 rounded-top-4">
                    <h5 class="modal-title" id="koreksiStokModalLabel">Koreksi Stok</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body">
                        <p>Anda akan mengoreksi stok untuk produk:</p>
                        <h6 id="namaProdukModal" class="mb-3 text-primary fw-semibold">Nama Produk</h6>

                        <label for="stokValueModal" class="form-label small text-muted">Jumlah Stok Sebenarnya:</label>
                        <input type="number" name="stok" id="stokValueModal" class="form-control form-control" min="0" required autofocus>
                    </div>
                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Koreksi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const koreksiStokModal = document.getElementById('koreksiStokModal');
        if (koreksiStokModal) {
            koreksiStokModal.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget;
                koreksiStokModal.querySelector('form').action = button.getAttribute('data-url');
                koreksiStokModal.querySelector('#namaProdukModal').textContent = button.getAttribute('data-nama');
                koreksiStokModal.querySelector('#stokValueModal').value = button.getAttribute('data-stok');
            });
        }
    </script>
    @endpush
</x-layout>
