<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Raw Connect</title>
        
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            /* Style kustom untuk memastikan layout di tengah, anti-gagal */
            html, body {
                height: 100%;
            }
            body {
                display: flex;
                align-items: center;
                justify-content: center;
                padding-top: 40px;
                padding-bottom: 40px;
                padding-left: 1rem;  /* Tambahkan ini (sekitar 16px) */
                padding-right: 1rem; /* Tambahkan ini (sekitar 16px) */
                background-color:rgb(117, 208, 112) !important;
            }
            .auth-card {
                width: 100%;
                max-width: 420px;
            }
        </style>
    </head>
    <body>
        <div class="auth-card">
            {{-- Kartu untuk membungkus SEMUANYA, logo dan form --}}
            <div class="card shadow border-0 rounded-4">
                {{-- Logo diletakkan di dalam card-header agar rapi --}}
                <div class="card-header bg-transparent text-center border-0 pt-4">
                     <a href="/">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo Raw Connect" style="width: 100px;">
                    </a>
                </div>
                {{-- Padding atas (pt-0) dikurangi karena sudah ada padding dari header --}}
                <div class="card-body p-4 p-lg-5 pt-0">
                    {{-- Form login/register dari Breeze akan ditampilkan di sini --}}
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>