<x-guest-layout>
    <h5 class="text-center fw-bold mb-4">Selamat Datang Kembali</h5>

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-4">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="d-grid shadow-sm">
            <button type="submit" class="btn btn-primary shadow-sm">Login</button>
        </div>
    </form>
    <div class="mt-3 text-center">
        <a href="{{ route('password.request') }}">Lupa Password?</a>
    </div>

</x-guest-layout>