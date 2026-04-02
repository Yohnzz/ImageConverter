<x-guest-layout>
    <x-slot name="leftHeadline">
        <h2>Happens to the best of us</h2>
        <p>Enter your email and we'll send you a secure link to reset your password right away.</p>
    </x-slot>

    <x-slot name="leftDots">
        <span></span>
        <span></span>
        <span class="on"></span>
    </x-slot>

    @if (session('status'))
        <div class="alert-success">{{ session('status') }}</div>
    @endif

    <div class="form-heading">
        <h1>Reset password</h1>
        <p>We'll email you a link to reset your password</p>
    </div>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="field">
            <label for="email">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="you@company.com" />
            @error('email')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-primary">Send reset link</button>

        <div class="divider"></div>

        <div class="form-footer">
            Remembered it? <a href="{{ route('login') }}" class="link">Back to sign in</a>
        </div>
    </form>
</x-guest-layout>
