{{-- resources/views/pengiriman/partials/paket-list.blade.php --}}

@forelse ($pakets as $paket)
    <div class="card mb-3 shadow-sm">
        {{-- Bagian Header Kartu (Sesuai kode Anda) --}}
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="fw-bold d-flex align-items-center gap-1">
                <img src="{{ optional($paket->toko)->logo ? asset('uploads/logo_toko/' . $paket->toko->logo) : asset('images/no-image.png') }}" alt="Logo {{ optional($paket->toko)->name }}" title="{{ optional($paket->toko)->name }}" style="width: 30px; height: 30px; object-fit: contain;">
                <span class="ms-1">{{ optional($paket->toko)->name }}</span>
                <span class="text-muted mx-2">|</span>
                <span>{{ optional($paket->merchant)->name }}</span>
                <span class="text-muted mx-2">|</span>
                <span>{{ optional($paket->ekspedisi)->name }}</span>
            </div>
        </div>

        {{-- Bagian Detail Item --}}
        <div class="card-body p-0">
            @php
                $itemsGroupedByJenis = $paket->items->groupBy(function($item) {
                    return optional(optional($item->produk)->jenisProduk)->name ?? 'Lain-lain';
                });
            @endphp

            <ul class="list-group list-group-flush">
                @foreach ($itemsGroupedByJenis as $jenisNama => $itemsInGroup)
                    <li class="list-group-item px-3 py-2" style="background-color: #f8f9fa;">
                        <strong class="text-dark-emphasis">{{ $jenisNama }}</strong>
                    </li>

                    @php
                        $firstItemInGroup = $itemsInGroup->first();
                        $restOfItemsInGroup = $itemsInGroup->slice(1);
                        $collapseId = 'paket-' . $paket->id. '-jenis-' . Str::slug($jenisNama);
                    @endphp

                    {{-- Tampilkan item pertama --}}
                    @if($firstItemInGroup)
                        <li class="list-group-item d-flex justify-content-between align-items-center ps-4">
                            <div>
                                <span>{{ optional($firstItemInGroup->produk)->nama ?? 'Produk Telah Dihapus' }}</span>
                                @if($firstItemInGroup->deskripsi_varian)
                                    <small class="text-primary fw-bold d-block">{{ $firstItemInGroup->deskripsi_varian }}</small>
                                @endif
                            </div>
                            <span class="badge bg-primary rounded-pill">x {{ $firstItemInGroup->jumlah }}</span>
                        </li>
                    @endif
                    
                    {{-- Hanya jika ada item sisa, tampilkan sisanya dan tombol --}}
                    @if($restOfItemsInGroup->isNotEmpty())
                        {{-- PERBAIKAN TATA LETAK: Bungkus sisa item dalam <li> yang berisi <div> collapse --}}
                        <li class="list-group-item p-0 border-0">
                            <div class="collapse" id="{{ $collapseId }}">
                                <ul class="list-group list-group-flush">
                                    @foreach($restOfItemsInGroup as $item)
                                        <li class="list-group-item d-flex justify-content-between align-items-center ps-4">
                                            <div>
                                                <span>{{ optional($item->produk)->nama ?? 'Produk Telah Dihapus' }}</span>
                                                @if($item->deskripsi_varian)
                                                    <small class="text-primary fw-bold d-block">{{ $item->deskripsi_varian }}</small>
                                                @endif
                                            </div>
                                            <span class="badge bg-primary rounded-pill">x {{ $item->jumlah }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </li>
                        
                        {{-- PERBAIKAN TATA LETAK: Tombol diletakkan di <li> terpisah di paling bawah --}}
                        <li class="list-group-item ps-4 py-2">
                            <a class="btn btn-sm btn-link text-decoration-none p-0 collapse-trigger" data-bs-toggle="collapse" href="#{{ $collapseId }}" role="button" aria-expanded="false">
                                <span class="collapse-text">Lihat {{ $restOfItemsInGroup->count() }} item lainnya</span>
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
        
        {{-- Bagian Footer Kartu --}}
        <div class="card-footer text-muted d-flex justify-content-between align-items-center">
             <small>
                {{ $paket->created_at->format('H:i') }} | {{ optional($paket->user)->name ?? 'N/A' }}
            </small>
            <div>
                <form action="{{ route('paket.updateStatus', $paket->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <select name="status" class="form-select form-select-sm {{ $paket->status == 'proses' ? 'status-proses' : ($paket->status == 'selesai' ? 'status-selesai' : 'status-dibatalkan') }}" onchange="this.form.submit()" style="width: 130px;">
                        <option value="proses" {{ $paket->status == 'proses' ? 'selected' : '' }}>Proses</option>
                        <option value="selesai" {{ $paket->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        
                        {{-- PERBAIKAN HAK AKSES: Menggunakan Gate 'batalkan-pengiriman' yang lebih spesifik --}}
                        @can('cancel-shipments')
                            <option value="dibatalkan" {{ $paket->status == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        @endcan
                    </select>
                </form>
            </div>
        </div>
    </div>
@empty
    <div class="alert alert-light text-center border-0">
        <i class="bi bi-box-seam fs-4 d-block mb-2"></i>
        Tidak ada paket pengiriman dengan status ini
    </div>
@endforelse