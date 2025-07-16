<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
          @can('is-super-admin')
            <a href="{{ route('superadmin.produk.index') }}" class="btn btn-secondary">
              <i class="bi bi-arrow-left me-2"></i>
            </a>
          @endcan
            
            <div class="d-flex align-items-center">
                {{-- PENJELASAN: Form ini sekarang membawa 'active_kategori' agar sidebar tidak menutup --}}
                <form action="{{ url()->current() }}" method="GET" class="d-flex align-items-center me-3">
                    <input type="hidden" name="sort" value="{{ request('sort', 'nama') }}">
                    <input type="hidden" name="order" value="{{ request('order', 'asc') }}">
                    <input type="hidden" name="active_kategori" value="{{ request('active_kategori') }}">
                    
                    <label for="per_page_top" class="form-label me-2 mb-0 text-muted"><small>Tampil:</small></label>
                    @php $perPage = request('per_page', 15); @endphp
                    <select class="form-select form-select-sm" style="width: auto;" name="per_page" id="per_page_top" onchange="this.form.submit()">
                        <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </form>

                
            </div>
        </div>
    
        @include('stok.partials.tabel-produk', ['produks' => $produks, 'sortField' => $sortField, 'sortOrder' => $sortOrder])

    </div>

    <!-- Modal untuk Koreksi Stok -->
    <div class="modal fade" id="koreksiStokModal" tabindex="-1" aria-labelledby="koreksiStokModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="koreksiStokModalLabel">Koreksi Stok</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="" method="POST">
            @csrf
            @method('PATCH')
            <div class="modal-body">
                <p class="mb-1">Anda akan mengoreksi stok untuk produk:</p>
                <h6 class="mb-3" id="namaProdukModal">Nama Produk Akan Muncul Di Sini</h6>
                <label for="stokValueModal" class="form-label">Masukkan Jumlah Stok Sebenarnya:</label>
                <input type="number" name="stok" class="form-control" id="stokValueModal" min="0" required autofocus>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
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
                const url = button.getAttribute('data-url');
                const nama = button.getAttribute('data-nama');
                const stok = button.getAttribute('data-stok');
                const modalForm = koreksiStokModal.querySelector('form');
                const modalNamaProduk = koreksiStokModal.querySelector('#namaProdukModal');
                const modalStokInput = koreksiStokModal.querySelector('#stokValueModal');
                modalForm.action = url;
                modalNamaProduk.textContent = nama;
                modalStokInput.value = stok;
            });
        }
    </script>
    @endpush
</x-layout>
