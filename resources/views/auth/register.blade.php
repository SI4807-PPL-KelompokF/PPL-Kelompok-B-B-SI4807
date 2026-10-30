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
                placeholder="nama@student.telkomuniversity.ac.id"
                pattern="^[a-zA-Z0-9._%+-]+@student\.telkomuniversity\.ac\.id$"
                title="Gunakan alamat email berdomain @student.telkomuniversity.ac.id"
                class="tu-input @error('email') is-invalid @enderror">
            @error('email')
                <p class="tu-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="tu-label">{{ __('Kata Sandi') }}</label>
            <x-password-input
                id="password"
                name="password"
                autocomplete="new-password"
                minlength="8"
                pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9]).{8,}"
                title="Minimal 8 karakter, wajib memuat huruf kecil, huruf besar, dan simbol."
                aria-describedby="password-rules"
                :invalid="$errors->has('password')"
            />
            <ul id="password-rules" data-password-rules="password" class="mt-2 space-y-1 text-xs text-gray-500">
                <li data-rule="length" class="flex items-center gap-2 transition-colors">
                    <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-current" aria-hidden="true"></span>
                    <span>Minimal 8 karakter</span>
                </li>
                <li data-rule="case" class="flex items-center gap-2 transition-colors">
                    <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-current" aria-hidden="true"></span>
                    <span>Kombinasi huruf kecil &amp; huruf besar (a-z, A-Z)</span>
                </li>
                <li data-rule="symbol" class="flex items-center gap-2 transition-colors">
                    <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-current" aria-hidden="true"></span>
                    <span>Mengandung simbol (!@#$%^&amp;*)</span>
                </li>
            </ul>
            @error('password')
                <p class="tu-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password-confirm" class="tu-label">{{ __('Konfirmasi Kata Sandi') }}</label>
            <x-password-input
                id="password-confirm"
                name="password_confirmation"
                autocomplete="new-password"
                minlength="8"
                aria-describedby="password-match-hint"
            />
            <p id="password-match-hint" data-password-match="password" data-password-confirm="password-confirm" class="mt-2 hidden items-center gap-2 text-xs font-medium" aria-live="polite">
                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-current" aria-hidden="true"></span>
                <span data-match-text>Konfirmasi kata sandi cocok</span>
            </p>
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
