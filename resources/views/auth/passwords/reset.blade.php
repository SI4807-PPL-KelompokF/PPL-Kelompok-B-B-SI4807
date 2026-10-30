@extends('layouts.app')

@section('body_class', 'is-auth')

@section('content')
<x-auth-shell
    heading="Atur Ulang Kata Sandi"
    subheading="Buat kata sandi baru untuk akun Telyuji Anda."
    aside-text="Satu langkah lagi. Pilih kata sandi baru yang kuat dan mudah Anda ingat."
>
    <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div>
            <label for="email" class="tu-label">{{ __('Alamat Email') }}</label>
            <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus
                placeholder="nama@student.telkomuniversity.ac.id"
                pattern="^[a-zA-Z0-9._%+-]+@student\.telkomuniversity\.ac\.id$"
                title="Gunakan alamat email berdomain @student.telkomuniversity.ac.id"
                class="tu-input @error('email') is-invalid @enderror">
            @error('email')
                <p class="tu-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="tu-label">{{ __('Kata Sandi Baru') }}</label>
            <x-password-input
                id="password"
                name="password"
                autocomplete="new-password"
                minlength="8"
                pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9]).{8,}"
                title="Minimal 8 karakter, wajib memuat huruf kecil, huruf besar, dan simbol."
                :invalid="$errors->has('password')"
            />
            @error('password')
                <p class="tu-error" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password-confirm" class="tu-label">{{ __('Konfirmasi Kata Sandi Baru') }}</label>
            <x-password-input
                id="password-confirm"
                name="password_confirmation"
                autocomplete="new-password"
                minlength="8"
            />
        </div>

        <button type="submit" class="tu-btn tu-btn--block">
            <span>{{ __('Perbarui Kata Sandi') }}</span>
            <span class="arr" aria-hidden="true">&rarr;</span>
        </button>
    </form>
</x-auth-shell>
@endsection
