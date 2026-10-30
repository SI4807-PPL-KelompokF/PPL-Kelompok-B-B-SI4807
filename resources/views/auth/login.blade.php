@extends('layouts.app')

@section('body_class', 'is-auth')

@section('content')
<x-auth-shell
    heading="Masuk ke Telyuji"
    subheading="Masuk dengan akun civitas akademika atau laboran."
>
    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="tu-label">{{ __('Alamat Email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                class="tu-input @error('email') is-invalid @enderror">
            @error('email')
                <p class="tu-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="tu-label !mb-0">{{ __('Kata Sandi') }}</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="tu-link text-xs text-gray-500">{{ __('Lupa kata sandi?') }}</a>
                @endif
            </div>
            <x-password-input id="password" name="password" autocomplete="current-password" :invalid="$errors->has('password')" />
            @error('password')
                <p class="tu-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <label for="remember" class="flex items-center gap-2.5 cursor-pointer text-xs text-gray-600">
            <input id="remember" type="checkbox" name="remember" class="tu-check" {{ old('remember') ? 'checked' : '' }}>
            {{ __('Ingat saya di perangkat ini') }}
        </label>

        <button type="submit" class="tu-btn tu-btn--block">
            <span>{{ __('Masuk') }}</span>
            <span class="arr" aria-hidden="true">&rarr;</span>
        </button>
    </form>

    <x-slot name="foot">
        <p class="mt-8 pt-6 border-t border-gray-200 text-sm text-gray-500">
            Belum memiliki akun pengajuan?
            <a href="{{ route('register') }}" class="tu-link font-semibold text-racing-red">Daftar sekarang</a>
        </p>
    </x-slot>
</x-auth-shell>
@endsection
