<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="container py-4">
        <div class="row justify-content-center align-items-center">
                <div class="card">
                    <div class="card-body p-4">
                        <form id="pengirimanForm" action="{{ route('pengiriman.tambah') }}" method="POST">
                            @csrf
                            <input type="hidden" name="action_type" id="action_type" value="pratinjau">

                            {{-- ===== BAGIAN DETAIL PENGIRIMAN ===== --}}
                            <fieldset>
                                <legend class="h6 mb-3 fw-bold border-bottom pb-2">Langkah 1: Detail Pengiriman</legend>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label for="toko_id" class="form-label">Pilih Toko</label>
                                        <select class="form-select" id="toko_id" name="toko_id" required>
                                            <option value="" selected disabled>-- Pilih Toko --</option>
                                            @foreach ($tokos as $toko)
                                                <option value="{{ $toko->id }}">{{ $toko->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="merchant_id" class="form-label">Pilih Merchant</label>
                                        <select class="form-select" id="merchant_id" name="merchant_id" required disabled>
                                            <option value="">-- Pilih Toko dahulu --</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="ekspedisi_id" class="form-label">Pilih Ekspedisi</label>
                                        <select class="form-select" id="ekspedisi_id" name="ekspedisi_id" required disabled>
                                            <option value="">-- Pilih Merchant dahulu --</option>
                                        </select>
                                    </div>
                                </div>
                            </fieldset>

                            <hr class="my-4">

                            {{-- ===== BAGIAN DETAIL PRODUK ===== --}}
                            <fieldset>
                                <legend class="h6 mb-3 fw-bold border-bottom pb-2">Langkah 2: Detail Produk</legend>
                                <div class="mb-3">
                                    <label for="jenis_produk_id" class="form-label">Pilih Jenis Produk</label>
                                    <select class="form-select" id="jenis_produk_id" name="jenis_produk_id" required disabled>
                                        <option value="">-- Pilih Kategori dahulu --</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="search-produk" class="form-label">Cari Nama Produk</label>
                                    <select id="search-produk" name="produk_id" placeholder="Pilih semua filter di atas, lalu ketik..." required disabled></select>
                                </div>
                                <div class="mb-3">
                                    <label for="berat_varian" class="form-label">Berat/Varian</label>
                                    <input type="number" step="any" class="form-control" name="berat_varian" id="berat_varian" placeholder="Hanya aktif untuk gram" disabled>
                                    <div class="form-text">Input ini hanya aktif jika produk yang dipilih satuannya 'gram'.</div>
                                </div>
                                <div class="mb-4">
                                    <label for="jumlah" class="form-label d-block text-center">Jumlah</label>
                                    <div class="d-flex justify-content-center">
                                        <div style="max-width: 200px;">
                                            <div class="input-group">
                                                <button class="btn btn-outline-secondary" type="button" onclick="decrement()">-</button>
                                                <input type="number" class="form-control text-center no-spinner" name="jumlah" id="jumlah" value="{{ old('jumlah', 1) }}" min="1" required>
                                                <button class="btn btn-outline-secondary" type="button" onclick="increment()">+</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            <div class="row mt-4">
                                <div class="col-12 mb-2">
                                    <button type="submit" class="btn btn-primary btn-aksi w-100 d-flex align-items-center justify-content-center" data-action="pratinjau">
                                        <i class="bi bi-plus-circle me-2"></i>
                                        <span class="align-middle">Tambah ke Pratinjau</span>
                                    </button>
                                </div>
                                <div class="col-6 mb-2 d-flex">
                                    <a href="{{ route('pengiriman.pratinjau') }}" class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center">
                                        <i class="bi bi-eye me-2"></i>
                                        <span class="align-middle">Lihat Pratinjau</span>
                                    </a>
                                </div>
                                <div class="col-6 mb-2 d-flex">
                                    <button type="submit" class="btn btn-success btn-aksi w-100 d-flex align-items-center justify-content-center" data-action="langsung">
                                        <i class="bi bi-send me-2"></i>
                                        <span class="align-middle">Kirim</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
        </div>
    </div>

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const tokoSelect = document.getElementById('toko_id');
                const merchantSelect = document.getElementById('merchant_id');
                const ekspedisiSelect = document.getElementById('ekspedisi_id');
                const kategoriSelect = document.getElementById('kategori_id');
                const jenisProdukSelect = document.getElementById('jenis_produk_id');
                const jumlahInput = document.getElementById('jumlah');
                const actionInput = document.getElementById('action_type');
                const oldInput = @json(session()->getOldInput());

                function resetSelect(select, placeholder) {
                    select.innerHTML = `<option value="">-- ${placeholder} --</option>`;
                    select.disabled = true;
                }

                let produkTomSelect = new TomSelect('#search-produk', {
                    valueField: 'id', labelField: 'text', searchField: 'text',
                    load: function(query, callback) {
                        if (!query.length || !tokoSelect.value || !jenisProdukSelect.value) return callback();
                        const url = `{{ route('api.pengiriman.search_produk') }}?toko_id=${tokoSelect.value}&jenis_produk_id=${jenisProdukSelect.value}&q=${encodeURIComponent(query)}`;
                        fetch(url).then(r => r.json()).then(j => callback(j)).catch(() => callback());
                    },
                });
                produkTomSelect.disable();

                // 1. Saat TOKO dipilih
                tokoSelect.addEventListener('change', function() {
                    const tokoId = this.value;

                    resetSelect(merchantSelect, 'Memuat...');
                    resetSelect(ekspedisiSelect, 'Pilih Merchant dahulu');
                    resetSelect(jenisProdukSelect, 'Memuat...');
                    produkTomSelect.clear();
                    produkTomSelect.disable();

                    if (!tokoId) return;

                    // Fetch data untuk Merchant
                    fetch(`{{ route('api.pengiriman.get_merchants') }}?toko_id=${tokoId}`).then(r=>r.json()).then(data=>{
                        merchantSelect.innerHTML = '<option value="" selected disabled>-- Pilih Merchant --</option>';
                        data.forEach(item => { merchantSelect.innerHTML += `<option value="${item.id}">${item.name}</option>`; });
                        merchantSelect.disabled = false;
                        if(oldInput && oldInput.merchant_id){ merchantSelect.value = oldInput.merchant_id; merchantSelect.dispatchEvent(new Event('change')); }
                    });

                    // Fetch data untuk Jenis Produk (langsung, tanpa perlu kategori)
                    fetch(`{{ route('api.pengiriman.get_jenis_produk_by_filters') }}?toko_id=${tokoId}`).then(r=>r.json()).then(data=>{
                        jenisProdukSelect.innerHTML = '<option value="" selected disabled>-- Pilih Jenis Produk --</option>';
                        data.forEach(item => { jenisProdukSelect.innerHTML += `<option value="${item.id}">${item.name}</option>`; });
                        jenisProdukSelect.disabled = false;
                        if(oldInput && oldInput.jenis_produk_id){ jenisProdukSelect.value = oldInput.jenis_produk_id; jenisProdukSelect.dispatchEvent(new Event('change')); }
                    });
                });

                // 2. Saat MERCHANT dipilih
                merchantSelect.addEventListener('change', function() {
                    const tokoId = tokoSelect.value;
                    const merchantId = this.value;
                    resetSelect(ekspedisiSelect, 'Memuat...');
                    if (!merchantId) return;
                    fetch(`{{ route('api.pengiriman.get_ekspedisis') }}?toko_id=${tokoId}&merchant_id=${merchantId}`).then(r=>r.json()).then(data=>{
                        ekspedisiSelect.innerHTML = '<option value="" selected disabled>-- Pilih Ekspedisi --</option>';
                        data.forEach(item => { ekspedisiSelect.innerHTML += `<option value="${item.id}">${item.name}</option>`; });
                        ekspedisiSelect.disabled = false;
                        if(oldInput && oldInput.ekspedisi_id){ ekspedisiSelect.value = oldInput.ekspedisi_id; }
                    });
                });

                
                // 4. Saat JENIS PRODUK dipilih
                jenisProdukSelect.addEventListener('change', function() {
                    produkTomSelect.clear();
                    produkTomSelect.enable();
                });

                // 5. Tombol +/-
                window.increment = function() { jumlahInput.value = parseInt(jumlahInput.value, 10) + 1; };
                window.decrement = function() { const val = parseInt(jumlahInput.value, 10); if (val > 1) { jumlahInput.value = val - 1; }};

                // 6. Berat/Varian
                produkTomSelect.on('change', function(value) {
                const beratInput = document.getElementById('berat_varian');
                
                // Jika tidak ada produk dipilih, reset dan nonaktifkan
                if (!value) {
                    beratInput.disabled = true;
                    beratInput.placeholder = 'Pilih produk dahulu';
                    beratInput.value = '';
                    return;
                }
                
                // Ambil data dari produk yang dipilih
                const selectedData = this.options[value];
                const satuan = selectedData.satuan || '';

                // Logika inti: Hanya aktifkan untuk 'gram'
                if (satuan.toLowerCase() === 'gram') {
                    beratInput.disabled = false;
                    beratInput.placeholder = 'Contoh: 100';
                    beratInput.focus();
                } else {
                    beratInput.disabled = true;
                    beratInput.placeholder = 'Hanya untuk gram';
                    beratInput.value = '';
                }
            });
                // 7. Tombol Aksi
                document.querySelectorAll('.btn-aksi').forEach(button => {
                    button.addEventListener('click', function() {
                        actionInput.value = this.getAttribute('data-action');
                    });
                });
                
                // PEMICU AWAL
                if (oldInput && oldInput.toko_id) {
                    tokoSelect.value = oldInput.toko_id;
                    tokoSelect.dispatchEvent(new Event('change'));
                }
            });
        </script>
    @endpush
</x-layout>
