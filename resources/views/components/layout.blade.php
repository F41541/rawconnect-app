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
    <div class="container py-2">
        {{ $slot }}
    </div>

    {{-- Menghapus semua <script src="..."> manual dari sini. --}}
    {{-- Semua library (jQuery, Bootstrap JS, Toastr JS, script.js) --}}
    {{-- sekarang di-bundle oleh Vite melalui resources/js/app.js --}}
    {{-- Jembatan data HANYA untuk notifikasi --}}
    <div id="page-data" 
        {{-- Data untuk Notifikasi Toastr --}}
        @if(session('success') || session('error'))
        data-session-status="{{ session('success') ? 'success' : 'error' }}"
        data-session-message="{{ session('success') ?? session('error') }}"
        @endif

        {{-- Data untuk Grafik (HANYA AKAN ADA JIKA DIKIRIM DARI CONTROLLER) --}}
        @isset($data['chartLabels'])
            data-sales-chart-labels='@json($data['chartLabels'])'
            data-sales-chart-values='@json($data['chartData'])'
        @endisset
        @isset($data['stockChartLabels'])
            data-stock-chart-labels='@json($data['stockChartLabels'])'
            data-stock-chart-masuk='@json($data['stockMasukData'])'
            data-stock-chart-keluar='@json($data['stockKeluarData'])'
        @endisset
        @isset($data['merchantLabels'])
            data-merchant-chart-labels='@json($data['merchantLabels'])'
            data-merchant-chart-data='@json($data['merchantData'])'
        @endisset
        
        style="display: none;">
    </div>
    
    {{-- Slot ini tetap ada untuk skrip tambahan yang spesifik per halaman --}}
    @stack('scripts')

</body>
</html>