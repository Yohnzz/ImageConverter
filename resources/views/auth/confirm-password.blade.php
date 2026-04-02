<x-guest-layout>
    <x-slot name="leftHeadline">
        <h2>One extra step for your security</h2>
        <p>Re-enter your password to access this protected area of the application.</p>
    </x-slot>

    <x-slot name="leftDots">
        <span class="on"></span>
        <span></span>
        <span></span>
    </x-slot>

    <div class="form-heading">
        <h1>Verify it's you</h1>
        <p>Confirm your password to continue</p>
    </div>

    <div class="alert-info">
        This area requires additional verification. Please re-enter your current password to proceed.
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="field">
            <label for="password">Current password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            @error('password')
                <div class="field-error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-primary">Verify &amp; continue</button>
    </form>
</x-guest-layout>
