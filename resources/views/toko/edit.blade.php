<x-layout>
    <x-slot:title>{{ $title }}</x-slot:title>

    <div class="container py-1">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Edit Toko: {{ $toko->name }}</div>
                    <div class="card-body">
                        <form action="{{ route('superadmin.toko.update', $toko->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            {{-- NAMA TOKO --}}
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Toko</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $toko->name) }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- LOGO TOKO --}}
                            <div class="mb-3">
                                <label for="logo" class="form-label">Ganti Logo Toko</label>
                                @if($toko->logo)
                                <div class="mb-2">
                                    <p class="mb-1 fw-bold"><small>Logo Saat Ini:</small></p>
                                    <img src="{{ asset('uploads/logo_toko/' . $toko->logo) }}" alt="Logo saat ini" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px;">
                                </div>
                                @endif
                                <input class="form-control @error('logo') is-invalid @enderror" type="file" id="logo" name="logo">
                                <div class="form-text">Kosongkan jika tidak ingin mengganti logo.</div>
                                @error('logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <a href="{{ route('superadmin.toko.index') }}" class="btn btn-secondary me-2">Batal</a>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
