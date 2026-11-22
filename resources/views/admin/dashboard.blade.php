@extends('layouts.app')

@section('content')
<div class="relative overflow-hidden" id="admin-dashboard-root">
    <x-bg-pattern uid="admin-dash-pat" />
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 space-y-10">

        {{-- ══════════════════════════════════════════════════════════════════
             HEADER
        ══════════════════════════════════════════════════════════════════ --}}
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-8">
            <div class="tu-reveal">
                <div class="inline-flex items-center gap-2 mb-3">
                    <span class="tu-chip tu-chip--cream">
                        <svg class="w-3 h-3 mr-1 text-racing-red" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z" />
                        </svg>
                        Dashboard Analitik
                    </span>
                </div>
                <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-gray-950 leading-[1.05]">
                    Dashboard Laboratorium
                </h1>
                <p class="mt-4 max-w-xl text-sm sm:text-base leading-relaxed text-gray-600">
                    Pantau statistik operasional laboratorium secara real-time — tingkat penggunaan mesin uji,
                    distribusi pengajuan per fakultas, dan kondisi antrean keseluruhan.
                </p>
            </div>
            <div class="tu-reveal [--d:120ms] flex flex-wrap items-center gap-3">
                <span class="tu-chip tu-chip--cream text-xs text-gray-500">
                    Data diperbarui setiap kunjungan
                </span>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════════════════
             RINGKASAN METRIK (4 KARTU)
        ══════════════════════════════════════════════════════════════════ --}}
        <section aria-label="Ringkasan metrik laboratorium">
            <div class="grid grid-cols-2 lg:grid-cols-4 border-y border-gray-200 bg-white/70">

                {{-- Total Pengajuan --}}
                <div class="tu-metric tu-reveal px-5 sm:px-7 py-8 border-r border-b lg:border-b-0 border-gray-200 [--c:#111827]" dusk="metric-total">
                    <span class="tu-tick"></span>
                    <div class="flex items-center gap-2 mt-5">
                        <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="tu-eyebrow text-gray-500">Total Pengajuan</p>
                    </div>
                    <p class="mt-2 text-5xl font-extrabold tracking-tight text-gray-950 tabular-nums">
                        <span data-count="{{ $metrics['total'] }}" dusk="val-total">{{ number_format($metrics['total']) }}</span>
                    </p>
                    <p class="mt-2 text-xs text-gray-500">Seluruh tiket tercatat</p>
                </div>

                {{-- Antrean Aktif (Menunggu Verifikasi) --}}
                <div class="tu-metric tu-reveal px-5 sm:px-7 py-8 border-b lg:border-b-0 lg:border-r border-gray-200 [--c:#F7D488] [--d:80ms]" dusk="metric-menunggu">
                    <span class="tu-tick"></span>
                    <div class="flex items-center gap-2 mt-5">
                        <svg class="w-4 h-4 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="tu-eyebrow text-amber-800">Antrean Aktif</p>
                    </div>
                    <p class="mt-2 text-5xl font-extrabold tracking-tight text-gray-950 tabular-nums">
                        <span data-count="{{ $metrics['menunggu'] }}" dusk="val-menunggu">{{ number_format($metrics['menunggu']) }}</span>
                    </p>
                    <p class="mt-2 text-xs text-amber-700">Menunggu verifikasi laboran</p>
                </div>

                {{-- Sedang Diuji --}}
                <div class="tu-metric tu-reveal px-5 sm:px-7 py-8 border-r border-gray-200 [--c:#F26A2D] [--d:160ms]" dusk="metric-sedang-diuji">
                    <span class="tu-tick"></span>
                    <div class="flex items-center gap-2 mt-5">
                        <svg class="w-4 h-4 text-tiger-flame shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                        <p class="tu-eyebrow text-tiger-flame">Sedang Diuji</p>
                    </div>
                    <p class="mt-2 text-5xl font-extrabold tracking-tight text-gray-950 tabular-nums">
                        <span data-count="{{ $metrics['sedang_diuji'] }}" dusk="val-sedang-diuji">{{ number_format($metrics['sedang_diuji']) }}</span>
                    </p>
                    <p class="mt-2 text-xs text-gray-500">Proses instrumen lab aktif</p>
                </div>

                {{-- Pengujian Selesai --}}
                <div class="tu-metric tu-reveal px-5 sm:px-7 py-8 [--c:#34d399] [--d:240ms]" dusk="metric-selesai">
                    <span class="tu-tick"></span>
                    <div class="flex items-center gap-2 mt-5">
                        <svg class="w-4 h-4 text-emerald-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="tu-eyebrow text-emerald-800">Pengujian Selesai</p>
                    </div>
                    <p class="mt-2 text-5xl font-extrabold tracking-tight text-gray-950 tabular-nums">
                        <span data-count="{{ $metrics['selesai'] }}" dusk="val-selesai">{{ number_format($metrics['selesai']) }}</span>
                    </p>
                    <p class="mt-2 text-xs text-emerald-700">LHU siap diunduh pemohon</p>
                </div>
            </div>
        </section>

        {{-- ══════════════════════════════════════════════════════════════════
             GRAFIK — 2 kolom grid pada layar besar
        ══════════════════════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

            {{-- ── GRAFIK 1: Penggunaan Mesin (Instrumen) ────────────────── --}}
            <section class="bg-white border border-gray-200 tu-reveal flex flex-col" aria-labelledby="chart-instrumen-heading" dusk="chart-instrumen-section">
                <div class="border-b border-gray-200 px-5 sm:px-7 py-4 flex items-center justify-between gap-4">
                    <div>
                        <h2 id="chart-instrumen-heading" class="text-base font-bold tracking-tight text-gray-950">
                            Tingkat Penggunaan Mesin Laboratorium
                        </h2>
                        <p class="mt-0.5 text-xs text-gray-500">
                            Jumlah tiket pengajuan per mesin/instrumen uji — diurutkan dari tertinggi
                        </p>
                    </div>
                    <span class="tu-eyebrow text-xs text-gray-400 shrink-0">Bar Chart</span>
                </div>

                <div class="flex-1 px-5 sm:px-7 py-6">
                    @if ($usageByInstrumen->isEmpty())
                        {{-- Empty state --}}
                        <div class="flex flex-col items-center justify-center py-16 text-center" dusk="empty-instrumen">
                            <div class="relative grid h-14 w-14 place-items-center overflow-hidden border border-dashed border-gray-400 bg-white text-gray-500">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <span class="tu-scan-line absolute inset-x-0 h-px bg-racing-red"></span>
                            </div>
                            <p class="mt-5 text-sm font-semibold text-gray-950">Belum ada data penggunaan mesin</p>
                            <p class="mt-1.5 max-w-sm text-xs leading-relaxed text-gray-500">
                                Data grafik akan muncul setelah terdapat pengajuan tiket pengujian yang tercatat dalam sistem.
                            </p>
                        </div>
                    @else
                        {{-- Canvas untuk Chart.js --}}
                        <div class="relative" style="min-height: 280px;">
                            <canvas id="chartInstrumen" dusk="canvas-instrumen"
                                aria-label="Grafik bar tingkat penggunaan mesin laboratorium"
                                role="img"></canvas>
                        </div>

                        {{-- Data JSON untuk Chart.js (di-inject dari PHP, bukan dummy) --}}
                        <script id="data-instrumen" type="application/json">
                            {!! json_encode([
                                'labels' => $usageByInstrumen->pluck('instrumen')->toArray(),
                                'data'   => $usageByInstrumen->pluck('jumlah_pengajuan')->map(fn($v) => (int)$v)->toArray(),
                            ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}
                        </script>
                    @endif
                </div>
            </section>

            {{-- ── GRAFIK 2: Distribusi Pengajuan per Fakultas ──────────── --}}
            <section class="bg-white border border-gray-200 tu-reveal flex flex-col" aria-labelledby="chart-fakultas-heading" dusk="chart-fakultas-section">
                <div class="border-b border-gray-200 px-5 sm:px-7 py-4 flex items-center justify-between gap-4">
                    <div>
                        <h2 id="chart-fakultas-heading" class="text-base font-bold tracking-tight text-gray-950">
                            Distribusi Pengajuan Berdasarkan Fakultas
                        </h2>
                        <p class="mt-0.5 text-xs text-gray-500">
                            Jumlah tiket pengajuan dari setiap fakultas pemohon
                        </p>
                    </div>
                    @if ($usageByFakultas->count() <= 6)
                        <span class="tu-eyebrow text-xs text-gray-400 shrink-0">Doughnut</span>
                    @else
                        <span class="tu-eyebrow text-xs text-gray-400 shrink-0">Bar Chart</span>
                    @endif
                </div>

                <div class="flex-1 px-5 sm:px-7 py-6">
                    @if ($usageByFakultas->isEmpty())
                        {{-- Empty state --}}
                        <div class="flex flex-col items-center justify-center py-16 text-center" dusk="empty-fakultas">
                            <div class="relative grid h-14 w-14 place-items-center overflow-hidden border border-dashed border-gray-400 bg-white text-gray-500">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span class="tu-scan-line absolute inset-x-0 h-px bg-racing-red"></span>
                            </div>
                            <p class="mt-5 text-sm font-semibold text-gray-950">Belum ada data fakultas</p>
                            <p class="mt-1.5 max-w-sm text-xs leading-relaxed text-gray-500">
                                Grafik distribusi fakultas akan tampil setelah ada pengajuan tiket yang tercatat dengan informasi fakultas.
                            </p>
                        </div>
                    @else
                        {{-- Gunakan Doughnut untuk ≤6 kategori, Bar untuk lebih --}}
                        @php $chartTypeFakultas = $usageByFakultas->count() <= 6 ? 'doughnut' : 'bar'; @endphp

                        <div class="relative {{ $chartTypeFakultas === 'doughnut' ? 'flex items-center justify-center' : '' }}"
                             style="{{ $chartTypeFakultas === 'doughnut' ? 'min-height: 280px;' : 'min-height: 280px;' }}">
                            <canvas id="chartFakultas" dusk="canvas-fakultas"
                                aria-label="Grafik distribusi pengajuan berdasarkan fakultas"
                                role="img"
                                style="{{ $chartTypeFakultas === 'doughnut' ? 'max-width: 320px; max-height: 320px;' : '' }}"></canvas>
                        </div>

                        <script id="data-fakultas" type="application/json">
                            {!! json_encode([
                                'type'   => $chartTypeFakultas,
                                'labels' => $usageByFakultas->pluck('fakultas')->toArray(),
                                'data'   => $usageByFakultas->pluck('jumlah_pengajuan')->map(fn($v) => (int)$v)->toArray(),
                            ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}
                        </script>
                    @endif
                </div>

                {{-- Legenda tabel di bawah grafik (selalu tampil kalau ada data) --}}
                @if ($usageByFakultas->isNotEmpty())
                    <div class="border-t border-gray-100 px-5 sm:px-7 py-4" dusk="fakultas-legend-table">
                        <p class="tu-eyebrow text-xs text-gray-400 mb-3">Rincian</p>
                        <div class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                            @php $totalFak = $usageByFakultas->sum('jumlah_pengajuan'); @endphp
                            @foreach ($usageByFakultas as $fak)
                                @php
                                    $pct = $totalFak > 0 ? round(($fak->jumlah_pengajuan / $totalFak) * 100, 1) : 0;
                                @endphp
                                <div class="flex items-center gap-3" dusk="legend-row-{{ $loop->index }}">
                                    <div class="shrink-0 w-2.5 h-2.5 rounded-sm" style="background-color: {{ $loop->index === 0 ? '#ED1E28' : ($loop->index === 1 ? '#F26A2D' : ($loop->index === 2 ? '#F7D488' : ($loop->index === 3 ? '#34d399' : ($loop->index === 4 ? '#60a5fa' : '#a78bfa')))) }}"></div>
                                    <span class="flex-1 text-xs text-gray-700 truncate">{{ $fak->fakultas }}</span>
                                    <span class="tu-mono text-xs font-semibold text-gray-950">{{ number_format($fak->jumlah_pengajuan) }}</span>
                                    <span class="tu-mono text-xs text-gray-400">{{ $pct }}%</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </section>
        </div>

        <p id="chart-library-error" class="hidden border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900" role="alert">
            Grafik belum dapat dimuat karena library visualisasi tidak berhasil diakses. Periksa koneksi internet, lalu muat ulang halaman.
        </p>

        {{-- ══════════════════════════════════════════════════════════════════
             TABEL RINGKASAN INSTRUMEN (opsional, sebagai tabel pendamping)
        ══════════════════════════════════════════════════════════════════ --}}
        @if ($usageByInstrumen->isNotEmpty())
        <section class="bg-white border border-gray-200 tu-reveal" aria-labelledby="table-instrumen-heading" dusk="table-instrumen-section">
            <div class="flex items-center justify-between gap-4 border-b border-gray-200 px-5 sm:px-7 py-4">
                <h2 id="table-instrumen-heading" class="text-base font-bold tracking-tight text-gray-950">
                    Rincian Penggunaan per Mesin Laboratorium
                </h2>
                <span class="tu-eyebrow text-xs text-gray-400">{{ $usageByInstrumen->count() }} mesin</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm" dusk="instrumen-detail-table">
                    <thead>
                        <tr class="border-b border-gray-100 tu-eyebrow !text-[10px] text-gray-400">
                            <th scope="col" class="px-5 sm:px-7 py-3 font-medium w-8">#</th>
                            <th scope="col" class="px-3 py-3 font-medium">Nama Mesin / Instrumen</th>
                            <th scope="col" class="px-3 py-3 font-medium text-right">Jumlah Pengajuan</th>
                            <th scope="col" class="px-5 sm:px-7 py-3 font-medium">Proporsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalInstrumen = $usageByInstrumen->sum('jumlah_pengajuan'); @endphp
                        @foreach ($usageByInstrumen as $instrumen)
                            @php
                                $pct = $totalInstrumen > 0 ? round(($instrumen->jumlah_pengajuan / $totalInstrumen) * 100, 1) : 0;
                            @endphp
                            <tr class="border-b border-gray-100 hover:bg-gray-50/60 transition-colors" dusk="instrumen-row-{{ $loop->index }}">
                                <td class="px-5 sm:px-7 py-3.5 align-middle">
                                    <span class="tu-mono text-xs font-semibold text-gray-400">{{ $loop->iteration }}</span>
                                </td>
                                <td class="px-3 py-3.5 align-middle">
                                    <span class="font-semibold text-gray-950">{{ $instrumen->instrumen }}</span>
                                </td>
                                <td class="px-3 py-3.5 align-middle text-right">
                                    <span class="tu-mono text-sm font-bold text-gray-950">{{ number_format($instrumen->jumlah_pengajuan) }}</span>
                                </td>
                                <td class="px-5 sm:px-7 py-3.5 align-middle min-w-[160px]">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                            <div class="h-full rounded-full bg-racing-red transition-all duration-700"
                                                 style="width: {{ $pct }}%"></div>
                                        </div>
                                        <span class="tu-mono text-xs text-gray-500 w-10 text-right">{{ $pct }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
        @endif

    </div>{{-- /max-w-7xl --}}
</div>{{-- /relative overflow-hidden --}}
@endsection

@push('scripts')
{{-- Chart.js — dimuat hanya di halaman ini, tidak mempengaruhi halaman lain --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"
        integrity="sha256-s4B2di9zY7yekStouOA0gmeY213ya7YfAA7C56MTe8c="
        crossorigin="anonymous"></script>
<script>
(function () {
    'use strict';

    // Hentikan inisialisasi dengan pesan yang jelas apabila library CDN gagal dimuat.
    if (typeof window.Chart === 'undefined') {
        var chartError = document.getElementById('chart-library-error');
        if (chartError) chartError.classList.remove('hidden');
        return;
    }

    // Referensi chart digunakan oleh automated browser test untuk memastikan canvas
    // benar-benar dirender, bukan sekadar ada di DOM.
    window.telyujiDashboardCharts = window.telyujiDashboardCharts || {};

    // ── Palet warna brand Telyuji ────────────────────────────────────────
    var PALETTE = [
        '#ED1E28', // racing-red
        '#F26A2D', // tiger-flame
        '#F7D488', // jasmine
        '#34d399', // emerald-400
        '#60a5fa', // blue-400
        '#a78bfa', // violet-400
        '#fb923c', // orange-400
        '#38bdf8', // sky-400
        '#4ade80', // green-400
        '#f472b6', // pink-400
    ];

    function getColor(index) {
        return PALETTE[index % PALETTE.length];
    }

    // ── Global Chart.js defaults (konsisten dengan desain Telyuji) ───────
    Chart.defaults.font.family = '"General Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif';
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#6b7280'; // text-gray-500

    // ── Grafik 1: Penggunaan Instrumen (Horizontal Bar) ──────────────────
    var instrumenEl  = document.getElementById('chartInstrumen');
    var instrumenRaw = document.getElementById('data-instrumen');

    if (instrumenEl && instrumenRaw) {
        var instrumenData = JSON.parse(instrumenRaw.textContent || '{}');
        var iLabels = instrumenData.labels || [];
        var iData   = instrumenData.data   || [];

        window.telyujiDashboardCharts.instrumen = new Chart(instrumenEl, {
            type: 'bar',
            data: {
                labels: iLabels,
                datasets: [{
                    label: 'Jumlah Pengajuan',
                    data: iData,
                    backgroundColor: iLabels.map(function (_, idx) {
                        var c = getColor(idx);
                        return c + 'CC'; // 80% opacity
                    }),
                    borderColor: iLabels.map(function (_, idx) {
                        return getColor(idx);
                    }),
                    borderWidth: 1.5,
                    borderRadius: 3,
                    borderSkipped: false,
                }],
            },
            options: {
                indexAxis: 'y',          // Horizontal bar
                responsive: true,
                maintainAspectRatio: false,
                aspectRatio: 1.6,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#fff',
                        titleColor: '#111827',
                        bodyColor: '#374151',
                        borderColor: '#e5e7eb',
                        borderWidth: 1,
                        padding: 10,
                        callbacks: {
                            title: function (items) {
                                return items[0].label;
                            },
                            label: function (item) {
                                return ' ' + item.formattedValue + ' pengajuan';
                            },
                        },
                    },
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            callback: function (val) {
                                return Number.isInteger(val) ? val : null;
                            },
                        },
                        grid: { color: '#f3f4f6' },
                        title: {
                            display: true,
                            text: 'Jumlah Pengajuan',
                            color: '#9ca3af',
                            font: { size: 11 },
                        },
                    },
                    y: {
                        grid: { display: false },
                        ticks: {
                            font: { weight: '600' },
                            color: '#374151',
                        },
                    },
                },
            },
        });
    }

    // ── Grafik 2: Distribusi Fakultas (Doughnut atau Bar) ────────────────
    var fakultasEl  = document.getElementById('chartFakultas');
    var fakultasRaw = document.getElementById('data-fakultas');

    if (fakultasEl && fakultasRaw) {
        var fakultasData = JSON.parse(fakultasRaw.textContent || '{}');
        var fType   = fakultasData.type   || 'bar';
        var fLabels = fakultasData.labels || [];
        var fData   = fakultasData.data   || [];
        var fColors = fLabels.map(function (_, idx) { return getColor(idx); });

        if (fType === 'doughnut') {
            window.telyujiDashboardCharts.fakultas = new Chart(fakultasEl, {
                type: 'doughnut',
                data: {
                    labels: fLabels,
                    datasets: [{
                        label: 'Jumlah Pengajuan',
                        data: fData,
                        backgroundColor: fColors.map(function (c) { return c + 'CC'; }),
                        borderColor: fColors,
                        borderWidth: 2,
                        hoverOffset: 8,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    cutout: '62%',
                    plugins: {
                        legend: { display: false }, // Legenda sudah ada di tabel bawah
                        tooltip: {
                            backgroundColor: '#fff',
                            titleColor: '#111827',
                            bodyColor: '#374151',
                            borderColor: '#e5e7eb',
                            borderWidth: 1,
                            padding: 10,
                            callbacks: {
                                label: function (item) {
                                    var total = item.dataset.data.reduce(function (a, b) { return a + b; }, 0);
                                    var pct   = total > 0 ? Math.round((item.parsed / total) * 1000) / 10 : 0;
                                    return ' ' + item.formattedValue + ' pengajuan (' + pct + '%)';
                                },
                            },
                        },
                    },
                },
            });
        } else {
            // Bar chart untuk fakultas > 6
            window.telyujiDashboardCharts.fakultas = new Chart(fakultasEl, {
                type: 'bar',
                data: {
                    labels: fLabels,
                    datasets: [{
                        label: 'Jumlah Pengajuan',
                        data: fData,
                        backgroundColor: fColors.map(function (c) { return c + 'CC'; }),
                        borderColor: fColors,
                        borderWidth: 1.5,
                        borderRadius: 3,
                        borderSkipped: false,
                    }],
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#fff',
                            titleColor: '#111827',
                            bodyColor: '#374151',
                            borderColor: '#e5e7eb',
                            borderWidth: 1,
                            padding: 10,
                            callbacks: {
                                label: function (item) {
                                    return ' ' + item.formattedValue + ' pengajuan';
                                },
                            },
                        },
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                callback: function (val) {
                                    return Number.isInteger(val) ? val : null;
                                },
                            },
                            grid: { color: '#f3f4f6' },
                            title: {
                                display: true,
                                text: 'Jumlah Pengajuan',
                                color: '#9ca3af',
                                font: { size: 11 },
                            },
                        },
                        y: {
                            grid: { display: false },
                            ticks: {
                                font: { weight: '600' },
                                color: '#374151',
                            },
                        },
                    },
                },
            });
        }
    }
})();
</script>
@endpush