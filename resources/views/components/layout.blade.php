<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Raw Connect</title>
    
    {{-- Menghapus semua link CSS manual. --}}
    {{-- Vite sekarang menjadi satu-satunya yang bertanggung jawab untuk memuat SEMUA CSS dan JS. --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Slot ini tetap ada untuk style tambahan yang spesifik per halaman --}}
    @stack('styles')

</head>
<body class="bg-light">
    {{-- Komponen Sidebar dan Header --}}
    <x-sidebar />
    <x-header>
        {{ $title ?? 'Judul Halaman' }}
    </x-header>

    {{-- Konten Utama Halaman --}}
    <div class="container py-4">
        {{ $slot }}
    </div>

    {{-- Menghapus semua <script src="..."> manual dari sini. --}}
    {{-- Semua library (jQuery, Bootstrap JS, Toastr JS, script.js) --}}
    {{-- sekarang di-bundle oleh Vite melalui resources/js/app.js --}}

    {{-- Skrip inline untuk menampilkan notifikasi dari session Laravel --}}
    @if (session('success') || session('error'))
        <script>
            // Membuat variabel global yang bisa dibaca oleh app.js
            window.sessionFlash = {
                status: "{{ session('success') ? 'success' : 'error' }}",
                message: "{{ session('success') ?? session('error') }}"
            };
        </script>
    @endif
    
    {{-- Slot ini tetap ada untuk skrip tambahan yang spesifik per halaman --}}
    @stack('scripts')

</body>
</html>