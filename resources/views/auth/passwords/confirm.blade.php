@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto my-8">
    <div class="bg-white border border-gray-200 rounded-lg p-6 sm:p-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">{{ __('Konfirmasi Kata Sandi') }}</h1>
            <p class="text-sm text-gray-600 mt-1">{{ __('Harap konfirmasi kata sandi Anda sebelum melanjutkan.') }}</p>
        </div>

        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
            @csrf

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('Kata Sandi') }}
                </label>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    class="w-full px-3.5 py-2 text-sm bg-white border @error('password') border-racing-red @else border-gray-300 @enderror rounded-md text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-racing-red focus:border-racing-red transition-colors">
                @error('password')
                    <p class="mt-1.5 text-xs text-racing-red font-medium" role="alert">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="pt-2">
                <button type="submit"
                    class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md text-sm font-semibold text-white bg-racing-red hover:bg-tiger-flame focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-racing-red transition-colors">
                    {{ __('Konfirmasi') }}
                </button>
            </div>

            @if (Route::has('password.request'))
                <div class="text-center pt-2">
                    <a href="{{ route('password.request') }}" class="text-xs text-gray-600 hover:text-racing-red transition-colors">
                        {{ __('Lupa kata sandi Anda?') }}
                    </a>
                </div>
            @endif
        </form>
    </div>
</div>
@endsection
