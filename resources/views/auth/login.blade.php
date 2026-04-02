<x-guest-layout>
    <x-slot name="leftHeadline">
        <h2>Welcome back to your workspace</h2>
        <p>Manage your projects, collaborate with your team, and track progress — all in one place.</p>
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
        <h1>Sign in</h1>
        <p>Enter your credentials to access your account</p>
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
                Keep me signed in
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="link">Forgot password?</a>
            @endif
        </div>

        <button type="submit" class="btn-primary">Continue</button>

        <div class="form-footer">
            Don't have an account? <a href="{{ route('register') }}" class="link">Create one</a>
        </div>
    </form>
</x-guest-layout>
