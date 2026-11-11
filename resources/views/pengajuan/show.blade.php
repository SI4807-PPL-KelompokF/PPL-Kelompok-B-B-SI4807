@extends('layouts.app')

@section('content')
<div class="relative overflow-hidden bg-gray-50 min-h-screen py-8 sm:py-12">
    <x-bg-pattern uid="timeline-pat" />

    <div class="relative max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <!-- Breadcrumb & Top Bar -->
        <div class="tu-reveal space-y-2">
            <nav class="flex items-center gap-2 text-xs text-gray-500" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="transition-colors hover:text-racing-red">Dashboard</a>
                <span>&rarr;</span>
                <span class="font-medium text-gray-700">Pelacakan Linimasa</span>
                <span>&rarr;</span>
                <span class="font-semibold text-gray-900 tu-mono">{{ $tiket->nomor_tiket }}</span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-gray-950 tu-mono" dusk="text-nomor-tiket">
                            {{ $tiket->nomor_tiket }}
                        </h1>
                        @if ($tiket->isCompleted())
                            <span class="tu-chip tu-chip--mint text-xs">Selesai &amp; LHU Terbit</span>
                        @elseif ($tiket->isDitolak())
                            <span class="tu-chip bg-red-100 text-red-800 border-red-300 text-xs">Pengajuan Ditolak</span>
                        @elseif ($tiket->status === 'Sedang Diuji')
                            <span class="tu-chip lu-chip--flame text-xs">Sedang Diuji di Lab</span>
                        @elseif ($tiket->status === 'Dalam Antrean')
                            <span class="tu-chip tu-chip--jasmine text-xs">Dalam Antrean Fisik</span>
                        @else
                            <span class="tu-chip tu-chip--cream text-xs">Menunggu Penyerahan Fisik</span>
                        @endif
                    </div>
                    <p class="mt-1 text-sm text-gray-600">
                        {{ $tiket->instrumen }} &middot; {{ $tiket->nama_sampel }} &middot; {{ $tiket->laboratorium }}
                    </p>
                </div>

                <!-- Action Button Group -->
                <div class="flex flex-wrap items-center gap-2.5">
                    <a href="{{ route('pengajuan.resi.download', $tiket->nomor_tiket) }}" dusk="btn-unduh-resi" class="tu-btn tu-btn--sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        <span>Unduh Resi PDF</span>
                    </a>

                    <a href="{{ route('pengajuan.resi.print', $tiket->nomor_tiket) }}" target="_blank" dusk="btn-cetak-resi" class="tu-btn tu-btn--ghost tu-btn--sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <span>Cetak Barcode</span>
                    </a>

                    @if ($tiket->isCompleted() && $tiket->dokumenHasil)
                        <a href="{{ route('pemohon.pengujian.download', $tiket->nomor_tiket) }}" dusk="btn-unduh-lhu" class="tu-btn tu-btn--sm bg-emerald-600 hover:bg-emerald-700 text-white border-emerald-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Unduh LHU Resmi</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        @if (session('status'))
            <div class="tu-alert" role="alert" dusk="alert-sukses-pengajuan">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            </div>
        @endif

        @if ($tiket->isDitolak())
            <!-- Banner Penolakan Spesimen (PBI-12 / SC-12) -->
            <div class="bg-red-50 border-l-4 border-racing-red p-5 rounded-r-lg shadow-sm tu-reveal">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center shrink-0 text-racing-red font-bold">
                        &times;
                    </div>
                    <div class="space-y-1">
                        <h2 class="text-base font-bold text-red-950">Pengajuan Spesimen Ditolak oleh Laboran</h2>
                        <p class="text-xs text-red-700 leading-relaxed">
                            Alasan penolakan: <span class="font-semibold">{{ $tiket->alasan_penolakan ?: 'Spesimen tidak sesuai SOP atau telah melewati batas waktu penyerahan fisik 3x24 jam.' }}</span>
                        </p>
                        @if ($tiket->rejected_at)
                            <p class="text-[11px] text-red-500 pt-1">
                                Waktu penolakan: {{ $tiket->rejected_at->translatedFormat('d F Y, H:i') }} WIB
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <!-- ============================================================== -->
        <!-- PBI-09: KOMPONEN VISUAL TIMELINE TRACKING                      -->
        <!-- ============================================================== -->
        <section class="bg-white border border-gray-200 rounded-lg p-6 sm:p-8 shadow-sm tu-reveal" aria-labelledby="timeline-heading" dusk="section-timeline-tracking">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border-b border-gray-100 pb-5">
                <div>
                    <h2 id="timeline-heading" class="text-lg font-bold tracking-tight text-gray-950 flex items-center gap-2">
                        <span>Visual Timeline Tracking</span>
                        <span class="inline-block w-2 h-2 rounded-full bg-racing-red animate-ping"></span>
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Pelacakan real-time linimasa tahapan sampel laboratorium
                    </p>
                </div>
                <div class="text-xs text-gray-500 flex items-center gap-3">
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span> Selesai
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-racing-red inline-block"></span> Tahap Berjalan
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded-full bg-gray-200 inline-block"></span> Menunggu
                    </span>
                </div>
            </div>

            <!-- DESKTOP TIMELINE (Horizontal 5-Stage Stepper) -->
            <div class="hidden md:block mt-8 pt-2 pb-4">
                <div class="relative">
                    <!-- Garis Penghubung Latar Belakang -->
                    <div class="absolute top-5 left-10 right-10 h-1 bg-gray-200 -z-0" aria-hidden="true"></div>

                    <!-- Garis Progres Aktif -->
                    @php
                        $stageNum = $tiket->isDitolak() ? 1 : $tiket->stageNumber();
                        // 1 = 0%, 2 = 25%, 3 = 50%, 4 = 75%, 5 = 100%
                        $progressPercent = min(100, max(0, ($stageNum - 1) * 25));
                    @endphp
                    <div class="absolute top-5 left-10 h-1 bg-racing-red transition-all duration-700 ease-out -z-0"
                         style="width: calc({{ $progressPercent }}% * 0.85);" aria-hidden="true"></div>

                    <!-- 5 Tahapan Linimasa Horizontal -->
                    <ol class="grid grid-cols-5 relative z-10 text-center" role="list">
                        @foreach ($timeline['stages'] as $index => $stage)
                            @php
                                $stepIdx = $index + 1;
                                $isCompleted = $stageNum > $stepIdx || ($stageNum === 5 && $stepIdx === 5);
                                $isCurrent = $stageNum === $stepIdx && !$tiket->isDitolak();
                                $isPending = $stageNum < $stepIdx;
                            @endphp
                            <li class="flex flex-col items-center group px-1" dusk="timeline-step-{{ $stage['key'] }}">
                                <!-- Circle Node -->
                                <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs transition-all duration-300
                                            {{ $isCompleted ? 'bg-emerald-600 text-white shadow-sm' : '' }}
                                            {{ $isCurrent ? 'bg-racing-red text-white ring-4 ring-red-100 shadow-md scale-110' : '' }}
                                            {{ $isPending ? 'bg-white border-2 border-gray-300 text-gray-400' : '' }}">
                                    @if ($isCompleted)
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                    @else
                                        <span>0{{ $stepIdx }}</span>
                                    @endif
                                </div>

                                <!-- Label & Keterangan -->
                                <div class="mt-3 space-y-1">
                                    <p class="text-xs font-bold leading-tight
                                              {{ $isCurrent ? 'text-racing-red' : ($isCompleted ? 'text-gray-900' : 'text-gray-400') }}">
                                        {{ $stage['label'] }}
                                    </p>
                                    <p class="text-[11px] text-gray-500 leading-snug line-clamp-2 max-w-[130px]">
                                        {{ $stage['desc'] }}
                                    </p>
                                    @if ($stage['timestamp'])
                                        <p class="text-[10px] text-gray-400 font-medium pt-0.5 tu-mono">
                                            {{ $stage['timestamp']->translatedFormat('d M, H:i') }}
                                        </p>
                                    @elseif ($isCurrent)
                                        <span class="inline-block px-1.5 py-0.5 bg-red-100 text-racing-red text-[9px] font-bold rounded">
                                            Aktif
                                        </span>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>

            <!-- MOBILE TIMELINE (Vertical Stepper with Timestamps - KNF-05) -->
            <div class="block md:hidden mt-6 space-y-6">
                <ol class="relative border-l-2 border-gray-200 ml-4 space-y-6" role="list">
                    @foreach ($timeline['stages'] as $index => $stage)
                        @php
                            $stepIdx = $index + 1;
                            $isCompleted = $stageNum > $stepIdx || ($stageNum === 5 && $stepIdx === 5);
                            $isCurrent = $stageNum === $stepIdx && !$tiket->isDitolak();
                            $isPending = $stageNum < $stepIdx;
                        @endphp
                        <li class="relative pl-6" dusk="timeline-mobile-step-{{ $stage['key'] }}">
                            <!-- Node Marker -->
                            <div class="absolute -left-[17px] top-0 w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs
                                        {{ $isCompleted ? 'bg-emerald-600 text-white' : '' }}
                                        {{ $isCurrent ? 'bg-racing-red text-white ring-4 ring-red-100' : '' }}
                                        {{ $isPending ? 'bg-white border-2 border-gray-300 text-gray-400' : '' }}">
                                @if ($isCompleted)
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                @else
                                    <span>0{{ $stepIdx }}</span>
                                @endif
                            </div>

                            <div class="bg-gray-50 border border-gray-200 rounded-md p-3.5 space-y-1">
                                <div class="flex items-center justify-between">
                                    <p class="text-xs font-bold {{ $isCurrent ? 'text-racing-red' : ($isCompleted ? 'text-gray-900' : 'text-gray-400') }}">
                                        {{ $stage['label'] }}
                                    </p>
                                    @if ($isCurrent)
                                        <span class="px-2 py-0.5 bg-red-100 text-racing-red text-[10px] font-bold rounded">Berlangsung</span>
                                    @elseif ($isCompleted)
                                        <span class="text-emerald-600 text-[10px] font-semibold">Selesai</span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-600">{{ $stage['desc'] }}</p>
                                @if ($stage['timestamp'])
                                    <p class="text-[10px] text-gray-400 font-medium pt-1 tu-mono">
                                        Waktu: {{ $stage['timestamp']->translatedFormat('d M Y, H:i') }} WIB
                                    </p>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>

        <!-- ============================================================== -->
        <!-- DUA PANEL RINCIAN: SPESIMEN & RESI BARCODE                     -->
        <!-- ============================================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Kolom Kiri (8 Col): Detail Spesimen & Alur Laboratorium -->
            <div class="lg:col-span-8 space-y-6">
                <!-- Rincian Spesimen -->
                <section class="bg-white border border-gray-200 rounded-lg p-6 sm:p-7 shadow-sm tu-reveal">
                    <h2 class="text-base font-bold text-gray-950 border-b border-gray-100 pb-3 mb-4">
                        Rincian Pengajuan Spesimen
                    </h2>

                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-xs">
                        <div>
                            <dt class="font-semibold text-gray-500">Nama / Label Sampel</dt>
                            <dd class="mt-0.5 font-bold text-gray-950 text-sm">{{ $tiket->nama_sampel }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-gray-500">Bentuk Pengujian</dt>
                            <dd class="mt-0.5 font-bold text-gray-950">
                                @if ($tiket->jenis_sampel === 'digital')
                                    <span class="inline-flex items-center gap-1 text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded">
                                        Berkas Digital (E-Ticket)
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-amber-900 bg-amber-100 px-2 py-0.5 rounded">
                                        Spesimen Fisik (Tempel Resi)
                                    </span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-gray-500">Instrumen Laboratorium</dt>
                            <dd class="mt-0.5 font-medium text-gray-900">{{ $tiket->instrumen }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-gray-500">Lokasi Laboratorium</dt>
                            <dd class="mt-0.5 font-medium text-gray-900">{{ $tiket->laboratorium }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-gray-500">Pemohon (NIM / NIP)</dt>
                            <dd class="mt-0.5 font-medium text-gray-900">{{ $tiket->nama_pemohon }} ({{ $tiket->nim_nip }})</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-gray-500">Fakultas &amp; Program Studi</dt>
                            <dd class="mt-0.5 font-medium text-gray-900">{{ $tiket->fakultas }} &middot; {{ $tiket->prodi ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-gray-500">Rencana Pengujian</dt>
                            <dd class="mt-0.5 font-medium text-gray-900 tu-mono">
                                {{ $tiket->tanggal_uji ? $tiket->tanggal_uji->translatedFormat('d F Y') : '-' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="font-semibold text-gray-500">Tanggal Terdaftar</dt>
                            <dd class="mt-0.5 font-medium text-gray-900 tu-mono">
                                {{ $tiket->submitted_at ? $tiket->submitted_at->translatedFormat('d F Y, H:i') : $tiket->created_at->translatedFormat('d F Y, H:i') }} WIB
                            </dd>
                        </div>

                        @if ($tiket->jenis_sampel === 'digital' && $tiket->tautan_file_cloud)
                            <div class="sm:col-span-2 pt-2 border-t border-gray-100">
                                <dt class="font-semibold text-gray-500">Tautan Berkas Cloud</dt>
                                <dd class="mt-1">
                                    <a href="{{ $tiket->tautan_file_cloud }}" target="_blank" rel="noopener noreferrer"
                                       class="text-racing-red hover:underline font-mono text-xs break-all flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                        <span>{{ $tiket->tautan_file_cloud }}</span>
                                    </a>
                                </dd>
                            </div>
                        @endif

                        <div class="sm:col-span-2 pt-2 border-t border-gray-100">
                            <dt class="font-semibold text-gray-500">Deskripsi Karakteristik Sampel</dt>
                            <dd class="mt-1 text-gray-700 leading-relaxed">{{ $tiket->deskripsi_sampel ?: '-' }}</dd>
                        </div>

                        <div class="sm:col-span-2 pt-2 border-t border-gray-100">
                            <dt class="font-semibold text-gray-500">Tujuan Pengujian Riset</dt>
                            <dd class="mt-1 text-gray-700 leading-relaxed">{{ $tiket->tujuan_pengujian ?: '-' }}</dd>
                        </div>
                    </dl>
                </section>

                <!-- Ketentuan Penyerahan Sampel & SOP Lab -->
                <section class="bg-amber-50/80 border border-amber-200 rounded-lg p-5 space-y-3 tu-reveal [--d:80ms]">
                    <div class="flex items-center gap-2 text-amber-900 font-bold text-xs uppercase tracking-wider">
                        <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Instruksi Logistik Penting Bagi Pemohon</span>
                    </div>

                    <ul class="text-xs text-amber-900/90 space-y-1.5 list-disc list-inside leading-relaxed">
                        @if ($tiket->jenis_sampel === 'fisik')
                            <li><strong>Spesimen Fisik:</strong> Harap cetak Resi Barcode dan tempelkan pada wadah sebelum diserahkan ke Laboran.</li>
                            <li><strong>Batas Penyerahan 3x24 Jam:</strong> Tiket yang tidak diserahkan sampel fisiknya dalam 3x24 jam akan otomatis ditolak (no-show) oleh sistem.</li>
                        @else
                            <li><strong>Spesimen Digital:</strong> Resi ini berfungsi sebagai E-Ticket digital. Pastikan link cloud tetap aktif dan dapat dibuka oleh laboran.</li>
                        @endif
                        <li><strong>Pengambilan Spesimen:</strong> Sampel fisik yang telah selesai diuji wajib diambil maksimal 7 (tujuh) hari kerja setelah LHU resmi diterbitkan.</li>
                        <li><strong>Fasilitas Kampus:</strong> Layanan pengujian ini 100% bebas biaya bagi seluruh civitas akademika Telkom University.</li>
                    </ul>
                </section>
            </div>

            <!-- Kolom Kanan (4 Col): Kartu Barcode & Tindakan Cepat Resi -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Kartu Resi Barcode Vektor (PBI-08) -->
                <section class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm tu-reveal [--d:120ms] text-center space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <p class="text-xs font-bold text-gray-900 uppercase tracking-wide">Resi E-Ticket Barcode</p>
                        <span class="tu-chip tu-chip--cream text-[10px]">Code 128</span>
                    </div>

                    <!-- Visual Barcode -->
                    <div class="bg-gray-50 border border-gray-200 p-4 rounded-md flex flex-col items-center justify-center">
                        <div class="w-full flex justify-center py-2" dusk="barcode-svg-container">
                            {!! $barcodeSvg !!}
                        </div>
                        <p class="text-[10px] text-gray-400 font-mono mt-1">
                            TOKEN: {{ substr($tiket->barcode_token ?: 'TELYUJI', 0, 16) }}
                        </p>
                    </div>

                    <!-- Tombol Aksi Lengkap -->
                    <div class="space-y-2 pt-2">
                        <a href="{{ route('pengajuan.resi.download', $tiket->nomor_tiket) }}" class="tu-btn tu-btn--block tu-btn--sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Unduh Resi PDF</span>
                        </a>

                        <a href="{{ route('pengajuan.resi.print', $tiket->nomor_tiket) }}" target="_blank" class="tu-btn tu-btn--ghost tu-btn--block tu-btn--sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            <span>Cetak Resi Fisik</span>
                        </a>

                        <a href="{{ route('pengajuan.resi.preview', $tiket->nomor_tiket) }}" target="_blank" class="text-xs text-gray-500 hover:text-racing-red transition-colors block pt-1">
                            Buka Pratinjau PDF di Tab Baru &rarr;
                        </a>
                    </div>
                </section>

                <!-- Riwayat Audit Trail Tiket Ini (PBI-15 Integration) -->
                @if ($tiket->auditTrails && $tiket->auditTrails->isNotEmpty())
                    <section class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm tu-reveal [--d:160ms] space-y-3">
                        <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wide border-b border-gray-100 pb-2">
                            Log Jejak Aktivitas (Audit Trail)
                        </h3>
                        <div class="space-y-3">
                            @foreach ($tiket->auditTrails->take(4) as $audit)
                                <div class="text-xs border-l-2 border-racing-red pl-3 py-0.5 space-y-0.5">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-gray-900">{{ $audit->aksi }}</span>
                                        <span class="text-[10px] text-gray-400 tu-mono">{{ $audit->created_at->format('d/m H:i') }}</span>
                                    </div>
                                    <p class="text-gray-600 text-[11px] leading-tight">{{ $audit->keterangan }}</p>
                                    <p class="text-[10px] text-gray-400">Oleh: {{ $audit->nama_aktor }}</p>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
