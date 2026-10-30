@extends('layouts.app')

@section('body_class', 'is-auth')

@section('content')
<x-auth-shell
    heading="Atur Ulang Kata Sandi"
    subheading="Masukkan alamat email Anda untuk menerima tautan pemulihan kata sandi."
    aside-text="Lupa kata sandi bukan masalah. Kami kirimkan tautan aman untuk membuat yang baru."
>
    @if (session('status'))
        <div class="tu-alert mb-5" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="tu-label">{{ __('Alamat Email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                class="tu-input @error('email') is-invalid @enderror">
            @error('email')
                <p class="tu-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="tu-btn tu-btn--block">
            <span>{{ __('Kirim Tautan Atur Ulang') }}</span>
            <span class="arr" aria-hidden="true">&rarr;</span>
        </button>
    </form>

    <x-slot name="foot">
        <p class="mt-8 pt-6 border-t border-gray-200 text-sm text-gray-500">
            Kembali ke
            <a href="{{ route('login') }}" class="tu-link font-semibold text-racing-red">halaman masuk</a>
        </p>
    </x-slot>
</x-auth-shell>
@endsection
