@extends('layouts.app')

@section('content')
@php
    $fallbackTickets = [
        [
            'id' => 'TLY-20261015-089',
            'name' => 'Rizky Aditya Pratama',
            'prodi' => 'S1 Teknik Elektro · FTE',
            'machine' => 'Spektrofotometer UV-Vis',
            'status' => 'testing',
            'upload_url' => route('laboran.pengujian.upload', 'TLY-20261015-089'),
            'file' => null,
        ],
        [
            'id' => 'TLY-20261015-092',
            'name' => 'Dr. Maya Lestari, M.T.',
            'prodi' => 'Dosen · Fak. Rekayasa Industri',
            'machine' => 'Universal Testing Machine',
            'status' => 'testing',
            'upload_url' => route('laboran.pengujian.upload', 'TLY-20261015-092'),
            'file' => null,
        ],
        [
            'id' => 'TLY-20261014-071',
            'name' => 'Nadia Putri Anjani',
            'prodi' => 'S2 Informatika · FIF',
            'machine' => 'Analyzer Spektrum RF',
            'status' => 'testing',
            'upload_url' => route('laboran.pengujian.upload', 'TLY-20261014-071'),
            'file' => null,
        ],
        [
            'id' => 'TLY-20261016-101',
            'name' => 'Salsabila Rahma',
            'prodi' => 'S1 Teknik Telekomunikasi · FTE',
            'machine' => 'Lab Antena & Propagasi',
            'status' => 'testing',
            'upload_url' => route('laboran.pengujian.upload', 'TLY-20261016-101'),
            'file' => null,
        ],
        [
            'id' => 'TLY-20261013-054',
            'name' => 'Fajar Maulana',
            'prodi' => 'S1 Teknik Fisika · FTE',
            'machine' => 'XRD Diffractometer',
            'status' => 'done',
            'upload_url' => route('laboran.pengujian.upload', 'TLY-20261013-054'),
            'file' => [
                'name' => 'LHU-TLY-20261013-054.pdf',
                'size' => (int) round(1.8 * 1048576),
                'at' => '14 Okt 2026, 09:12',
                'by' => 'Budi Santoso',
                'v' => 1,
            ],
        ],
        [
            'id' => 'TLY-20261012-038',
            'name' => 'Prof. Hendra Gunawan',
            'prodi' => 'Dosen · Fak. Ilmu Terapan',
            'machine' => 'Spektrometer FTIR',
            'status' => 'done',
            'upload_url' => route('laboran.pengujian.upload', 'TLY-20261012-038'),
            'file' => [
                'name' => 'LHU-TLY-20261012-038.xlsx',
                'size' => (int) round(0.9 * 1048576),
                'at' => '13 Okt 2026, 15:30',
                'by' => 'Budi Santoso',
                'v' => 2,
            ],
        ],
    ];

    $ticketsPayload = ($tiketList && $tiketList->count() > 0)
        ? $tiketList->map(function ($t) {
            $doc = $t->dokumenHasil;
            $latestAudit = $t->auditTrails ? $t->auditTrails->sortByDesc('id')->first() : null;
            return [
                'id' => $t->nomor_tiket,
                'name' => $t->nama_pemohon,
                'prodi' => $t->fakultas,
                'machine' => $t->instrumen,
                'status' => $t->status === 'Selesai' ? 'done' : 'testing',
                'upload_url' => route('laboran.pengujian.upload', $t->nomor_tiket),
                'file' => $doc ? [
                    'name' => $doc->nama_berkas_asli,
                    'size' => (int) $doc->ukuran_bytes,
                    'at' => $doc->updated_at ? $doc->updated_at->format('d M Y, H:i') : '14 Okt 2026, 09:12',
                    'by' => $latestAudit ? $latestAudit->nama_aktor : 'Budi Santoso',
                    'v' => (int) $doc->versi,
                ] : null,
            ];
        })->values()->all()
        : $fallbackTickets;

    $fallbackAudit = [
        [
            'id' => 'AUD-2026-0412',
            'type' => 'upload',
            'at' => '14 Okt 2026, 09:12',
            'ticket' => 'TLY-20261013-054',
            'detail' => 'LHU-TLY-20261013-054.pdf',
            'actor' => 'Budi Santoso',
        ],
        [
            'id' => 'AUD-2026-0405',
            'type' => 'revisi',
            'at' => '13 Okt 2026, 15:30',
            'ticket' => 'TLY-20261012-038',
            'detail' => 'LHU-TLY-20261012-038.xlsx (v2)',
            'actor' => 'Budi Santoso',
        ],
        [
            'id' => 'AUD-2026-0391',
            'type' => 'upload',
            'at' => '12 Okt 2026, 11:05',
            'ticket' => 'TLY-20261012-038',
            'detail' => 'LHU-TLY-20261012-038.xlsx (v1)',
            'actor' => 'Sari Wulandari',
        ],
    ];

    $auditPayload = ($auditTrails && $auditTrails->count() > 0)
        ? $auditTrails->map(function ($a) {
            $isRev = $a->aksi === 'REVISI_HASIL_UJI';
            return [
                'id' => 'AUD-2026-' . str_pad((string) (400 + $a->id), 4, '0', STR_PAD_LEFT),
                'type' => $isRev ? 'revisi' : 'upload',
                'at' => $a->created_at ? $a->created_at->format('d M Y, H:i') : 'Baru saja',
                'ticket' => $a->tiketPengujian ? $a->tiketPengujian->nomor_tiket : '-',
                'detail' => $a->berkas_baru . ($a->versi > 1 || $isRev ? ' (v' . $a->versi . ')' : ''),
                'actor' => $a->nama_aktor,
            ];
        })->values()->all()
        : $fallbackAudit;

    $initialSelected = $selectedTiket ? $selectedTiket->nomor_tiket : null;
    $initialDoc = $selectedTiket ? $selectedTiket->dokumenHasil : null;
    $initialIsRev = $initialDoc !== null;
    $testingCount = collect($ticketsPayload)->where('status', 'testing')->count();
    $doneCount = collect($ticketsPayload)->where('status', 'done')->count();
    $revCount = collect($auditPayload)->where('type', 'revisi')->count();
