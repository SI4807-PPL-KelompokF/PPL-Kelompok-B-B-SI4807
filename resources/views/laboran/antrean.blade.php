@extends('layouts.app')

@section('content')
<div class="relative overflow-hidden" id="laboran-antrean-root">
    <x-bg-pattern uid="laboran-antrean-pat" />
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 space-y-10">
        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
            <div class="tu-reveal max-w-2xl">
                <div class="inline-flex items-center gap-2 mb-3">
                    <span class="tu-chip lu-chip--flame">Panel Laboran</span>
                    <span class="tu-chip tu-chip--cream">PBI-10 Validasi Antrean</span>
                </div>
                <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-gray-950 leading-[1.05]">
                    Antrean Pengujian
                </h1>
                <p class="mt-4 text-sm sm:text-base leading-relaxed text-gray-600">
                    Validasi penerimaan sampel fisik maupun berkas digital pemohon, lalu mulai proses pengujian mesin agar tiket siap diunggah LHU-nya.
                </p>
            </div>
            <div class="tu-reveal [--d:100ms] flex flex-wrap items-center gap-3">
                <a href="{{ route('laboran.pengujian.index') }}" class="tu-btn tu-btn--sm">
                    <span>Unggah Hasil Uji ({{ $metrics['sedang_diuji'] }})</span>
                    <span class="arr" aria-hidden="true">&rarr;</span>
                </a>
            </div>
        </div>

        @if (session('status') || session('success'))
            <div class="tu-alert" dusk="antrean-status-alert" role="alert">
                {{ session('status') ?? session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="tu-alert tu-alert--danger" role="alert">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Metric Strip -->
        <section aria-label="Ringkasan antrean pengujian">
            <div class="grid grid-cols-2 lg:grid-cols-4 border-y border-gray-200 bg-white/70">
                <div class="tu-metric tu-reveal px-4 sm:px-7 py-7 border-r border-b lg:border-b-0 border-gray-200 [--c:#111827]">
                    <span class="tu-tick"></span>
                    <p class="mt-5 text-xs sm:text-sm font-semibold text-gray-600">Antrean Aktif</p>
                    <p class="mt-1 text-4xl sm:text-5xl font-extrabold tracking-tight text-gray-950 tabular-nums">{{ $metrics['total'] }}</p>
                    <p class="mt-2 text-xs text-gray-500">Menunggu aksi penerimaan/mesin</p>
                </div>
                <div class="tu-metric tu-reveal px-4 sm:px-7 py-7 border-b lg:border-b-0 lg:border-r border-gray-200 [--c:#ED1E28] [--d:80ms]">
                    <span class="tu-tick"></span>
                    <p class="mt-5 text-xs sm:text-sm font-semibold text-racing-red">Menunggu Verifikasi</p>
                    <p class="mt-1 text-4xl sm:text-5xl font-extrabold tracking-tight text-gray-950 tabular-nums">{{ $metrics['menunggu'] }}</p>
                    <p class="mt-2 text-xs text-gray-500">Belum diterima laboran</p>
                </div>
                <div class="tu-metric tu-reveal px-4 sm:px-7 py-7 border-r border-gray-200 [--c:#F7D488] [--d:160ms]">
                    <span class="tu-tick"></span>
                    <p class="mt-5 text-xs sm:text-sm font-semibold text-amber-800">Dalam Antrean</p>
                    <p class="mt-1 text-4xl sm:text-5xl font-extrabold tracking-tight text-gray-950 tabular-nums">{{ $metrics['antrean'] }}</p>
                    <p class="mt-2 text-xs text-amber-700">Siap diproses ke mesin</p>
                </div>
                <div class="tu-metric tu-reveal px-4 sm:px-7 py-7 [--c:#F26A2D] [--d:240ms]">
                    <span class="tu-tick"></span>
                    <p class="mt-5 text-xs sm:text-sm font-semibold text-tiger-flame">Proses Pengujian</p>
                    <p class="mt-1 text-4xl sm:text-5xl font-extrabold tracking-tight text-gray-950 tabular-nums">{{ $metrics['sedang_diuji'] }}</p>
                    <p class="mt-2 text-xs text-gray-500">Aktif di Panel Hasil Uji</p>
                </div>
            </div>
        </section>

        <!-- Queue Table -->
        <section class="bg-white border border-gray-200" aria-labelledby="antrean-heading">
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4 border-b border-gray-200 px-5 sm:px-7 pt-5">
                <div>
                    <h2 id="antrean-heading" class="text-base font-bold tracking-tight text-gray-950">Daftar Tiket Masuk &amp; Antrean Lab</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Tahap 1: Terima sampel fisik / verifikasi digital &rarr; Tahap 2: Proses ke mesin uji.</p>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between lg:justify-end gap-4 lg:gap-6">
                    <div class="flex overflow-x-auto no-scrollbar" role="tablist" aria-label="Filter status antrean">
                        <a href="{{ route('laboran.antrean.index', ['status' => 'all', 'q' => $search]) }}"
                           role="tab" aria-selected="{{ $statusFilter === 'all' ? 'true' : 'false' }}" class="tu-tab">
                            Semua <span class="tu-mono text-xs text-gray-400">{{ $metrics['total'] }}</span>
                        </a>
                        <a href="{{ route('laboran.antrean.index', ['status' => 'menunggu', 'q' => $search]) }}"
                           role="tab" aria-selected="{{ $statusFilter === 'menunggu' ? 'true' : 'false' }}" class="tu-tab">
                            Menunggu Verifikasi <span class="tu-mono text-xs text-gray-400">{{ $metrics['menunggu'] }}</span>
                        </a>
                        <a href="{{ route('laboran.antrean.index', ['status' => 'antrean', 'q' => $search]) }}"
                           role="tab" aria-selected="{{ $statusFilter === 'antrean' ? 'true' : 'false' }}" class="tu-tab">
                            Dalam Antrean <span class="tu-mono text-xs text-gray-400">{{ $metrics['antrean'] }}</span>
                        </a>
                    </div>

                    <form method="GET" action="{{ route('laboran.antrean.index') }}" class="relative pb-3 sm:w-60">
                        <input type="hidden" name="status" value="{{ $statusFilter }}">
                        <svg class="pointer-events-none absolute left-3 top-[13px] h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z"/>
                        </svg>
                        <input type="search" name="q" value="{{ $search }}" class="tu-input !py-2 !pl-9 !text-sm" placeholder="Cari tiket, sampel, mesin" autocomplete="off">
                    </form>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm" dusk="table-antrean">
                    <thead>
                        <tr class="border-b border-gray-100 tu-eyebrow !text-[10px] text-gray-400">
                            <th scope="col" class="px-5 sm:px-7 py-3 font-medium">Tiket &amp; Tanggal Uji</th>
                            <th scope="col" class="px-3 py-3 font-medium">Sampel &amp; Pemohon</th>
                            <th scope="col" class="px-3 py-3 font-medium">Instrumen &amp; Bentuk</th>
                            <th scope="col" class="px-3 py-3 font-medium">Status</th>
                            <th scope="col" class="px-5 sm:px-7 py-3 font-medium text-right">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($tiketList as $tiket)
                            <tr class="lu-row no-anim" dusk="row-antrean-{{ $tiket->nomor_tiket }}">
                                <td class="px-5 sm:px-7 py-4 align-top whitespace-nowrap">
                                    <a href="{{ route('pengajuan.show', $tiket->nomor_tiket) }}" class="tu-mono text-sm font-bold text-racing-red hover:underline block">
                                        {{ $tiket->nomor_tiket }}
                                    </a>
                                    <p class="tu-mono text-[11px] text-gray-500 mt-0.5">
                                        {{ $tiket->tanggal_uji ? $tiket->tanggal_uji->format('d M Y') : '-' }}
                                    </p>
                                </td>
                                <td class="px-3 py-4 align-top">
                                    <p class="font-semibold text-gray-950">{{ $tiket->nama_sampel }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $tiket->nama_pemohon }} &middot; {{ $tiket->fakultas }}</p>
                                    @if ($tiket->jenis_sampel === 'digital' && $tiket->tautan_file_cloud)
                                        <a href="{{ $tiket->tautan_file_cloud }}" target="_blank" rel="noopener noreferrer"
                                           class="inline-flex items-center gap-1 text-xs font-semibold text-racing-red hover:underline mt-1">
                                            <span>Buka Berkas Cloud</span>
                                            <span aria-hidden="true">&rarr;</span>
                                        </a>
                                    @endif
                                </td>
                                <td class="px-3 py-4 align-top">
                                    <p class="font-medium text-gray-900">{{ $tiket->instrumen }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $tiket->laboratorium }}</p>
                                    <div class="mt-1.5">
                                        @if ($tiket->jenis_sampel === 'digital')
                                            <span class="tu-chip tu-chip--mint">Berkas Digital</span>
                                        @else
                                            <span class="tu-chip tu-chip--cream">Spesimen Fisik</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-3 py-4 align-top whitespace-nowrap">
                                    @if ($tiket->status === \App\Models\TiketPengujian::STATUS_MENUNGGU)
                                        <span class="tu-chip tu-chip--cream">{{ $tiket->status }}</span>
                                    @elseif ($tiket->status === \App\Models\TiketPengujian::STATUS_DALAM_ANTREAN)
                                        <span class="tu-chip tu-chip--jasmine">{{ $tiket->status }}</span>
                                    @else
                                        <span class="tu-chip lu-chip--flame">{{ $tiket->status }}</span>
                                    @endif
                                </td>
                                <td class="px-5 sm:px-7 py-3.5 align-top text-right whitespace-nowrap">
                                    @if ($tiket->status === \App\Models\TiketPengujian::STATUS_MENUNGGU)
                                        @if ($tiket->jenis_sampel === 'digital')
                                            <form method="POST" action="{{ route('laboran.antrean.verifikasi-digital', $tiket->nomor_tiket) }}" class="inline">
                                                @csrf
                                                <button type="submit" dusk="btn-verifikasi-digital-{{ $tiket->nomor_tiket }}" class="tu-btn tu-btn--sm">
                                                    <span>Verifikasi Berkas Digital</span>
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('laboran.antrean.terima-fisik', $tiket->nomor_tiket) }}" class="inline">
                                                @csrf
                                                <button type="submit" dusk="btn-terima-fisik-{{ $tiket->nomor_tiket }}" class="tu-btn tu-btn--sm">
                                                    <span>Terima Sampel</span>
                                                </button>
                                            </form>
                                        @endif
                                    @elseif ($tiket->status === \App\Models\TiketPengujian::STATUS_DALAM_ANTREAN)
                                        <form method="POST" action="{{ route('laboran.antrean.proses-mesin', $tiket->nomor_tiket) }}" class="inline">
                                            @csrf
                                            <button type="submit" dusk="btn-proses-mesin-{{ $tiket->nomor_tiket }}" class="tu-btn tu-btn--sm">
                                                <span>Proses Mesin</span>
                                                <span class="arr" aria-hidden="true">&rarr;</span>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-xs text-gray-500">Tidak ada tindakan</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                                    Belum ada tiket dalam antrean.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
@endsection
