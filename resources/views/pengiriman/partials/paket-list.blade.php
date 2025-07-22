{{-- resources/views/pengiriman/partials/paket-list.blade.php --}}

@forelse ($pakets as $paket)
    <div class="card mb-3 shadow-sm rounded-4 border" style="background: #f6f8fa;">
        {{-- Header --}}
        <div class="card-header d-flex justify-content-between align-items-center bg-white rounded-top-4 border-bottom-0">
            <div class="fw-bold d-flex align-items-center gap-2">
                <img src="{{ optional($paket->toko)->logo ? asset('uploads/logo_toko/' . $paket->toko->logo) : asset('images/no-image.png') }}" alt="Logo {{ optional($paket->toko)->name }}" title="{{ optional($paket->toko)->name }}" class="rounded-circle border" style="width: 36px; height: 36px; object-fit: contain;">
                <span class="ms-2">{{ optional($paket->toko)->name }}</span>
                <span class="text-muted mx-2">|</span>
                <span>{{ optional($paket->merchant)->name }}</span>
                <span class="text-muted mx-2">|</span>
                <span>{{ optional($paket->ekspedisi)->name }}</span>
            </div>
            <span class="badge 
                @if($paket->status == 'proses') bg-warning
                @elseif($paket->status == 'selesai') bg-success 
                @elseif($paket->status == 'dibatalkan') bg-danger 
                @else bg-secondary @endif
                text-uppercase px-3 py-2 fs-8">
                {{ ucfirst($paket->status) }}
            </span>
        </div>

        {{-- Detail Item --}}
        <div class="card-body p-0">
            @php
                $itemsGroupedByJenis = $paket->items->groupBy(function($item) {
                    return optional(optional($item->produk)->jenisProduk)->name ?? 'Lain-lain';
                });
            @endphp

            <ul class="list-group list-group-flush">
                @foreach ($itemsGroupedByJenis as $jenisNama => $itemsInGroup)
                    <li class="list-group-item px-3 py-2 bg-light border-0 rounded-top-3">
                        <strong class="text-dark-emphasis">{{ $jenisNama }}</strong>
                    </li>

                    @php
                        $firstItemInGroup = $itemsInGroup->first();
                        $restOfItemsInGroup = $itemsInGroup->slice(1);
                        $collapseId = 'paket-' . $paket->id. '-jenis-' . Str::slug($jenisNama);
                    @endphp

                    {{-- Tampilkan item pertama --}}
                    @if($firstItemInGroup)
                        <li class="list-group-item d-flex justify-content-between align-items-center ps-4 border-0">
                            <div>
                                <span class="fw-semibold">{{ optional($firstItemInGroup->produk)->nama ?? 'Produk Telah Dihapus' }}</span>
                                @if($firstItemInGroup->deskripsi_varian)
                                    <small class="text-primary fw-bold d-block">{{ $firstItemInGroup->deskripsi_varian }}</small>
                                @endif
                            </div>
                            <span class="badge bg-primary rounded-pill shadow-sm">x {{ $firstItemInGroup->jumlah }}</span>
                        </li>
                    @endif
                    
                    {{-- Sisa item --}}
                    @if($restOfItemsInGroup->isNotEmpty())
                        <li class="list-group-item p-0 border-0">
                            <div class="collapse" id="{{ $collapseId }}">
                                <ul class="list-group list-group-flush">
                                    @foreach($restOfItemsInGroup as $item)
                                        <li class="list-group-item d-flex justify-content-between align-items-center ps-4 border-0">
                                            <div>
                                                <span class="fw-semibold">{{ optional($item->produk)->nama ?? 'Produk Telah Dihapus' }}</span>
                                                @if($item->deskripsi_varian)
                                                    <small class="text-primary fw-bold d-block">{{ $item->deskripsi_varian }}</small>
                                                @endif
                                            </div>
                                            <span class="badge bg-primary rounded-pill shadow-sm">x {{ $item->jumlah }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </li>
                        <li class="list-group-item ps-4 py-2 border-0">
                            <a class="btn btn-sm btn-link text-decoration-none p-0 collapse-trigger fw-bold" data-bs-toggle="collapse" href="#{{ $collapseId }}" role="button" aria-expanded="false">
                                <span class="collapse-text">Lihat {{ $restOfItemsInGroup->count() }} item lainnya</span>
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
        
        {{-- Footer --}}
        <div class="card-footer bg-light rounded-bottom-4 d-flex justify-content-between align-items-center">
            <small>
                {{ $paket->created_at->format('H:i') }} | {{ optional($paket->user)->name ?? 'N/A' }}
            </small>
            <div>
                @if ($paket->status == 'proses')
                    @can('cancel-shipment')
                        <form action="{{ route('paket.updateStatus', $paket->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="dibatalkan">
                            <button type="submit" class="btn btn-sm btn-danger rounded-3 align-items-center"><i class="bi bi-x-circle fs-6"></i> Batalkan</button>
                        </form>
                    @endcan
                    @can('update-status')
                        <form action="{{ route('paket.updateStatus', $paket->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="selesai">
                            <button type="submit" class="btn btn-sm btn-success rounded-3 px-3"><i class="bi bi-check-circle fs-6"></i> Selesaikan</button>
                        </form>
                    @endcan
                @elseif ($paket->status == 'selesai')
                    @can('cancel-shipment')
                        <form action="{{ route('paket.updateStatus', $paket->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="dibatalkan">
                            <button type="submit" class="btn btn-sm btn-danger rounded-3 px-3"><i class="bi bi-x-circle fs-6"></i> Batalkan</button>
                        </form>
                    @endcan
                @elseif ($paket->status == 'dibatalkan')
                    @can('cancel-shipment')
                        <form action="{{ route('paket.updateStatus', $paket->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="proses">
                            <button type="submit" class="btn btn-sm btn-secondary rounded-3 px-3"><i class="bi bi-arrow-repeat fs-6"></i> Kembali ke Proses</button>
                        </form>
                    @endcan
                @endif
            </div>
        </div>
    </div>
@empty
    <div class="alert alert-light text-center border-0 rounded-4 shadow-sm">
        <i class="bi bi-box-seam fs-4 d-block mb-2"></i>
        Tidak ada paket pengiriman dengan status ini
    </div>
@endforelse
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.collapse-trigger').forEach(function (trigger) {
        var collapseId = trigger.getAttribute('href').replace('#', '');
        var collapseElem = document.getElementById(collapseId);
        var collapseText = trigger.querySelector('.collapse-text');
        var originalText = collapseText.textContent;
        var lessText = 'Lihat lebih sedikit';

        collapseElem.addEventListener('show.bs.collapse', function () {
            collapseText.textContent = lessText;
        });
        collapseElem.addEventListener('hide.bs.collapse', function () {
            collapseText.textContent = originalText;
        });
    });
});
</script>
@endpush