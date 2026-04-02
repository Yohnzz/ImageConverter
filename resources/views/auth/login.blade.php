<x-guest-layout>
    <x-slot name="leftHeadline">
        <h2>Masuk untuk upload tanpa batas guest</h2>
        <p>Guest tetap bisa upload, tapi login memberi kuota lebih besar dan manajemen link yang lebih leluasa.</p>
    </x-slot>

    <x-slot name="leftDots">
        <span class="on"></span>
        <span></span>
        <span></span>
    </x-slot>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert-success">{{ session('status') }}</div>
    @endif

    <div class="form-heading">
        <h1>Login</h1>
        <p>Masuk untuk upload gambar hingga 100 MB per file</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="field">
            <label for="email">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="you@company.com" />
            @error('email')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="field">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            @error('password')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="row-between">
            <label class="check-label">
                <input type="checkbox" name="remember" id="remember_me" />
                Tetap masuk
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="link">Lupa password?</a>
            @endif
        </div>

        <button type="submit" class="btn-primary">Masuk</button>

        <div class="form-footer">
            Belum punya akun? <a href="{{ route('register') }}" class="link">Daftar sekarang</a>
        </div>
    </form>
</x-guest-layout>
