@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-gray-200">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Dashboard Layanan Pengujian</h1>
            <p class="text-sm text-gray-600 mt-1">Selamat datang kembali, <span class="font-semibold text-gray-900">{{ Auth::user()->name }}</span>. Kelola pengujian laboratorium Anda di sini.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold uppercase tracking-wider bg-cream text-gray-800 border border-amber-200">
                Peran: {{ Auth::user()->role ?? 'pemohon' }}
            </span>
            <a href="#" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-racing-red hover:bg-tiger-flame rounded-md shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-racing-red">
                + Pengajuan Sampel Baru
            </a>
        </div>
    </div>

    @if (session('status'))
        <div class="p-4 rounded-md bg-frosted-mint border border-emerald-300 text-emerald-900 text-sm font-medium" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <!-- Metric Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-gray-200 rounded-lg p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Total Pengajuan</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">0</p>
            <p class="mt-1 text-xs text-gray-500">Seluruh riwayat sampel</p>
        </div>

        <div class="bg-white border border-amber-200 bg-amber-50/30 rounded-lg p-5">
            <p class="text-xs font-medium text-amber-800 uppercase tracking-wider">Menunggu Verifikasi</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">0</p>
            <p class="mt-1 text-xs text-amber-700">Dalam antrean laboran</p>
        </div>

        <div class="bg-white border border-orange-200 bg-orange-50/20 rounded-lg p-5">
            <p class="text-xs font-medium text-tiger-flame uppercase tracking-wider">Sedang Diuji</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">0</p>
            <p class="mt-1 text-xs text-gray-500">Proses instrumen lab</p>
        </div>

        <div class="bg-white border border-emerald-200 bg-emerald-50/30 rounded-lg p-5">
            <p class="text-xs font-medium text-emerald-800 uppercase tracking-wider">Hasil Selesai</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">0</p>
            <p class="mt-1 text-xs text-emerald-700">LHU siap diunduh</p>
        </div>
    </div>

    <!-- Active Samples Table / Overview -->
    <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-base font-semibold text-gray-900">Daftar Pengujian Sampel Terkini</h2>
            <span class="text-xs text-gray-500">Diperbarui secara real-time</span>
        </div>
        <div class="p-8 text-center">
            <div class="w-12 h-12 mx-auto rounded-full bg-gray-100 flex items-center justify-center text-gray-400 mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"></path>
                </svg>
            </div>
            <p class="text-sm font-medium text-gray-900">Belum ada pengujian sampel aktif</p>
            <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">Klik tombol pengajuan sampel baru untuk memulai proses pengujian laboratorium Anda.</p>
            <div class="mt-4">
                <a href="#" class="inline-flex items-center px-3.5 py-2 text-xs font-semibold text-racing-red hover:text-tiger-flame border border-racing-red/30 hover:border-racing-red rounded transition-colors">
                    Pelajari Prosedur Pengujian & Persyaratan Dokumen &rarr;
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