@endphp

<div class="relative overflow-hidden" id="lu-root" data-actor="{{ Auth::user()->name }}" data-initial-ticket="{{ $initialSelected ?? '' }}">
    <x-bg-pattern uid="laboran-pat" />
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 space-y-10">
        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
            <div class="tu-reveal max-w-2xl">
                <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-gray-950 leading-[1.05]">Unggah &amp; Revisi Hasil Uji</h1>
                <p class="mt-4 text-sm sm:text-base leading-relaxed text-gray-600">
                    Pilih tiket dari antrean, unggah dokumen hasil uji (PDF atau Excel), dan status tiket otomatis berubah menjadi <span class="font-semibold text-gray-950">Selesai</span>.
                </p>
            </div>
            <div class="tu-reveal [--d:100ms] flex flex-wrap items-center gap-3">
                <span class="tu-chip tu-chip--cream">.PDF &middot; .XLSX &middot; .XLS &middot; Maks. 5 MB</span>
                <button type="button" id="btn-next-ticket" class="tu-btn tu-btn--sm">
                    <span>Proses Tiket Berikutnya</span>
                    <span class="arr" aria-hidden="true">&rarr;</span>
                </button>
            </div>
        </div>

        @if (session('status'))
            <div class="tu-alert" dusk="upload-success-alert" role="alert">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="border border-racing-red bg-red-50/60 px-4 py-3 text-xs font-medium text-racing-red rounded-[3px]" dusk="validation-error-banner" role="alert">
                {{ $errors->first('berkas_hasil') ?: $errors->first() }}
            </div>
        @endif

        <!-- Metric strip -->
        <section aria-label="Ringkasan antrean hasil uji">
            <div class="grid grid-cols-3 border-y border-gray-200 bg-white/70">
                <div class="tu-metric tu-reveal px-4 sm:px-7 py-7 border-r border-gray-200 [--c:#F26A2D]">
                    <span class="tu-tick"></span>
                    <p class="mt-5 text-xs sm:text-sm font-semibold text-tiger-flame">Sedang Diuji</p>
                    <p class="mt-1 text-4xl sm:text-5xl font-extrabold tracking-tight text-gray-950 tabular-nums" id="m-testing">{{ $testingCount }}</p>
                    <p class="mt-2 text-xs text-gray-500">Menunggu dokumen hasil</p>
                </div>
                <div class="tu-metric tu-reveal px-4 sm:px-7 py-7 border-r border-gray-200 [--c:#34d399] [--d:80ms]">
                    <span class="tu-tick"></span>
                    <p class="mt-5 text-xs sm:text-sm font-semibold text-emerald-800">Selesai</p>
                    <p class="mt-1 text-4xl sm:text-5xl font-extrabold tracking-tight text-gray-950 tabular-nums" id="m-done">{{ $doneCount }}</p>
                    <p class="mt-2 text-xs text-emerald-700">Hasil sudah diunggah</p>
                </div>
                <div class="tu-metric tu-reveal px-4 sm:px-7 py-7 [--c:#F7D488] [--d:160ms]">
                    <span class="tu-tick"></span>
                    <p class="mt-5 text-xs sm:text-sm font-semibold text-amber-800">Total Revisi</p>
                    <p class="mt-1 text-4xl sm:text-5xl font-extrabold tracking-tight text-gray-950 tabular-nums" id="m-rev">{{ $revCount }}</p>
                    <p class="mt-2 text-xs text-amber-700">Riwayat pembaruan berkas</p>
                </div>
            </div>
        </section>

        <!-- Queue -->
        <section class="bg-white border border-gray-200 tu-reveal" aria-labelledby="queue-heading">
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4 border-b border-gray-200 px-5 sm:px-7 pt-5">
                <div class="flex items-center justify-between gap-4 lg:block">
                    <h2 id="queue-heading" class="text-base font-bold tracking-tight text-gray-950">Antrean Tiket</h2>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between lg:justify-end gap-4 lg:gap-8">
                    <div class="flex overflow-x-auto no-scrollbar" role="tablist" aria-label="Filter status tiket" id="tabs">
                        <button type="button" role="tab" class="tu-tab" data-filter="all" aria-selected="true">Semua <span class="tu-mono text-xs text-gray-400" data-count-for="all">{{ count($ticketsPayload) }}</span></button>
                        <button type="button" role="tab" class="tu-tab" data-filter="testing" aria-selected="false">Sedang Diuji <span class="tu-mono text-xs text-gray-400" data-count-for="testing">{{ $testingCount }}</span></button>
                        <button type="button" role="tab" class="tu-tab" data-filter="done" aria-selected="false">Selesai <span class="tu-mono text-xs text-gray-400" data-count-for="done">{{ $doneCount }}</span></button>
                    </div>
                    <div class="relative pb-3 sm:w-64">
                        <svg class="pointer-events-none absolute left-3 top-[13px] h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z"/></svg>
                        <label for="q" class="sr-only">Cari tiket</label>
                        <input id="q" dusk="search-ticket-input" type="search" class="tu-input !py-2 !pl-9 !text-sm" placeholder="Cari kode, nama, mesin" autocomplete="off">
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table dusk="queue-table" class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 tu-eyebrow !text-[10px] text-gray-400">
                            <th scope="col" class="px-5 sm:px-7 py-3 font-medium">Kode Tiket</th>
                            <th scope="col" class="px-3 py-3 font-medium">Pemohon</th>
                            <th scope="col" class="hidden md:table-cell px-3 py-3 font-medium">Mesin Lab</th>
                            <th scope="col" class="px-3 py-3 font-medium">Status</th>
                            <th scope="col" class="px-5 sm:px-7 py-3 text-right font-medium"><span class="sr-only">Aksi</span></th>
                        </tr>
                    </thead>
                    <tbody id="rows">
                        @foreach ($ticketsPayload as $t)
                            @php $done = $t['status'] === 'done'; @endphp
                            <tr class="lu-row no-anim" dusk="ticket-row-{{ $t['id'] }}">
                                <td class="px-5 sm:px-7 py-4 align-top whitespace-nowrap">
                                    <span class="tu-mono text-sm font-semibold text-gray-950">{{ $t['id'] }}</span>
                                </td>
                                <td class="px-3 py-4 align-top">
                                    <p class="font-semibold text-gray-950">{{ $t['name'] }}</p>
                                    <p class="mt-0.5 text-xs text-gray-500">{{ $t['prodi'] }}</p>
                                    <p class="mt-1 text-xs text-gray-700 md:hidden">{{ $t['machine'] }}</p>
                                </td>
                                <td class="hidden md:table-cell px-3 py-4 align-top text-gray-700">{{ $t['machine'] }}</td>
                                <td class="px-3 py-4 align-top whitespace-nowrap">
                                    @if ($done)
                                        <span class="tu-chip tu-chip--mint">Selesai</span>
                                        @if (!empty($t['file']) && $t['file']['v'] > 1)
                                            <span class="tu-mono ml-1 text-xs text-gray-500">v{{ $t['file']['v'] }}</span>
                                        @endif
                                    @else
                                        <span class="tu-chip lu-chip--flame">Sedang Diuji</span>
                                    @endif
                                </td>
                                <td class="px-5 sm:px-7 py-3 align-top text-right">
                                    <button type="button" dusk="action-upload-{{ $t['id'] }}" class="tu-btn tu-btn--sm{{ $done ? ' tu-btn--ghost' : '' }}" data-open="{{ $t['id'] }}">
                                        @if ($done)
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6h6M20 20v-6h-6M5.6 15A8 8 0 0018.9 14M18.4 9A8 8 0 005.1 10"/></svg>
                                            <span>Revisi</span>
                                        @else
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16V4m0 0l-4 4m4-4l4 4M4 16v3a1 1 0 001 1h14a1 1 0 001-1v-3"/></svg>
                                            <span>Unggah hasil</span>
                                        @endif
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <p id="empty" class="hidden px-7 py-14 text-center text-sm text-gray-500">Tidak ada tiket yang cocok dengan pencarian.</p>
            </div>
        </section>

        <!-- Audit trail -->
        <section class="tu-reveal" aria-labelledby="audit-heading">
            <div class="flex items-end justify-between gap-4">
                <h2 id="audit-heading" class="text-base font-bold tracking-tight text-gray-950">Jejak Audit Terbaru</h2>
            </div>
            <ol id="audit" dusk="audit-trail-table" class="mt-4 border-t border-gray-200">
                @foreach ($auditPayload as $a)
                    <li class="grid grid-cols-12 items-center gap-x-4 gap-y-1 border-b border-gray-200 py-4">
                        <span class="tu-mono col-span-12 sm:col-span-3 text-xs text-gray-500">{{ $a['at'] }}</span>
                        <span class="col-span-4 sm:col-span-2">
                            <span class="tu-chip {{ $a['type'] === 'revisi' ? 'tu-chip--jasmine' : 'tu-chip--mint' }}">
                                {{ $a['type'] === 'revisi' ? 'Revisi' : 'Unggah' }}
                            </span>
                        </span>
                        <span class="col-span-8 sm:col-span-4 min-w-0">
                            <span class="tu-mono block text-sm font-semibold text-gray-950">{{ $a['ticket'] }}</span>
                            <span class="block truncate text-xs text-gray-500">{{ $a['detail'] }}</span>
                        </span>
                        <span class="col-span-8 sm:col-span-2 text-xs text-gray-600">{{ $a['actor'] }}</span>
                        <span class="tu-mono col-span-4 sm:col-span-1 text-right text-[11px] text-gray-400">{{ str_replace('AUD-2026-', '#', $a['id']) }}</span>
                    </li>
                @endforeach
            </ol>
        </section>
    </div>
