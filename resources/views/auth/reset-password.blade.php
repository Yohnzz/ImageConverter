<x-guest-layout>
    <x-slot name="leftHeadline">
        <h2>Choose a strong new password</h2>
        <p>Use a mix of uppercase, numbers, and symbols for the best protection.</p>
    </x-slot>

    <x-slot name="leftDots">
        <span></span>
        <span></span>
        <span class="on"></span>
    </x-slot>

    <div class="form-heading">
        <h1>New password</h1>
        <p>Create a new password for your account</p>
    </div>

    <div class="secure-badge">
        <span class="dot"></span>
        secure · token verified
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="field">
            <label for="email">Email address</label>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" placeholder="you@company.com" />
            @error('email')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="field">
            <label for="password">New password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Min. 8 characters" />
            @error('password')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="field">
            <label for="password_confirmation">Confirm new password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat new password" />
            @error('password_confirmation')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-primary">Update password</button>
    </form>
</x-guest-layout>
