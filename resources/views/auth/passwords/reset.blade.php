@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto my-8">
    <div class="bg-white border border-gray-200 rounded-lg p-6 sm:p-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">{{ __('Atur Ulang Kata Sandi') }}</h1>
            <p class="text-sm text-gray-600 mt-1">Buat kata sandi baru untuk akun Telyuji Anda.</p>
        </div>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('Alamat Email') }}
                </label>
                <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus
                    class="w-full px-3.5 py-2 text-sm bg-white border @error('email') border-racing-red @else border-gray-300 @enderror rounded-md text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-racing-red focus:border-racing-red transition-colors">
                @error('email')
                    <p class="mt-1.5 text-xs text-racing-red font-medium" role="alert">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('Kata Sandi Baru') }}
                </label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                    class="w-full px-3.5 py-2 text-sm bg-white border @error('password') border-racing-red @else border-gray-300 @enderror rounded-md text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-racing-red focus:border-racing-red transition-colors">
                @error('password')
                    <p class="mt-1.5 text-xs text-racing-red font-medium" role="alert">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label for="password-confirm" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('Konfirmasi Kata Sandi Baru') }}
                </label>
                <input id="password-confirm" type="password" name="password_confirmation" required autocomplete="new-password"
                    class="w-full px-3.5 py-2 text-sm bg-white border border-gray-300 rounded-md text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-racing-red focus:border-racing-red transition-colors">
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md text-sm font-semibold text-white bg-racing-red hover:bg-tiger-flame focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-racing-red transition-colors">
                    {{ __('Perbarui Kata Sandi') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
