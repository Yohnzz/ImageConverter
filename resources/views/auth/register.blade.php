<x-guest-layout>
    <x-slot name="leftHeadline">
        <h2>Bikin akun biar upload makin lega</h2>
        <p>Akun login mendapatkan batas ukuran upload sampai 100 MB per gambar.</p>
    </x-slot>

    <x-slot name="leftDots">
        <span></span>
        <span class="on"></span>
        <span></span>
    </x-slot>

    <div class="form-heading">
        <h1>Daftar</h1>
        <p>Buat akun baru untuk mengelola semua link gambar</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="field">
            <label for="name">Full name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Alex Johnson" />
            @error('name')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="field">
            <label for="email">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="you@company.com" />
            @error('email')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="field">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Min. 8 characters" />
            @error('password')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="field">
            <label for="password_confirmation">Confirm password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat password" />
            @error('password_confirmation')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-primary">Buat akun</button>

        <div class="form-footer">
            Sudah punya akun? <a href="{{ route('login') }}" class="link">Login</a>
        </div>
    </form>
</x-guest-layout>
