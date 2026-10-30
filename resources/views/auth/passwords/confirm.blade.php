@extends('layouts.app')

@section('body_class', 'is-auth')

@section('content')
<x-auth-shell
    heading="Konfirmasi Kata Sandi"
    subheading="Harap konfirmasi kata sandi Anda sebelum melanjutkan."
    aside-text="Langkah ini memastikan hanya Anda yang dapat mengakses bagian sensitif akun."
>
    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf

        <div>
            <label for="password" class="tu-label">{{ __('Kata Sandi') }}</label>
            <x-password-input id="password" name="password" autocomplete="current-password" :invalid="$errors->has('password')" :autofocus="true" />
            @error('password')
                <p class="tu-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="tu-btn tu-btn--block">
            <span>{{ __('Konfirmasi') }}</span>
            <span class="arr" aria-hidden="true">&rarr;</span>
        </button>

        @if (Route::has('password.request'))
            <p class="text-center">
                <a href="{{ route('password.request') }}" class="tu-link text-xs text-gray-500">{{ __('Lupa kata sandi Anda?') }}</a>
            </p>
        @endif
    </form>
</x-auth-shell>
@endsection
