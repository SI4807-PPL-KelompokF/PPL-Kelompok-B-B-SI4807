<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resi Barcode Pengujian - {{ $tiket->nomor_tiket }}</title>
    <link rel="preconnect" href="https://api.fontshare.com">
    <link rel="preconnect" href="https://cdn.fontshare.com" crossorigin>
    <link href="https://api.fontshare.com/v2/css?f[]=general-sans@400,500,600,700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'racing-red': '#ED1E28',
                        'tiger-flame': '#F26A2D',
                        'jasmine': '#F7D488',
                        'cream': '#EAEFB1',
                        'frosted-mint': '#E9F7CA',
                    },
                    fontFamily: {
                        sans: ['"General Sans"', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 text-gray-900 font-sans p-4 sm:p-8 print:p-0 print:bg-white">
    <!-- Non-printable top action bar -->
    <div class="max-w-3xl mx-auto mb-6 flex items-center justify-between print:hidden">
        <a href="{{ route('pengajuan.show', $tiket->nomor_tiket) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-600 hover:text-racing-red transition-colors">
            <span>&larr; Kembali ke Pelacakan</span>
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('pengajuan.resi.download', $tiket->nomor_tiket) }}" class="px-3 py-1.5 border border-gray-300 rounded text-xs font-semibold text-gray-700 bg-white hover:bg-gray-50">
                Unduh PDF Asli
            </a>
            <button type="button" onclick="window.print()" class="px-4 py-1.5 bg-racing-red text-white rounded text-xs font-semibold hover:bg-tiger-flame transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Lembar Ini</span>
            </button>
        </div>
    </div>

    <!-- Printable Slip Container -->
    <div class="max-w-3xl mx-auto bg-white border border-gray-300 p-8 sm:p-10 shadow-sm print:shadow-none print:border-none print:p-4 print:max-w-full">
        <!-- Top Colored Strip -->
        <div class="h-2 bg-racing-red -mt-8 sm:-mt-10 -mx-8 sm:-mx-10 mb-6 print:-mt-4 print:-mx-4"></div>

        <!-- Header -->
        <div class="flex items-start justify-between border-b-2 border-gray-900 pb-5 mb-6">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-2xl font-extrabold tracking-tight text-racing-red">TELYUJI</span>
                    <span class="text-gray-300">|</span>
                    <span class="text-xs font-bold text-gray-800 uppercase tracking-wider">Laboratorium Riset Terpadu</span>
                </div>
                <p class="text-xs text-gray-500 font-medium mt-0.5">Telkom University &middot; Teliti dan Teruji</p>
                <h1 class="text-sm font-extrabold text-gray-950 mt-2 uppercase tracking-wide">
                    RESI E-TICKET &amp; BUKTI PENDAFTARAN SPESIMEN
                </h1>
            </div>

            <!-- Vector Barcode -->
            <div class="text-right">
                <div class="inline-block bg-white p-1 border border-gray-200 rounded">
                    {!! $barcodeSvg !!}
                </div>
                <p class="text-[10px] text-gray-400 font-mono mt-0.5">* {{ $tiket->nomor_tiket }} *</p>
            </div>
        </div>

        <!-- Nomor Tiket Box -->
        <div class="bg-gray-50 border border-gray-200 p-4 rounded mb-6 flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Nomor Identifikasi Tiket</p>
                <p class="text-2xl font-extrabold text-racing-red font-mono">{{ $tiket->nomor_tiket }}</p>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Bentuk Spesimen</p>
                <span class="inline-block mt-0.5 px-2.5 py-1 text-xs font-bold rounded {{ $tiket->jenis_sampel === 'digital' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-900' }}">
                    {{ $tiket->jenis_sampel === 'digital' ? 'BERKAS DIGITAL' : 'SPESIMEN FISIK' }}
                </span>
            </div>
        </div>

        <!-- Data Grid -->
        <div class="grid grid-cols-2 gap-6 text-xs mb-6">
            <div class="space-y-3">
                <p class="font-bold text-gray-950 border-b border-gray-200 pb-1 uppercase tracking-wide text-[11px]">1. Identitas Pemohon</p>
                <div>
                    <span class="text-gray-500 block">Nama Pemohon:</span>
                    <span class="font-bold text-gray-900 text-sm">{{ $tiket->nama_pemohon }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block">Nomor Induk (NIM / NIP):</span>
                    <span class="font-medium text-gray-900 font-mono">{{ $tiket->nim_nip }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block">Fakultas &amp; Program Studi:</span>
                    <span class="font-medium text-gray-900">{{ $tiket->fakultas }} &middot; {{ $tiket->prodi ?: '-' }}</span>
                </div>
            </div>

            <div class="space-y-3">
                <p class="font-bold text-gray-950 border-b border-gray-200 pb-1 uppercase tracking-wide text-[11px]">2. Spesifikasi Layanan Lab</p>
                <div>
                    <span class="text-gray-500 block">Nama / Label Sampel:</span>
                    <span class="font-bold text-gray-900 text-sm">{{ $tiket->nama_sampel }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block">Instrumen Laboratorium:</span>
                    <span class="font-medium text-gray-900">{{ $tiket->instrumen }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block">Lokasi Laboratorium:</span>
                    <span class="font-medium text-gray-900">{{ $tiket->laboratorium }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block">Rencana Tanggal Pengujian:</span>
                    <span class="font-medium text-gray-900 font-mono">{{ $tiket->tanggal_uji ? $tiket->tanggal_uji->translatedFormat('d F Y') : '-' }}</span>
                </div>
            </div>
        </div>

        <!-- Deskripsi Box -->
        <div class="border border-gray-200 p-3.5 rounded text-xs bg-gray-50/50 mb-6 space-y-2">
            <div>
                <span class="font-bold text-gray-700">Karakteristik &amp; Deskripsi Sampel:</span>
                <p class="text-gray-600 mt-0.5">{{ $tiket->deskripsi_sampel ?: '-' }}</p>
            </div>
            <div>
                <span class="font-bold text-gray-700">Tujuan Pengujian:</span>
                <p class="text-gray-600 mt-0.5">{{ $tiket->tujuan_pengujian ?: '-' }}</p>
            </div>
            @if ($tiket->jenis_sampel === 'digital' && $tiket->tautan_file_cloud)
                <div>
                    <span class="font-bold text-gray-700">Tautan Berkas Cloud:</span>
                    <p class="text-racing-red font-mono break-all mt-0.5">{{ $tiket->tautan_file_cloud }}</p>
                </div>
            @endif
        </div>

        <!-- Ketentuan SOP -->
        <div class="border-t border-gray-200 pt-3 mb-8">
            <p class="text-[10px] font-bold text-gray-900 uppercase tracking-wider mb-1.5">Ketentuan Layanan &amp; Logistik:</p>
            <ol class="text-[10px] text-gray-600 space-y-1 list-decimal list-inside leading-relaxed">
                <li>Layanan pengujian ini bebas biaya bagi seluruh sivitas akademika Telkom University.</li>
                <li>Spesimen fisik wajib ditempeli resi ini sebelum diserahkan ke Laboran (maksimal 3x24 jam sejak tiket dibuat).</li>
                <li>Sisa spesimen uji wajib diambil maksimal 7 (tujuh) hari kerja setelah Laporan Hasil Uji (LHU) resmi terbit.</li>
                <li>Pelacakan progres pengujian dilakukan secara mandiri melalui Visual Timeline Tracking pada portal Telyuji.</li>
            </ol>
        </div>

        <!-- Tanda Terima -->
        <div class="grid grid-cols-2 gap-8 text-center text-xs pt-4 border-t border-gray-200">
            <div>
                <p class="text-gray-500">Pemohon Pengujian,</p>
                <div class="h-16"></div>
                <p class="font-bold text-gray-900">( {{ $tiket->nama_pemohon }} )</p>
            </div>
            <div>
                <p class="text-gray-500">Petugas Laboran Penerima Fisik,</p>
                <div class="h-16"></div>
                <p class="font-bold text-gray-900">( Tim Laboran Telkom University )</p>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 pt-4 border-t border-gray-200 text-[10px] text-gray-400 flex items-center justify-between font-mono">
            <span>Diterbitkan otomatis: {{ now()->translatedFormat('d F Y, H:i') }} WIB</span>
            <span>TOKEN: {{ substr($tiket->barcode_token ?: 'TELYUJI', 0, 16) }}</span>
        </div>
    </div>
</body>
</html>
