@extends('layouts.app')

@section('content')
<div class="relative">
    <div class="tu-grid-bg absolute inset-x-0 top-0 h-80" aria-hidden="true"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 space-y-10">
        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-8">
            <div class="tu-reveal">
                <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-gray-950 leading-[1.05]">Dashboard Layanan Pengujian</h1>
                <p class="mt-4 max-w-xl text-sm sm:text-base leading-relaxed text-gray-600">
                    Selamat datang kembali, <span class="font-semibold text-gray-950">{{ Auth::user()->name }}</span>. Kelola pengujian laboratorium Anda di sini.
                </p>
            </div>
            <div class="tu-reveal [--d:120ms]">
                <a href="#" class="tu-btn">
                    <span aria-hidden="true">+</span>
                    <span>Pengajuan Sampel Baru</span>
                </a>
            </div>
        </div>

        @if (session('status'))
            <div class="tu-alert" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <!-- Metric strip -->
        <section aria-label="Ringkasan pengajuan">
            <div class="grid grid-cols-2 lg:grid-cols-4 border-y border-gray-200 bg-white/70">
                <div class="tu-metric tu-reveal px-5 sm:px-7 py-8 border-r border-b lg:border-b-0 border-gray-200 [--c:#111827]">
                    <span class="tu-tick"></span>
                    <p class="tu-eyebrow mt-5 text-gray-500">Total Pengajuan</p>
                    <p class="mt-2 text-5xl font-extrabold tracking-tight text-gray-950 tabular-nums"><span data-count="0">0</span></p>
                    <p class="mt-2 text-xs text-gray-500">Seluruh riwayat sampel</p>
                </div>
                <div class="tu-metric tu-reveal px-5 sm:px-7 py-8 border-b lg:border-b-0 lg:border-r border-gray-200 [--c:#F7D488] [--d:80ms]">
                    <span class="tu-tick"></span>
                    <p class="tu-eyebrow mt-5 text-amber-800">Menunggu Verifikasi</p>
                    <p class="mt-2 text-5xl font-extrabold tracking-tight text-gray-950 tabular-nums"><span data-count="0">0</span></p>
                    <p class="mt-2 text-xs text-amber-700">Dalam antrean laboran</p>
                </div>
                <div class="tu-metric tu-reveal px-5 sm:px-7 py-8 border-r border-gray-200 [--c:#F26A2D] [--d:160ms]">
                    <span class="tu-tick"></span>
                    <p class="tu-eyebrow mt-5 text-tiger-flame">Sedang Diuji</p>
                    <p class="mt-2 text-5xl font-extrabold tracking-tight text-gray-950 tabular-nums"><span data-count="0">0</span></p>
                    <p class="mt-2 text-xs text-gray-500">Proses instrumen lab</p>
                </div>
                <div class="tu-metric tu-reveal px-5 sm:px-7 py-8 [--c:#34d399] [--d:240ms]">
                    <span class="tu-tick"></span>
                    <p class="tu-eyebrow mt-5 text-emerald-800">Hasil Selesai</p>
                    <p class="mt-2 text-5xl font-extrabold tracking-tight text-gray-950 tabular-nums"><span data-count="0">0</span></p>
                    <p class="mt-2 text-xs text-emerald-700">LHU siap diunduh</p>
                </div>
            </div>
        </section>

        <!-- Samples table -->
        <section class="bg-white border border-gray-200 tu-reveal" aria-labelledby="sample-heading">
            <div class="flex items-center justify-between gap-4 border-b border-gray-200 px-5 sm:px-7 py-4">
                <h2 id="sample-heading" class="text-base font-bold tracking-tight text-gray-950">Daftar Pengujian Sampel Terkini</h2>
            </div>

            <div class="relative">
                <!-- Ghost table -->
                <div class="hidden sm:grid grid-cols-12 gap-4 px-7 py-3 border-b border-gray-100 tu-eyebrow !text-[10px] text-gray-400" aria-hidden="true">
                    <span class="col-span-3">ID Sampel</span>
                    <span class="col-span-4">Instrumen</span>
                    <span class="col-span-3">Status</span>
                    <span class="col-span-2 text-right">Estimasi</span>
                </div>
                <div class="px-5 sm:px-7" aria-hidden="true">
                    <div class="grid grid-cols-12 items-center gap-4 border-b border-gray-100 py-5 opacity-90">
                        <span class="tu-skel col-span-5 sm:col-span-3 h-3"></span>
                        <span class="tu-skel hidden sm:block col-span-4 h-3"></span>
                        <span class="tu-skel col-span-4 sm:col-span-3 h-5"></span>
                        <span class="tu-skel col-span-3 sm:col-span-2 h-3"></span>
                    </div>
                    <div class="grid grid-cols-12 items-center gap-4 border-b border-gray-100 py-5 opacity-60">
                        <span class="tu-skel col-span-5 sm:col-span-3 h-3"></span>
                        <span class="tu-skel hidden sm:block col-span-4 h-3"></span>
                        <span class="tu-skel col-span-4 sm:col-span-3 h-5"></span>
                        <span class="tu-skel col-span-3 sm:col-span-2 h-3"></span>
                    </div>
                    <div class="grid grid-cols-12 items-center gap-4 py-5 opacity-30">
                        <span class="tu-skel col-span-5 sm:col-span-3 h-3"></span>
                        <span class="tu-skel hidden sm:block col-span-4 h-3"></span>
                        <span class="tu-skel col-span-4 sm:col-span-3 h-5"></span>
                        <span class="tu-skel col-span-3 sm:col-span-2 h-3"></span>
                    </div>
                </div>

                <!-- Empty state -->
                <div class="tu-empty-fade absolute inset-0 flex flex-col items-center justify-end px-6 pb-10 text-center">
                    <div class="relative grid h-14 w-14 place-items-center overflow-hidden border border-dashed border-gray-400 bg-white text-gray-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"></path>
                        </svg>
                        <span class="tu-scan-line absolute inset-x-0 h-px bg-racing-red"></span>
                    </div>
                    <p class="mt-5 text-sm font-semibold text-gray-950">Belum ada pengujian sampel aktif</p>
                    <p class="mt-1.5 max-w-sm text-xs leading-relaxed text-gray-500">Klik tombol pengajuan sampel baru untuk memulai proses pengujian laboratorium Anda.</p>
                    <a href="#" class="tu-btn tu-btn--ghost tu-btn--sm mt-6">
                        <span>Pelajari Prosedur Pengujian &amp; Persyaratan Dokumen</span>
                        <span class="arr" aria-hidden="true">&rarr;</span>
                    </a>
                </div>

                <div class="h-72 sm:h-64" aria-hidden="true"></div>
            </div>
        </section>
    </div>
</div>
@endsection
