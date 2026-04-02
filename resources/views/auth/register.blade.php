<x-guest-layout>
    <x-slot name="leftHeadline">
        <h2>Join thousands of teams already using Nexus</h2>
        <p>Set up your account in under two minutes and start collaborating today.</p>
    </x-slot>

    <x-slot name="leftDots">
        <span></span>
        <span class="on"></span>
        <span></span>
    </x-slot>

    <div class="form-heading">
        <h1>Create account</h1>
        <p>Fill in your details to get started</p>
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

        <button type="submit" class="btn-primary">Create account</button>

        <div class="form-footer">
            Already have an account? <a href="{{ route('login') }}" class="link">Sign in</a>
        </div>
    </form>
</x-guest-layout>
