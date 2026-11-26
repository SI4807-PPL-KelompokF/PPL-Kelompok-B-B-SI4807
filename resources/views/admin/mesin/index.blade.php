@extends('layouts.app')

@section('content')
<div class="relative overflow-hidden" id="admin-mesin-root">
    <x-bg-pattern uid="admin-mesin-pat" />
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 space-y-10">
        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
            <div class="tu-reveal max-w-2xl">
                <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-gray-950 leading-[1.05]">
                    Katalog Mesin Laboratorium
                </h1>
                <p class="mt-4 text-sm sm:text-base leading-relaxed text-gray-600">
                    Kelola daftar instrumen laboratorium, lokasi penempatan, kuota harian, estimasi SLA, dan status operasional (Aktif / Maintenance).
                </p>
            </div>
            <div class="tu-reveal [--d:100ms] shrink-0">
                <a href="{{ route('admin.mesin.create') }}" dusk="btn-tambah-mesin" class="tu-btn">
                    <span aria-hidden="true">+</span>
                    <span>Tambah Mesin</span>
                </a>
            </div>
        </div>

        @if (session('status') || session('success'))
            <div class="tu-alert" dusk="mesin-status-alert" role="alert">
                {{ session('status') ?? session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="tu-alert tu-alert--danger" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Metric Strip -->
        <section aria-label="Ringkasan katalog mesin">
            <div class="grid grid-cols-3 border-y border-gray-200 bg-white/70">
                <div class="tu-metric tu-reveal px-4 sm:px-7 py-7 border-r border-gray-200 [--c:#111827]">
                    <span class="tu-tick"></span>
                    <p class="mt-5 text-xs sm:text-sm font-semibold text-gray-600">Total Mesin</p>
                    <p class="mt-1 text-4xl sm:text-5xl font-extrabold tracking-tight text-gray-950 tabular-nums">{{ $metrics['total'] }}</p>
                    <p class="mt-2 text-xs text-gray-500">Terdaftar di katalog</p>
                </div>
                <div class="tu-metric tu-reveal px-4 sm:px-7 py-7 border-r border-gray-200 [--c:#34d399] [--d:80ms]">
                    <span class="tu-tick"></span>
                    <p class="mt-5 text-xs sm:text-sm font-semibold text-emerald-800">Aktif Siap Pakai</p>
                    <p class="mt-1 text-4xl sm:text-5xl font-extrabold tracking-tight text-gray-950 tabular-nums">{{ $metrics['aktif'] }}</p>
                    <p class="mt-2 text-xs text-emerald-700">Tersedia di form pengajuan</p>
                </div>
                <div class="tu-metric tu-reveal px-4 sm:px-7 py-7 [--c:#F7D488] [--d:160ms]">
                    <span class="tu-tick"></span>
                    <p class="mt-5 text-xs sm:text-sm font-semibold text-amber-800">Maintenance</p>
                    <p class="mt-1 text-4xl sm:text-5xl font-extrabold tracking-tight text-gray-950 tabular-nums">{{ $metrics['maintenance'] }}</p>
                    <p class="mt-2 text-xs text-amber-700">Sementara tidak dapat dipilih</p>
                </div>
            </div>
        </section>

        <!-- Table -->
        <section class="bg-white border border-gray-200" aria-labelledby="mesin-table-heading">
            <div class="border-b border-gray-200 px-5 sm:px-7 py-4">
                <h2 id="mesin-table-heading" class="text-base font-bold tracking-tight text-gray-950">Daftar Mesin &amp; Instrumen Uji</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm" dusk="table-mesin">
                    <thead>
                        <tr class="border-b border-gray-100 tu-eyebrow !text-[10px] text-gray-400">
                            <th scope="col" class="px-5 sm:px-7 py-3 font-medium w-12">No</th>
                            <th scope="col" class="px-3 py-3 font-medium">Nama Mesin &amp; Laboratorium</th>
                            <th scope="col" class="px-3 py-3 font-medium">Kategori Layanan</th>
                            <th scope="col" class="px-3 py-3 font-medium whitespace-nowrap">Kuota / SLA</th>
                            <th scope="col" class="px-3 py-3 font-medium">Status</th>
                            <th scope="col" class="px-5 sm:px-7 py-3 font-medium text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($mesins as $mesin)
                            <tr class="lu-row no-anim" dusk="row-mesin-{{ $mesin->id }}">
                                <td class="px-5 sm:px-7 py-4 align-middle tu-mono text-xs text-gray-500">{{ $loop->iteration }}</td>
                                <td class="px-3 py-4 align-middle">
                                    <p class="font-semibold text-gray-950">{{ $mesin->nama_mesin }}</p>
                                    <p class="mt-0.5 text-xs text-gray-500">{{ $mesin->laboratorium }}</p>
                                </td>
                                <td class="px-3 py-4 align-middle">
                                    <span class="tu-chip tu-chip--cream">{{ $mesin->kategori_layanan }}</span>
                                </td>
                                <td class="px-3 py-4 align-middle whitespace-nowrap tu-mono text-xs text-gray-700">
                                    <span class="font-semibold text-gray-950">{{ $mesin->kuota_harian }}</span> slot/hari
                                    <span class="text-gray-400">&middot;</span>
                                    <span class="font-semibold text-gray-950">{{ $mesin->estimasi_sla_hari }}</span> hari
                                </td>
                                <td class="px-3 py-4 align-middle whitespace-nowrap" dusk="status-mesin-{{ $mesin->id }}">
                                    @if ($mesin->isAktif())
                                        <span class="tu-chip tu-chip--mint">Aktif</span>
                                    @else
                                        <span class="tu-chip tu-chip--jasmine">Maintenance</span>
                                    @endif
                                </td>
                                <td class="px-5 sm:px-7 py-3.5 align-middle text-right whitespace-nowrap">
                                    <div class="inline-flex items-center justify-end gap-2">
                                        <form method="POST" action="{{ route('admin.mesin.toggle', $mesin) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" dusk="btn-toggle-mesin-{{ $mesin->id }}"
                                                    class="tu-btn tu-btn--sm tu-btn--ghost !py-1.5 !px-3 text-xs">
                                                {{ $mesin->isAktif() ? 'Set Maintenance' : 'Set Aktif' }}
                                            </button>
                                        </form>
                                        <a href="{{ route('admin.mesin.edit', $mesin) }}" dusk="btn-edit-mesin-{{ $mesin->id }}"
                                           class="tu-btn tu-btn--sm tu-btn--ghost !py-1.5 !px-3 text-xs">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.mesin.destroy', $mesin) }}" class="inline"
                                              onsubmit="return confirm('Hapus mesin {{ addslashes($mesin->nama_mesin) }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" dusk="btn-hapus-mesin-{{ $mesin->id }}"
                                                    class="tu-btn tu-btn--sm tu-btn--ghost !py-1.5 !px-3 text-xs text-racing-red hover:border-racing-red">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">
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
