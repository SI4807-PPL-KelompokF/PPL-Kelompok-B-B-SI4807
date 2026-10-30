@extends('layouts.app')

@section('body_class', 'is-auth')

@section('content')
<x-auth-shell
    heading="Pendaftaran Akun"
    subheading="Daftarkan akun pemohon untuk pengajuan uji sampel."
    aside-text="Daftar sekali, lalu ajukan sampel, pantau antrean instrumen, dan unduh LHU resmi dari satu tempat."
>
    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <label for="name" class="tu-label">{{ __('Nama Lengkap') }}</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                class="tu-input @error('name') is-invalid @enderror">
            @error('name')
                <p class="tu-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="email" class="tu-label">{{ __('Alamat Email Kampus') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                class="tu-input @error('email') is-invalid @enderror">
            @error('email')
                <p class="tu-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="tu-label">{{ __('Kata Sandi') }}</label>
            <x-password-input id="password" name="password" autocomplete="new-password" :invalid="$errors->has('password')" />
            @error('password')
                <p class="tu-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password-confirm" class="tu-label">{{ __('Konfirmasi Kata Sandi') }}</label>
            <x-password-input id="password-confirm" name="password_confirmation" autocomplete="new-password" />
        </div>

        <button type="submit" class="tu-btn tu-btn--block">
            <span>{{ __('Daftar Sekarang') }}</span>
            <span class="arr" aria-hidden="true">&rarr;</span>
        </button>
    </form>

    <x-slot name="foot">
        <p class="mt-8 pt-6 border-t border-gray-200 text-sm text-gray-500">
            Sudah memiliki akun?
            <a href="{{ route('login') }}" class="tu-link font-semibold text-racing-red">Masuk di sini</a>
        </p>
    </x-slot>
</x-auth-shell>
@endsection
