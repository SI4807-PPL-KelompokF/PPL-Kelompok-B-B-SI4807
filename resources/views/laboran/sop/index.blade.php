@extends('layouts.app')

@section('content')
<div class="relative overflow-hidden" id="laboran-sop-root">
    <x-bg-pattern uid="laboran-sop-pat" />
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 space-y-10">
        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
            <div class="tu-reveal max-w-2xl">
                <div class="inline-flex items-center gap-2 mb-3">
                    <span class="tu-chip lu-chip--flame">Panel Laboran</span>
                    <span class="tu-chip tu-chip--cream">PBI-06 Panduan SOP</span>
                </div>
                <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-gray-950 leading-[1.05]">
                    SOP Penyiapan Sampel
                </h1>
                <p class="mt-4 text-sm sm:text-base leading-relaxed text-gray-600">
                    Kelola panduan standar operasional penyiapan spesimen untuk setiap instrumen laboratorium. Perubahan langsung tampil pada formulir pengajuan Pemohon.
                </p>
            </div>
            <div class="tu-reveal [--d:100ms] flex flex-wrap items-center gap-3">
                <a href="{{ route('laboran.antrean.index') }}" class="tu-btn tu-btn--ghost tu-btn--sm">
                    <span>Antrean Fisik</span>
                </a>
                <a href="{{ route('laboran.pengujian.index') }}" class="tu-btn tu-btn--ghost tu-btn--sm">
                    <span>Panel Hasil Uji</span>
                </a>
            </div>
        </div>

        @if (session('status') || session('success'))
            <div class="tu-alert" dusk="sop-status-alert" role="alert">
                {{ session('status') ?? session('success') }}
            </div>
        @endif

        <!-- Metric Strip -->
        <section aria-label="Ringkasan kelengkapan SOP">
            <div class="grid grid-cols-3 border-y border-gray-200 bg-white/70">
                <div class="tu-metric tu-reveal px-4 sm:px-7 py-7 border-r border-gray-200 [--c:#111827]">
                    <span class="tu-tick"></span>
                    <p class="mt-5 text-xs sm:text-sm font-semibold text-gray-600">Total Mesin</p>
                    <p class="mt-1 text-4xl sm:text-5xl font-extrabold tracking-tight text-gray-950 tabular-nums">{{ $metrics['total'] }}</p>
                </div>
                <div class="tu-metric tu-reveal px-4 sm:px-7 py-7 border-r border-gray-200 [--c:#34d399] [--d:80ms]">
                    <span class="tu-tick"></span>
                    <p class="mt-5 text-xs sm:text-sm font-semibold text-emerald-800">SOP Tersedia</p>
                    <p class="mt-1 text-4xl sm:text-5xl font-extrabold tracking-tight text-gray-950 tabular-nums">{{ $metrics['sudah_ada'] }}</p>
                </div>
                <div class="tu-metric tu-reveal px-4 sm:px-7 py-7 [--c:#F7D488] [--d:160ms]">
                    <span class="tu-tick"></span>
                    <p class="mt-5 text-xs sm:text-sm font-semibold text-amber-800">Belum Diisi</p>
                    <p class="mt-1 text-4xl sm:text-5xl font-extrabold tracking-tight text-gray-950 tabular-nums">{{ $metrics['belum_ada'] }}</p>
                </div>
            </div>
        </section>

        <!-- Table -->
        <section class="bg-white border border-gray-200" aria-labelledby="sop-table-heading">
            <div class="flex items-center justify-between gap-4 border-b border-gray-200 px-5 sm:px-7 py-4">
                <h2 id="sop-table-heading" class="text-base font-bold tracking-tight text-gray-950">Daftar SOP per Instrumen</h2>
                <span class="tu-eyebrow text-xs text-gray-400">{{ $mesins->count() }} mesin</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm" dusk="table-sop">
                    <thead>
                        <tr class="border-b border-gray-100 tu-eyebrow !text-[10px] text-gray-400">
                            <th scope="col" class="px-5 sm:px-7 py-3 font-medium w-12">No</th>
                            <th scope="col" class="px-3 py-3 font-medium">Nama Mesin &amp; Kategori</th>
                            <th scope="col" class="px-3 py-3 font-medium">Status &amp; Ringkasan SOP</th>
                            <th scope="col" class="px-5 sm:px-7 py-3 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($mesins as $mesin)
                            @php
                                $plainSop = trim(preg_replace('/\s+/', ' ', strip_tags((string) $mesin->sop_penyiapan)));
                                $hasSop = $plainSop !== '';
                            @endphp
                            <tr class="lu-row no-anim" dusk="row-sop-{{ $mesin->id }}">
                                <td class="px-5 sm:px-7 py-4 align-top tu-mono text-xs text-gray-500">{{ $loop->iteration }}</td>
                                <td class="px-3 py-4 align-top">
                                    <p class="font-semibold text-gray-950">{{ $mesin->nama_mesin }}</p>
                                    <p class="mt-0.5 text-xs text-gray-500">{{ $mesin->laboratorium }} &middot; {{ $mesin->kategori_layanan }}</p>
                                </td>
                                <td class="px-3 py-4 align-top max-w-md">
                                    @if ($hasSop)
                                        <span class="tu-chip tu-chip--mint">Sudah ada</span>
                                        <p class="mt-1.5 text-xs text-gray-600 line-clamp-2">{{ \Illuminate\Support\Str::limit($plainSop, 110) }}</p>
                                    @else
                                        <span class="tu-chip tu-chip--jasmine">Belum ada</span>
                                        <p class="mt-1.5 text-xs text-gray-500">Belum memiliki panduan penyiapan sampel khusus.</p>
                                    @endif
                                </td>
                                <td class="px-5 sm:px-7 py-3.5 align-top text-right whitespace-nowrap">
                                    <div class="inline-flex items-center justify-end gap-2">
                                        <a href="{{ route('laboran.sop.edit', $mesin) }}" dusk="btn-edit-sop-{{ $mesin->id }}"
                                           class="tu-btn tu-btn--sm {{ $hasSop ? 'tu-btn--ghost' : '' }} !py-1.5 !px-3 text-xs">
                                            {{ $hasSop ? 'Edit SOP' : 'Tulis SOP' }}
                                        </a>
                                        @if ($hasSop)
                                            <form method="POST" action="{{ route('laboran.sop.destroy', $mesin) }}" class="inline"
                                                  onsubmit="return confirm('Hapus SOP mesin {{ addslashes($mesin->nama_mesin) }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" dusk="btn-hapus-sop-{{ $mesin->id }}"
                                                        class="tu-btn tu-btn--sm tu-btn--ghost !py-1.5 !px-3 text-xs text-racing-red hover:border-racing-red">
                                                    Hapus SOP
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-500">
                                    Belum ada data mesin laboratorium.
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