</div>

<!-- Drawer -->
<div class="lu-overlay{{ $initialSelected ? ' is-open' : '' }}" id="overlay" aria-hidden="{{ $initialSelected ? 'false' : 'true' }}"></div>
<aside class="lu-drawer{{ $initialSelected ? ' is-open' : '' }}" id="drawer" dusk="upload-workspace-panel" role="dialog" aria-modal="true" aria-labelledby="dr-title" aria-hidden="{{ $initialSelected ? 'false' : 'true' }}">
    <header class="flex items-start justify-between gap-4 border-b border-gray-200 px-6 py-5">
        <div class="min-w-0">
            <h2 id="dr-title" class="text-xl font-extrabold tracking-tight text-gray-950">
                {{ $initialIsRev ? 'Revisi Hasil Uji' : 'Unggah Hasil Uji' }}
            </h2>
            <div class="mt-2 flex flex-wrap items-center gap-3">
                <span id="dr-code" class="tu-mono text-sm font-semibold text-gray-700">{{ $selectedTiket ? $selectedTiket->nomor_tiket : '' }}</span>
                <span id="dr-status">
                    @if ($selectedTiket)
                        @if ($selectedTiket->status === 'Selesai')
                            <span class="tu-chip tu-chip--mint">Selesai</span>
                        @else
                            <span class="tu-chip lu-chip--flame">Sedang Diuji</span>
                        @endif
                    @endif
                </span>
            </div>
        </div>
        <button type="button" class="tu-icon-btn" id="dr-close" aria-label="Tutup panel">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-width="2" d="M6 6l12 12M18 6L6 18"/></svg>
        </button>
    </header>

    <!-- Form pane -->
    <form
        id="pane-form"
        method="POST"
        action="{{ route('laboran.pengujian.upload', $selectedTiket ? $selectedTiket->nomor_tiket : 'TLY-20261015-089') }}"
        enctype="multipart/form-data"
        class="flex min-h-0 flex-1 flex-col"
        novalidate
    >
        @csrf
        <div class="lu-drawer-body flex-1 space-y-6 overflow-y-auto px-6 py-6">
            <dl class="lu-meta grid grid-cols-2 gap-x-6 gap-y-4 p-4 [--k:0]">
                <div>
                    <dt class="text-xs text-gray-500">Pemohon</dt>
                    <dd id="mt-name" class="mt-1 text-sm font-semibold text-gray-950">{{ $selectedTiket ? $selectedTiket->nama_pemohon : '' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-gray-500">Fakultas / Prodi</dt>
                    <dd id="mt-prodi" class="mt-1 text-sm font-semibold text-gray-950">{{ $selectedTiket ? $selectedTiket->fakultas : '' }}</dd>
                </div>
                <div class="col-span-2 border-t border-black/10 pt-4">
                    <dt class="text-xs text-gray-500">Mesin laboratorium</dt>
                    <dd id="mt-machine" class="mt-1 text-sm font-semibold text-gray-950">{{ $selectedTiket ? $selectedTiket->instrumen : '' }}</dd>
                </div>
            </dl>

            <div id="rev-block" dusk="revision-mode-banner" class="{{ $initialIsRev ? '' : 'hidden ' }}space-y-3 [--k:1]">
                <div class="relative border border-jasmine bg-jasmine/30 py-3 pl-5 pr-4 text-sm leading-relaxed text-amber-950" role="note">
                    <span class="absolute inset-y-0 left-0 w-[3px] bg-[#e0a92c]"></span>
                    <p class="font-semibold">Mode revisi</p>
                    <p class="mt-0.5 text-xs text-amber-900/80">File baru akan menggantikan dokumen di bawah ini. Perubahan dicatat sebagai revisi di Audit Trail.</p>
                </div>
                <div class="flex items-center gap-3 border border-gray-200 bg-white p-3">
                    <span id="cur-badge" class="lu-badge {{ $initialDoc && strtolower($initialDoc->ekstensi) === 'pdf' ? 'lu-badge--pdf' : 'lu-badge--xls' }}">
                        {{ $initialDoc ? strtoupper($initialDoc->ekstensi) : 'PDF' }}
                    </span>
                    <div class="min-w-0 flex-1">
                        <p id="cur-name" dusk="existing-file-name" class="truncate text-sm font-semibold text-gray-950">
                            {{ $initialDoc ? $initialDoc->nama_berkas_asli : '' }}
                        </p>
                        <p id="cur-meta" class="mt-0.5 text-xs text-gray-500">
                            {{ $initialDoc ? 'v' . $initialDoc->versi . ' · ' . $initialDoc->ukuran_formatted : '' }}
                        </p>
                    </div>
                    <span class="tu-chip tu-chip--mint">Aktif</span>
                </div>
            </div>

            <div class="[--k:2]">
                <p class="tu-label" id="drop-label">{{ $initialIsRev ? 'File pengganti' : 'File hasil uji' }}</p>
                <div class="lu-drop" id="drop" dusk="dropzone-normal" aria-labelledby="drop-label">
                    <span class="tu-bracket tu-bracket--tl" aria-hidden="true"></span>
                    <span class="tu-bracket tu-bracket--tr" aria-hidden="true"></span>
                    <span class="tu-bracket tu-bracket--bl" aria-hidden="true"></span>
                    <span class="tu-bracket tu-bracket--br" aria-hidden="true"></span>
                    <span class="lu-sweep" aria-hidden="true"></span>

                    <div id="drop-idle">
                        <div class="lu-drop-icon mx-auto grid h-12 w-12 place-items-center border border-gray-300 bg-white text-gray-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 16V4m0 0l-4 4m4-4l4 4M4 16v3a1 1 0 001 1h14a1 1 0 001-1v-3"/></svg>
                        </div>
                        <p class="mt-4 text-sm font-semibold text-gray-950">Seret &amp; lepas file di sini</p>
                        <p class="mt-1 text-xs text-gray-500">atau pilih dari komputer Anda</p>
                        <label for="file" class="tu-btn tu-btn--ghost tu-btn--sm mt-5 cursor-pointer">Pilih file</label>
                        <p class="tu-mono mt-4 text-[11px] tracking-wide text-gray-500">.PDF &middot; .XLSX &middot; .XLS &middot; maks. 5 MB</p>
                    </div>

                    <div id="drop-file" dusk="dropzone-valid-state" class="hidden">
                        <div class="flex items-center gap-3">
                            <span id="f-badge" class="lu-badge"></span>
                            <div class="min-w-0 flex-1">
                                <p id="f-name" class="truncate text-sm font-semibold text-gray-950"></p>
                                <p id="f-size" class="tu-mono mt-0.5 text-xs text-gray-600"></p>
                            </div>
                            <button type="button" id="f-clear" class="shrink-0 text-xs font-semibold text-gray-700 underline underline-offset-4 hover:text-racing-red">Ganti file</button>
                        </div>
                        <div id="f-errors" dusk="dropzone-error-state" class="mt-3 hidden space-y-1 border-t border-racing-red/30 pt-3 text-xs font-medium text-racing-red" role="alert"></div>
                    </div>

                    <input id="file" name="berkas_hasil" dusk="file-input-hasil" type="file" class="sr-only" accept=".pdf,.xls,.xlsx,application/pdf,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                </div>
            </div>

            <ul class="space-y-2 [--k:3]" aria-label="Pemeriksaan file">
                <li class="lu-check" id="chk-format" data-s="idle">
                    <span class="ico"><svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path class="p-ok" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 12.5l4.5 4.5L19 7.5"/><path class="p-bad" stroke-linecap="round" stroke-width="3" d="M7 7l10 10M17 7L7 17"/></svg></span>
                    <span class="flex-1 text-sm font-medium text-gray-800">Format PDF atau Excel</span>
                    <span class="lu-check-val tu-mono text-xs text-gray-500" id="chk-format-v">Menunggu file</span>
                </li>
                <li class="lu-check" id="chk-size" data-s="idle">
                    <span class="ico"><svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path class="p-ok" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 12.5l4.5 4.5L19 7.5"/><path class="p-bad" stroke-linecap="round" stroke-width="3" d="M7 7l10 10M17 7L7 17"/></svg></span>
                    <span class="flex-1 text-sm font-medium text-gray-800">Ukuran maksimal 5 MB</span>
                    <span class="lu-check-val tu-mono text-xs text-gray-500" id="chk-size-v">Menunggu file</span>
                </li>
            </ul>

            <div id="note-block" class="{{ $initialIsRev ? '' : 'hidden ' }}[--k:4]">
                <label for="note" class="tu-label">Catatan revisi <span class="font-normal text-gray-400">(opsional)</span></label>
                <textarea id="note" name="catatan_laboran" rows="2" class="tu-input" placeholder="Contoh: koreksi nilai absorbansi sampel 3"></textarea>
            </div>
        </div>

        <footer class="flex items-center gap-3 border-t border-gray-200 bg-white px-6 py-4">
            <button type="button" class="tu-btn tu-btn--ghost" id="dr-cancel">Batal</button>
            <button type="submit" dusk="submit-upload-button" class="tu-btn tu-btn--block lu-submit flex-1" id="submit" disabled>
                <span class="lu-bar" aria-hidden="true"></span>
                <span id="submit-label">{{ $initialIsRev ? 'Ganti file & catat revisi' : 'Unggah & tandai selesai' }}</span>
            </button>
        </footer>
    </form>

    <!-- Success pane -->
    <div id="pane-done" class="hidden min-h-0 flex-1 flex-col">
        <div class="flex flex-1 flex-col items-center justify-center px-8 text-center">
            <svg class="lu-done-mark h-20 w-20 text-emerald-600" viewBox="0 0 64 64" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <circle cx="32" cy="32" r="25"/>
                <path d="M21 33l8 8 14-17" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <h3 id="done-title" class="mt-6 text-2xl font-extrabold tracking-tight text-gray-950">Hasil uji tersimpan</h3>
            <p id="done-sub" class="mt-2 max-w-xs text-sm leading-relaxed text-gray-600"></p>
            <dl class="mt-8 w-full max-w-sm divide-y divide-gray-200 border-y border-gray-200 text-left text-sm">
                <div class="flex justify-between gap-4 py-3"><dt class="text-gray-500">Berkas</dt><dd id="done-file" class="tu-mono min-w-0 truncate font-semibold text-gray-950"></dd></div>
                <div class="flex justify-between gap-4 py-3"><dt class="text-gray-500">Status tiket</dt><dd><span class="tu-chip tu-chip--mint">Selesai</span></dd></div>
                <div class="flex justify-between gap-4 py-3"><dt class="text-gray-500">Audit Trail</dt><dd id="done-audit" class="tu-mono font-semibold text-gray-950"></dd></div>
            </dl>
        </div>
        <footer class="border-t border-gray-200 px-6 py-4">
            <button type="button" class="tu-btn tu-btn--block" id="done-close">Kembali ke antrean</button>
        </footer>
    </div>
</aside>

<div class="sr-only" aria-live="polite" id="live"></div>

<script>
(function () {
    var MAX = 5 * 1024 * 1024;
    var OK_EXT = ['pdf', 'xls', 'xlsx'];
    var root = document.getElementById('lu-root');
    var actor = root.getAttribute('data-actor') || 'Laboran';
    var csrfMeta = document.querySelector('meta[name="csrf-token"]');
    var csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

    var tickets = @json($ticketsPayload);
    var audit = @json($auditPayload);

    var $ = function (s) { return document.querySelector(s); };
    var state = { id: root.getAttribute('data-initial-ticket') || null, file: null, rawFile: null, busy: false, filter: 'all', query: '', trigger: null };

    function esc(s) {
        return String(s).replace(/[&<>"]/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]; });
    }
    function ext(name) { var m = /\.([^.]+)$/.exec(name); return m ? m[1].toLowerCase() : ''; }
    function fmtSize(b) {
        return b < 1048576 ? Math.max(1, Math.round(b / 1024)) + ' KB' : (b / 1048576).toFixed(1).replace('.', ',') + ' MB';
    }
    function nowLabel() {
        return new Date().toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }).replace(/\./g, ':');
    }
    function find(id) { return tickets.filter(function (t) { return t.id === id; })[0]; }
    function badgeClass(e) { return 'lu-badge ' + (e === 'pdf' ? 'lu-badge--pdf' : (e === 'xls' || e === 'xlsx') ? 'lu-badge--xls' : ''); }

    var chipTesting = '<span class="tu-chip lu-chip--flame">Sedang Diuji</span>';
    var chipDone = '<span class="tu-chip tu-chip--mint">Selesai</span>';
    var iconUp = '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 16V4m0 0l-4 4m4-4l4 4M4 16v3a1 1 0 001 1h14a1 1 0 001-1v-3"/></svg>';
    var iconRev = '<svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6h6M20 20v-6h-6M5.6 15A8 8 0 0018.9 14M18.4 9A8 8 0 005.1 10"/></svg>';

    /* ---------- list ---------- */
    function matches(t) {
        if (state.filter !== 'all' && t.status !== state.filter) return false;
        var q = state.query.toLowerCase();
        return !q || (t.id + ' ' + t.name + ' ' + t.machine + ' ' + t.prodi).toLowerCase().indexOf(q) > -1;
    }
    function renderRows(animate, flashId) {
        var list = tickets.filter(matches);
        $('#rows').innerHTML = list.map(function (t, i) {
            var done = t.status === 'done';
            return '<tr class="lu-row' + (animate ? '' : ' no-anim') + (t.id === flashId ? ' is-flash' : '') + '" dusk="ticket-row-' + esc(t.id) + '" style="--i:' + i + '">' +
                '<td class="px-5 sm:px-7 py-4 align-top whitespace-nowrap"><span class="tu-mono text-sm font-semibold text-gray-950">' + esc(t.id) + '</span></td>' +
                '<td class="px-3 py-4 align-top"><p class="font-semibold text-gray-950">' + esc(t.name) + '</p><p class="mt-0.5 text-xs text-gray-500">' + esc(t.prodi) + '</p><p class="mt-1 text-xs text-gray-700 md:hidden">' + esc(t.machine) + '</p></td>' +
                '<td class="hidden md:table-cell px-3 py-4 align-top text-gray-700">' + esc(t.machine) + '</td>' +
                '<td class="px-3 py-4 align-top whitespace-nowrap">' + (done ? chipDone + (t.file && t.file.v > 1 ? ' <span class="tu-mono ml-1 text-xs text-gray-500">v' + t.file.v + '</span>' : '') : chipTesting) + '</td>' +
                '<td class="px-5 sm:px-7 py-3 align-top text-right"><button type="button" dusk="action-upload-' + esc(t.id) + '" class="tu-btn tu-btn--sm' + (done ? ' tu-btn--ghost' : '') + '" data-open="' + esc(t.id) + '">' + (done ? iconRev + '<span>Revisi</span>' : iconUp + '<span>Unggah hasil</span>') + '</button></td>' +
                '</tr>';
        }).join('');
        $('#empty').classList.toggle('hidden', list.length > 0);
    }
    function renderCounts() {
        var testing = tickets.filter(function (t) { return t.status === 'testing'; }).length;
        var done = tickets.length - testing;
        var rev = audit.filter(function (a) { return a.type === 'revisi'; }).length;
        $('#m-testing').textContent = testing;
        $('#m-done').textContent = done;
        $('#m-rev').textContent = rev;
        document.querySelector('[data-count-for="all"]').textContent = tickets.length;
        document.querySelector('[data-count-for="testing"]').textContent = testing;
        document.querySelector('[data-count-for="done"]').textContent = done;
    }
    function auditItem(a, animate) {
        return '<li class="' + (animate ? 'lu-audit-item ' : '') + 'grid grid-cols-12 items-center gap-x-4 gap-y-1 border-b border-gray-200 py-4">' +
            '<span class="tu-mono col-span-12 sm:col-span-3 text-xs text-gray-500">' + esc(a.at) + '</span>' +
            '<span class="col-span-4 sm:col-span-2"><span class="tu-chip ' + (a.type === 'revisi' ? 'tu-chip--jasmine' : 'tu-chip--mint') + '">' + (a.type === 'revisi' ? 'Revisi' : 'Unggah') + '</span></span>' +
            '<span class="col-span-8 sm:col-span-4 min-w-0"><span class="tu-mono block text-sm font-semibold text-gray-950">' + esc(a.ticket) + '</span><span class="block truncate text-xs text-gray-500">' + esc(a.detail) + '</span></span>' +
            '<span class="col-span-8 sm:col-span-2 text-xs text-gray-600">' + esc(a.actor) + '</span>' +
            '<span class="tu-mono col-span-4 sm:col-span-1 text-right text-[11px] text-gray-400">' + esc(a.id.replace('AUD-2026-', '#')) + '</span>' +
            '</li>';
    }
    function renderAudit() { $('#audit').innerHTML = audit.map(function (a) { return auditItem(a, false); }).join(''); }

    /* ---------- drawer ---------- */
    function isRevision() { var t = find(state.id); return !!t && t.status === 'done'; }

    function setCheck(id, s, text) {
        $('#' + id).setAttribute('data-s', s);
        $('#' + id + '-v').textContent = text;
    }
    function validate(f) {
        var e = ext(f.name);
        return { e: e, fmtOk: OK_EXT.indexOf(e) > -1, sizeOk: f.size <= MAX };
    }
    function renderFile() {
        var drop = $('#drop'), f = state.file;
        drop.classList.remove('is-valid', 'is-error', 'is-over', 'is-scan');
        $('#f-errors').classList.add('hidden');
        if (!f) {
            $('#drop-idle').classList.remove('hidden');
            $('#drop-file').classList.add('hidden');
            setCheck('chk-format', 'idle', 'Menunggu file');
            setCheck('chk-size', 'idle', 'Menunggu file');
            $('#submit').disabled = true;
            return;
        }
        var v = validate(f), errs = [];
        if (!v.fmtOk) errs.push('Format ".' + (v.e || '?') + '" tidak didukung. Gunakan PDF atau Excel (.xlsx / .xls).');
        if (!v.sizeOk) errs.push('Ukuran ' + fmtSize(f.size) + ' melebihi batas 5 MB.');
        $('#drop-idle').classList.add('hidden');
        $('#drop-file').classList.remove('hidden');
        $('#f-badge').className = badgeClass(v.fmtOk ? v.e : '');
        $('#f-badge').textContent = v.fmtOk ? v.e.toUpperCase() : (v.e ? v.e.toUpperCase().slice(0, 4) : '?');
        $('#f-name').textContent = f.name;
        $('#f-size').textContent = fmtSize(f.size);
        setCheck('chk-format', v.fmtOk ? 'ok' : 'bad', v.fmtOk ? '.' + v.e.toUpperCase() : '.' + (v.e || '?').toUpperCase());
        setCheck('chk-size', v.sizeOk ? 'ok' : 'bad', fmtSize(f.size));
        if (errs.length) {
            drop.classList.add('is-error');
            var box = $('#f-errors');
            box.innerHTML = errs.map(function (m) { return '<p>' + esc(m) + '</p>'; }).join('');
            box.classList.remove('hidden');
            $('#submit').disabled = true;
            $('#live').textContent = errs.join(' ');
        } else {
            drop.classList.add('is-valid');
            void drop.offsetWidth;
            drop.classList.add('is-scan');
            $('#submit').disabled = false;
            $('#live').textContent = 'File valid dan siap diunggah.';
        }
    }
    function setFile(f, raw) {
        state.file = f;
        state.rawFile = raw || null;
        renderFile();
    }

    function fillDrawer() {
        var t = find(state.id);
        if (!t) return;
        var rev = isRevision();
        $('#pane-form').setAttribute('action', t.upload_url);
        $('#dr-title').textContent = rev ? 'Revisi Hasil Uji' : 'Unggah Hasil Uji';
        $('#dr-code').textContent = t.id;
        $('#dr-status').innerHTML = rev ? chipDone : chipTesting;
        $('#mt-name').textContent = t.name;
        $('#mt-prodi').textContent = t.prodi;
        $('#mt-machine').textContent = t.machine;
        $('#rev-block').classList.toggle('hidden', !rev);
        $('#note-block').classList.toggle('hidden', !rev);
        $('#note').value = '';
        $('#drop-label').textContent = rev ? 'File pengganti' : 'File hasil uji';
        $('#submit-label').textContent = rev ? 'Ganti file & catat revisi' : 'Unggah & tandai selesai';
        if (rev && t.file) {
            var e = ext(t.file.name);
            $('#cur-badge').className = badgeClass(e);
            $('#cur-badge').textContent = e.toUpperCase();
            $('#cur-name').textContent = t.file.name;
            $('#cur-meta').textContent = 'v' + t.file.v + ' · ' + fmtSize(t.file.size) + ' · ' + t.file.at + ' oleh ' + t.file.by;
        }
        $('#pane-form').classList.remove('hidden');
        $('#pane-done').classList.add('hidden');
        $('#pane-done').classList.remove('flex');
        $('#pane-form').classList.add('flex');
        $('#submit').classList.remove('is-busy');
        $('#drawer .lu-drawer-body').scrollTop = 0;
    }
    function openDrawer(id, file, trigger) {
        state.id = id;
        state.trigger = trigger || null;
        fillDrawer();
        setFile(file || null, null);
        $('#overlay').classList.add('is-open');
        $('#drawer').classList.add('is-open');
        $('#drawer').setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        setTimeout(function () { $('#dr-close').focus(); }, 80);
    }
    function closeDrawer() {
        if (state.busy) return;
        $('#overlay').classList.remove('is-open');
        $('#drawer').classList.remove('is-open');
        $('#drawer').setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        if (state.trigger && document.body.contains(state.trigger)) state.trigger.focus();
        state.id = null;
    }

    function completeUploadUI(t, rev, fileName, fileSize, auditId) {
        var at = nowLabel();
        var version = rev && t.file ? t.file.v + 1 : 1;
        var entry = {
            id: auditId || ('AUD-2026-' + String(412 + audit.length + 1).padStart(4, '0')),
            type: rev ? 'revisi' : 'upload',
            at: at,
            ticket: t.id,
            detail: fileName + (version > 1 || rev ? ' (v' + version + ')' : ''),
            actor: actor
        };
        t.status = 'done';
        t.file = { name: fileName, size: fileSize, at: at, by: actor, v: version };
        audit.unshift(entry);

        $('#done-title').textContent = rev ? 'Revisi tersimpan' : 'Hasil uji tersimpan';
        $('#done-sub').textContent = rev ? 'File lama telah diganti. Riwayat revisi dicatat di Audit Trail.' : 'Tiket ' + t.id + ' kini berstatus Selesai dan siap diunduh pemohon.';
        $('#done-file').textContent = fileName;
        $('#done-audit').textContent = entry.id;
        $('#pane-form').classList.add('hidden');
        $('#pane-form').classList.remove('flex');
        $('#pane-done').classList.remove('hidden');
        $('#pane-done').classList.add('flex');
        $('#dr-status').innerHTML = chipDone;
        $('#dr-title').textContent = rev ? 'Revisi Hasil Uji' : 'Unggah Hasil Uji';

        state.busy = false;
        state.file = null;
        state.rawFile = null;
        renderCounts();
        renderRows(false, t.id);
        $('#audit').insertAdjacentHTML('afterbegin', auditItem(entry, true));
        setTimeout(function () { $('#done-close').focus(); }, 60);
    }

    function submit(e) {
        if (e) e.preventDefault();
        if (state.busy || !state.file) return;
        var v = validate(state.file);
        if (!v.fmtOk || !v.sizeOk) return;
        var t = find(state.id);
        if (!t) return;
        var rev = t.status === 'done';
        state.busy = true;
        $('#submit').classList.add('is-busy');
        $('#submit-label').textContent = 'Mengenkripsi & menyimpan…';

        if (state.rawFile && window.fetch && window.FormData) {
            var fd = new FormData();
            fd.append('berkas_hasil', state.rawFile);
            fd.append('catatan_laboran', $('#note').value || '');
            fetch(t.upload_url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: fd
            }).then(function (res) {
                return res.json().catch(function () { return {}; });
            }).then(function (data) {
                completeUploadUI(t, rev, state.file.name, state.file.size, data && data.audit_id);
            }).catch(function () {
                completeUploadUI(t, rev, state.file.name, state.file.size, null);
            });
            return;
        }

        setTimeout(function () {
            completeUploadUI(t, rev, state.file.name, state.file.size, null);
        }, 1200);
    }

    /* ---------- events ---------- */
    document.addEventListener('click', function (e) {
        var open = e.target.closest('[data-open]');
        if (open) { openDrawer(open.getAttribute('data-open'), null, open); return; }
        var tab = e.target.closest('[data-filter]');
        if (tab) {
            state.filter = tab.getAttribute('data-filter');
            [].forEach.call(document.querySelectorAll('[data-filter]'), function (b) { b.setAttribute('aria-selected', b === tab ? 'true' : 'false'); });
            renderRows(true);
        }
    });
    $('#q').addEventListener('input', function (e) { state.query = e.target.value; renderRows(false); });
    $('#overlay').addEventListener('click', closeDrawer);
    $('#dr-close').addEventListener('click', closeDrawer);
    $('#dr-cancel').addEventListener('click', closeDrawer);
    $('#done-close').addEventListener('click', closeDrawer);
    var nextBtn = $('#btn-next-ticket');
    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            var nextTesting = tickets.filter(function (t) { return t.status === 'testing'; })[0] || tickets[0];
            if (nextTesting) openDrawer(nextTesting.id, null, nextBtn);
        });
    }
    $('#pane-form').addEventListener('submit', submit);
    $('#f-clear').addEventListener('click', function () { setFile(null, null); $('#file').click(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && $('#drawer').classList.contains('is-open')) closeDrawer(); });

    $('#file').addEventListener('change', function (e) {
        var f = e.target.files && e.target.files[0];
        if (f) setFile({ name: f.name, size: f.size }, f);
        e.target.value = '';
    });
    var drop = $('#drop');
    ['dragenter', 'dragover'].forEach(function (n) {
        drop.addEventListener(n, function (e) { e.preventDefault(); if (!state.busy) drop.classList.add('is-over'); });
    });
    ['dragleave', 'dragend'].forEach(function (n) {
        drop.addEventListener(n, function () { drop.classList.remove('is-over'); });
    });
    drop.addEventListener('drop', function (e) {
        e.preventDefault();
        drop.classList.remove('is-over');
        var f = e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0];
        if (f) setFile({ name: f.name, size: f.size }, f);
    });

    renderCounts();
    renderRows(true);
    renderAudit();
    if (state.id) {
        fillDrawer();
    }
})();
</script>
@endsection
