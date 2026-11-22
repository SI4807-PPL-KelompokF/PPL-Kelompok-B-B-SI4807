<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resi E-Ticket Pengujian — {{ $tiket->nomor_tiket }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="preconnect" href="https://api.fontshare.com">
    <link rel="preconnect" href="https://cdn.fontshare.com" crossorigin>
    <link href="https://api.fontshare.com/v2/css?f[]=general-sans@400,500,600,700&display=swap" rel="stylesheet">
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-900 font-sans antialiased py-6 px-4 sm:py-10 sm:px-6 print:p-0 print:bg-white">
    @php
        $isDigital = $tiket->jenis_sampel === 'digital';
        $submittedLabel = ($tiket->submitted_at ?: $tiket->created_at ?: now())->translatedFormat('d M Y, H:i') . ' WIB';
        $tanggalUjiLabel = $tiket->tanggal_uji ? $tiket->tanggal_uji->translatedFormat('d F Y') : '-';
        $shortToken = substr($tiket->barcode_token ?: strtoupper(hash('crc32b', $tiket->nomor_tiket . 'TELYUJI')), 0, 16);
    @endphp

    <!-- Non-printable Top Action Bar -->
    <div class="max-w-[794px] mx-auto mb-5 flex flex-wrap items-center justify-between gap-3 print:hidden">
        <a href="{{ route('pengajuan.show', $tiket->nomor_tiket) }}"
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-600 hover:text-racing-red transition-colors">
            <span aria-hidden="true">&larr;</span>
            <span>Kembali ke Pelacakan</span>
        </a>

        <div class="flex items-center gap-2">
            <a href="{{ route('pengajuan.resi.download', $tiket->nomor_tiket) }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 border border-gray-300 rounded-[3px] text-xs font-semibold text-gray-700 bg-white hover:border-gray-900 hover:text-gray-950 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <span>Unduh PDF</span>
            </a>

            <button type="button" onclick="window.print()"
                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-racing-red text-white rounded-[3px] text-xs font-semibold hover:bg-tiger-flame transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak Resi</span>
            </button>
        </div>
    </div>

    <!-- Minimalist A4 Printable Slip Container -->
    <main class="max-w-[794px] mx-auto bg-white border border-gray-200 shadow-sm overflow-hidden print:shadow-none print:border-none print:max-w-full">
        <!-- Subtle Dual-Tone Brand Accent Line -->
        <div class="flex h-1 w-full" aria-hidden="true">
            <div class="w-4/5 bg-racing-red"></div>
            <div class="w-1/5 bg-tiger-flame"></div>
        </div>

        <div class="p-8 sm:p-11 print:p-6 space-y-7">
            <!-- 1. Minimalist Institutional Header with Official Telyuji Logo -->
            <header class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-gray-200 pb-6">
                <div class="flex items-center gap-3.5">
                    <img src="{{ asset('images/logo-telyuji.png') }}"
                         alt="Logo Telyuji"
                         width="44"
                         height="44"
                         class="h-11 w-11 shrink-0 object-contain">
                    <div class="leading-tight">
                        <div class="flex items-center gap-2">
                            <span class="text-xl font-extrabold tracking-tight text-gray-950">Telyuji</span>
                            <span class="text-gray-300 text-xs" aria-hidden="true">&middot;</span>
                            <span class="text-[11px] font-medium text-gray-500">Teliti dan Teruji</span>
                        </div>
                        <p class="text-xs font-semibold text-gray-700 mt-0.5">
                            Laboratorium Riset Terpadu &middot; Telkom University
                        </p>
                    </div>
                </div>

                <div class="sm:text-right space-y-1.5">
                    <h1 class="text-[10px] font-bold tracking-[0.12em] text-gray-900 uppercase">
                        RESI E-TICKET &amp; BUKTI PENDAFTARAN SPESIMEN
                    </h1>
                    <div>
                        <span class="inline-block px-2.5 py-0.5 text-[10px] font-bold tracking-wider uppercase rounded-[2px] {{ $isDigital ? 'bg-frosted-mint text-gray-900' : 'bg-jasmine text-gray-900' }}">
                            {{ $isDigital ? 'Berkas Digital · E-Ticket' : 'Spesimen Fisik · Tempel Resi' }}
                        </span>
                    </div>
                </div>
            </header>

            <!-- 2. Hero Focal Strip: Ticket Number + Scannable Code 128 Barcode -->
            <section class="border border-gray-200 border-l-[3px] border-l-racing-red bg-white grid grid-cols-1 sm:grid-cols-12 items-center">
                <div class="sm:col-span-7 px-5 py-4 space-y-1">
                    <p class="text-[10px] font-bold tracking-[0.12em] text-gray-400 uppercase">
                        Nomor Identifikasi Tiket
                    </p>
                    <p class="text-2xl sm:text-[26px] font-extrabold tracking-tight text-racing-red font-mono leading-none py-0.5">
                        {{ $tiket->nomor_tiket }}
                    </p>
                    <p class="text-[11px] text-gray-500 pt-1">
                        Terdaftar <span class="font-medium text-gray-700">{{ $submittedLabel }}</span>
                        <span class="mx-1.5 text-gray-300">&middot;</span>
                        Rencana Uji <span class="font-medium text-gray-700">{{ $tanggalUjiLabel }}</span>
                    </p>
                </div>

                <div class="sm:col-span-5 border-t sm:border-t-0 sm:border-l border-gray-200 px-4 py-3.5 flex items-center justify-center bg-white">
                    <div class="w-full flex justify-center [&_svg]:max-w-[230px] [&_svg]:w-full [&_svg]:h-auto">
                        {!! $barcodeSvg !!}
                    </div>
                </div>
            </section>

            <!-- 3. Two-Column Editorial Specification Grid -->
            <section class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                <!-- Column 01: Applicant Identity -->
                <div class="space-y-4">
                    <h2 class="text-[10px] font-bold tracking-[0.14em] text-gray-900 uppercase border-b border-gray-200 pb-2">
                        01 &nbsp;Identitas Pemohon
                    </h2>
                    <dl class="space-y-3 text-xs">
                        <div>
                            <dt class="text-[10px] font-medium text-gray-400 uppercase tracking-wider">Nama Lengkap</dt>
                            <dd class="mt-0.5 text-sm font-bold text-gray-950">{{ $tiket->nama_pemohon }}</dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-medium text-gray-400 uppercase tracking-wider">Nomor Induk (NIM / NIP)</dt>
                            <dd class="mt-0.5 font-semibold text-gray-900 font-mono">{{ $tiket->nim_nip }}</dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-medium text-gray-400 uppercase tracking-wider">Fakultas / Unit</dt>
                            <dd class="mt-0.5 font-medium text-gray-800">{{ $tiket->fakultas }}</dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-medium text-gray-400 uppercase tracking-wider">Program Studi</dt>
                            <dd class="mt-0.5 font-medium text-gray-800">{{ $tiket->prodi ?: '-' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Column 02: Lab Service Specification -->
                <div class="space-y-4">
                    <h2 class="text-[10px] font-bold tracking-[0.14em] text-gray-900 uppercase border-b border-gray-200 pb-2">
                        02 &nbsp;Spesifikasi Layanan Lab
                    </h2>
                    <dl class="space-y-3 text-xs">
                        <div>
                            <dt class="text-[10px] font-medium text-gray-400 uppercase tracking-wider">Nama / Label Sampel</dt>
                            <dd class="mt-0.5 text-sm font-bold text-gray-950">{{ $tiket->nama_sampel }}</dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-medium text-gray-400 uppercase tracking-wider">Instrumen Pengujian</dt>
                            <dd class="mt-0.5 font-medium text-gray-800">{{ $tiket->instrumen }}</dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-medium text-gray-400 uppercase tracking-wider">Lokasi Laboratorium</dt>
                            <dd class="mt-0.5 font-medium text-gray-800">{{ $tiket->laboratorium }}</dd>
                        </div>
                        <div>
                            <dt class="text-[10px] font-medium text-gray-400 uppercase tracking-wider">Rencana Tanggal Pengujian</dt>
                            <dd class="mt-0.5 font-semibold text-gray-900 font-mono">{{ $tanggalUjiLabel }}</dd>
                        </div>
                    </dl>
                </div>
            </section>

            <!-- 4. Sample Notes & Optional Cloud Link (Minimalist Left-Accent Surface) -->
            <section class="bg-gray-50 border-l-2 border-tiger-flame px-4 py-3.5 space-y-2.5 text-xs">
                <div>
                    <p class="text-[10px] font-bold tracking-wider text-gray-500 uppercase">Karakteristik &amp; Deskripsi Sampel</p>
                    <p class="text-gray-700 mt-0.5 leading-relaxed">{{ $tiket->deskripsi_sampel ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold tracking-wider text-gray-500 uppercase">Tujuan Pengujian Riset</p>
                    <p class="text-gray-700 mt-0.5 leading-relaxed">{{ $tiket->tujuan_pengujian ?: '-' }}</p>
                </div>
                @if ($isDigital && $tiket->tautan_file_cloud)
                    <div class="pt-1 border-t border-gray-200/80">
                        <p class="text-[10px] font-bold tracking-wider text-gray-500 uppercase">Tautan Berkas Cloud</p>
                        <p class="text-racing-red font-mono text-[11px] break-all mt-0.5">{{ $tiket->tautan_file_cloud }}</p>
                    </div>
                @endif
            </section>

            <!-- 5. Concise Operational Guidelines -->
            <section class="space-y-2">
                <h2 class="text-[10px] font-bold tracking-[0.14em] text-gray-900 uppercase border-b border-gray-200 pb-2">
                    03 &nbsp;Ketentuan Layanan &amp; Logistik
                </h2>
                <ol class="text-[11px] text-gray-600 space-y-1 list-decimal list-inside leading-relaxed">
                    <li>Layanan pengujian laboratorium ini bebas biaya bagi seluruh sivitas akademika Telkom University.</li>
                    <li>Khusus spesimen fisik, tempelkan resi ini pada wadah sampel sebelum diserahkan ke Laboran (maksimal 3&times;24 jam).</li>
                    <li>Sisa spesimen fisik wajib diambil maksimal 7 (tujuh) hari kerja setelah Laporan Hasil Uji (LHU) diterbitkan.</li>
                    <li>Pantau progres pengujian secara real-time melalui Visual Timeline Tracking pada portal Telyuji.</li>
                </ol>
            </section>

            <!-- 6. Minimalist Open Signature Row -->
            <section class="border-t border-dashed border-gray-300 pt-6 grid grid-cols-2 gap-10 text-center text-xs">
                <div class="flex flex-col items-center">
                    <p class="font-bold text-gray-900">Pemohon Pengujian</p>
                    <p class="text-[10px] text-gray-400 mt-0.5">{{ $isDigital ? 'Tanda tangan & tanggal pengajuan' : 'Tanda tangan & tanggal penyerahan' }}</p>
                    <div class="h-14 w-48 border-b border-gray-400"></div>
                    <p class="font-bold text-gray-900 mt-2">{{ $tiket->nama_pemohon }}</p>
                    <p class="text-[10px] text-gray-400 font-mono">NIM / NIP: {{ $tiket->nim_nip }}</p>
                </div>

                <div class="flex flex-col items-center">
                    <p class="font-bold text-gray-900">{{ $isDigital ? 'Petugas Laboran Verifikator' : 'Petugas Laboran Penerima' }}</p>
                    <p class="text-[10px] text-gray-400 mt-0.5">{{ $isDigital ? 'Verifikasi berkas & persetujuan lab' : 'Paraf & stempel penerimaan fisik' }}</p>
                    <div class="h-14 w-48 border-b border-gray-400"></div>
                    <p class="font-bold text-gray-900 mt-2">Tim Laboran Telkom University</p>
                    <p class="text-[10px] text-gray-400">{{ $tiket->laboratorium }}</p>
                </div>
            </section>

            <!-- 7. Minimalist Institutional Footer & Verification Token -->
            <footer class="pt-4 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-[10px] text-gray-400">
                <div>
                    <p class="font-semibold text-gray-500">Telyuji &middot; Laboratorium Riset Terpadu Telkom University, Bandung</p>
                    <p>Diterbitkan otomatis pada {{ now()->translatedFormat('d M Y, H:i') }} WIB.</p>
                </div>
                <div class="sm:text-right font-mono">
                    <p class="font-bold text-gray-600">TOKEN: {{ $shortToken }}</p>
                    <p class="text-[9px] tracking-wider uppercase text-gray-400">Terverifikasi Digital</p>
                </div>
            </footer>
        </div>
    </main>
</body>
</html>
