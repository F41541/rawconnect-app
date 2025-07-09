<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-8">
                <div class="card">
                    <div class="card-body p-4">
                        {{-- Form utama kita --}}
                        <form action="{{ route('stok-adj.store') }}" method="POST" id="stokAdjustmentForm">
                            @csrf
                            {{-- Input tersembunyi untuk menandai aksi (tambah/kurang) --}}
                            <input type="hidden" name="action_type" id="action_type">

                            {{-- LANGKAH 1: PILIH TOKO --}}
                            <div class="mb-3">
                                <label for="toko_id" class="form-label fw-bold">1. Pilih Toko</label>
                                <select class="form-select" id="toko_id" name="toko_id" required>
                                    <option value="" selected disabled>-- Pilih Toko --</option>
                                    @foreach ($tokos as $toko)
                                        <option value="{{ $toko->id }}">{{ $toko->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            {{-- LANGKAH 2: PILIH KATEGORI --}}
                            <div class="mb-3">
                                <label for="kategori_id" class="form-label fw-bold">2. Pilih Kategori</label>
                                <select class="form-select" id="kategori_id" name="kategori_id" required disabled>
                                    <option value="">-- Pilih Toko terlebih dahulu --</option>
                                </select>
                            </div>

                            {{-- LANGKAH 3: PILIH JENIS PRODUK --}}
                            <div class="mb-3">
                                <label for="jenis_produk_id" class="form-label fw-bold">3. Pilih Jenis Produk</label>
                                <select class="form-select" id="jenis_produk_id" name="jenis_produk_id" required disabled>
                                    <option value="">-- Pilih Kategori terlebih dahulu --</option>
                                </select>
                            </div>

                            {{-- LANGKAH 4: CARI PRODUK --}}
                            <div class="mb-3">
                                <label for="search-produk" class="form-label fw-bold">4. Cari Produk</label>
                                <select id="search-produk" name="produk_id" placeholder="Ketik nama produk..." required disabled></select>
                            </div>
                            
                            <hr class="my-4">

                            {{-- LANGKAH 5: INPUT JUMLAH & AKSI --}}
                            <div class="row align-items-end">
                                <div class="col-sm-6 mb-3">
                                    <label for="jumlah" class="form-label fw-bold">5. Masukkan Jumlah</label>
                                    <input type="number" class="form-control" id="jumlah" name="jumlah" placeholder="Hanya angka positif" required min="1">
                                </div>
                                <div class="col-sm-6 mb-3 d-flex gap-2">
                                    {{-- Tombol Aksi --}}
                                    <button type="submit" class="btn btn-warning w-100 btn-aksi" data-action="kurangi">
                                        <i class="bi bi-dash-lg"></i> Kurangi dari Stok
                                    </button>
                                    <button type="submit" class="btn btn-success w-100 btn-aksi" data-action="tambah">
                                        <i class="bi bi-plus-lg"></i> Tambah ke Stok
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Kita letakkan semua CSS & JS yang dibutuhkan di sini --}}
    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
    @endpush

    @push('scripts')
        {{-- Memuat library TomSelect --}}
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // --- BAGIAN SETUP AWAL ---
                const tokoSelect = document.getElementById('toko_id');
                const kategoriSelect = document.getElementById('kategori_id');
                const jenisProdukSelect = document.getElementById('jenis_produk_id');
                const actionInput = document.getElementById('action_type');
                const oldInput = @json(session()->getOldInput());

                // Fungsi untuk mereset dropdown
                function resetSelect(select, placeholder) {
                    select.innerHTML = `<option value="">-- ${placeholder} --</option>`;
                    select.disabled = true;
                }

                // --- PERBAIKAN UTAMA: Konfigurasi TomSelect yang LENGKAP ---
                let produkTomSelect = new TomSelect('#search-produk', {
                    valueField: 'id',
                    labelField: 'text',
                    searchField: 'text',
                    // Ini adalah 'mesin' yang hilang, sekarang sudah ada kembali
                    load: function(query, callback) {
                        if (!query.length) return callback();
                        const url = `{{ route('api.stok-adj.search_produk') }}?toko_id=${tokoSelect.value}&jenis_produk_id=${jenisProdukSelect.value}&q=${encodeURIComponent(query)}`;
                        fetch(url)
                            .then(response => response.json())
                            .then(json => callback(json))
                            .catch(() => callback());
                    },
                    loadingClass: 'loading',
                });
                produkTomSelect.disable(); // Tetap nonaktif di awal

                // --- BAGIAN LOGIKA EVENT LISTENER (Sudah Benar) ---

                // 1. Saat TOKO dipilih
                tokoSelect.addEventListener('change', function() {
                    resetSelect(kategoriSelect, 'Memuat Kategori...');
                    resetSelect(jenisProdukSelect, 'Pilih Kategori dahulu');
                    produkTomSelect.clear();
                    produkTomSelect.disable();

                    const tokoId = this.value;
                    if (!tokoId) return;

                    fetch(`{{ route('api.stok-adj.get_kategori_by_toko') }}?toko_id=${tokoId}`)
                        .then(response => response.json())
                        .then(data => {
                            kategoriSelect.innerHTML = '<option value="" selected disabled>-- Pilih Kategori --</option>';
                            data.forEach(kategori => {
                                kategoriSelect.innerHTML += `<option value="${kategori.id}">${kategori.name}</option>`;
                            });
                            kategoriSelect.disabled = false;
                            
                            if (oldInput && oldInput.kategori_id) {
                                kategoriSelect.value = oldInput.kategori_id;
                                kategoriSelect.dispatchEvent(new Event('change'));
                            }
                        });
                });

                // 2. Saat KATEGORI dipilih
                kategoriSelect.addEventListener('change', function() {
                    resetSelect(jenisProdukSelect, 'Memuat Jenis Produk...');
                    produkTomSelect.clear();
                    produkTomSelect.disable();

                    const tokoId = tokoSelect.value;
                    const kategoriId = this.value;
                    if (!kategoriId) return;

                    fetch(`{{ route('api.stok-adj.get_jenis_produk_by_filters') }}?toko_id=${tokoId}&kategori_id=${kategoriId}`)
                        .then(response => response.json())
                        .then(data => {
                            jenisProdukSelect.innerHTML = '<option value="" selected disabled>-- Pilih Jenis Produk --</option>';
                            data.forEach(jenis => {
                                jenisProdukSelect.innerHTML += `<option value="${jenis.id}">${jenis.name}</option>`;
                            });
                            jenisProdukSelect.disabled = false;
                            
                            if (oldInput && oldInput.jenis_produk_id) {
                                jenisProdukSelect.value = oldInput.jenis_produk_id;
                                jenisProdukSelect.dispatchEvent(new Event('change'));
                            }
                        });
                });
                
                // 3. Saat JENIS PRODUK dipilih
                jenisProdukSelect.addEventListener('change', function() {
                    produkTomSelect.clear();
                    produkTomSelect.enable();
                });

                // 4. Tombol Aksi
                document.querySelectorAll('.btn-aksi').forEach(button => {
                    button.addEventListener('click', function() {
                        actionInput.value = this.getAttribute('data-action');
                    });
                });

                // 5. Pemicu saat halaman dimuat
                if (oldInput && oldInput.toko_id) {
                    tokoSelect.value = oldInput.toko_id;
                    tokoSelect.dispatchEvent(new Event('change'));
                }
            });
        </script>
    @endpush


</x-layout>
