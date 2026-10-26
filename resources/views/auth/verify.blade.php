@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto my-8">
    <div class="bg-white border border-gray-200 rounded-lg p-6 sm:p-8">
        <div class="mb-6">
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">{{ __('Verifikasi Email Anda') }}</h1>
            <p class="text-sm text-gray-600 mt-1">Konfirmasikan alamat email Anda untuk mengakses seluruh layanan laboratorium.</p>
        </div>

        @if (session('resent'))
            <div class="mb-4 p-3 rounded-md bg-frosted-mint border border-emerald-300 text-emerald-900 text-xs font-medium" role="alert">
                {{ __('Tautan verifikasi baru telah dikirimkan ke alamat email Anda.') }}
            </div>
        @endif

        <div class="text-xs text-gray-600 leading-relaxed space-y-3">
            <p>{{ __('Sebelum melanjutkan, silakan periksa email Anda untuk tautan verifikasi akun.') }}</p>
            <p>{{ __('Jika Anda tidak menerima email verifikasi') }}, silakan ajukan pengiriman ulang di bawah ini.</p>
        </div>

        <form class="mt-6" method="POST" action="{{ route('verification.resend') }}">
            @csrf
            <button type="submit"
                class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md text-sm font-semibold text-white bg-racing-red hover:bg-tiger-flame focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-racing-red transition-colors">
                {{ __('Kirim Ulang Tautan Verifikasi') }}
            </button>
        </form>
    </div>
</div>
@endsection
