@extends('layouts.app')

@section('body_class', 'is-auth')

@section('content')
<x-auth-shell
    heading="Verifikasi Email Anda"
    subheading="Konfirmasikan alamat email Anda untuk mengakses seluruh layanan laboratorium."
    aside-text="Kami perlu memastikan email kampus Anda aktif sebelum pengajuan sampel dibuka."
>
    @if (session('resent'))
        <div class="tu-alert mb-5" role="alert">
            {{ __('Tautan verifikasi baru telah dikirimkan ke alamat email Anda.') }}
        </div>
    @endif

    <div class="space-y-3 text-sm leading-relaxed text-gray-600">
        <p>{{ __('Sebelum melanjutkan, silakan periksa email Anda untuk tautan verifikasi akun.') }}</p>
        <p>{{ __('Jika Anda tidak menerima email verifikasi') }}, silakan ajukan pengiriman ulang di bawah ini.</p>
    </div>

    <form class="mt-7" method="POST" action="{{ Route::has('verification.resend') ? route('verification.resend') : '#' }}">
        @csrf
        <button type="submit" class="tu-btn tu-btn--block">
            <span>{{ __('Kirim Ulang Tautan Verifikasi') }}</span>
            <span class="arr" aria-hidden="true">&rarr;</span>
        </button>
    </form>
</x-auth-shell>
@endsection
