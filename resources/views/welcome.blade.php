@extends('layouts.app')

@section('content')
<div class="space-y-16 pb-16">
    <!-- Hero Section with TULT Background Image -->
    <section class="relative bg-gray-950 text-white overflow-hidden -mt-8">
        <!-- Background Image with Scrim Overlay -->
        <div class="absolute inset-0 z-0 bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('images/tult.jpg') }}');"></div>
        <div class="absolute inset-0 z-10 bg-gray-950/80 backdrop-brightness-75"></div>

        <div class="relative z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28 lg:py-32 flex flex-col items-center text-center">
            <!-- Main Heading -->
            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white max-w-4xl leading-tight">
                Layanan Pengujian Laboratorium Riset & Akademik Terpadu
            </h1>

            <!-- Subtitle -->
            <p class="mt-5 text-base sm:text-lg text-gray-200 max-w-2xl mx-auto leading-relaxed">
                Fasilitas pengujian instrumen modern terakreditasi lintas fakultas dan pusat riset Telkom University. Pengajuan digital, verifikasi transparan, dan penerbitan LHU resmi.
            </p>

            <!-- Action Buttons with Smooth Hover Lift -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3.5 pt-8 w-full max-w-md">
                @auth
                    <a href="{{ route('home') }}" class="group w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 text-sm font-semibold text-white bg-racing-red hover:bg-tiger-flame rounded-md shadow-lg shadow-racing-red/20 hover:shadow-tiger-flame/30 transition-all duration-300 transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-racing-red">
                        <span>Buka Dashboard Pengujian</span>
                        <span class="transition-transform duration-300 group-hover:translate-x-1">&rarr;</span>
                    </a>
                @else
                    <a href="{{ route('register') }}" class="group w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 text-sm font-semibold text-white bg-racing-red hover:bg-tiger-flame rounded-md shadow-lg shadow-racing-red/20 hover:shadow-tiger-flame/30 transition-all duration-300 transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-racing-red">
                        <span>Mulai Pengajuan Sampel</span>
                        <span class="transition-transform duration-300 group-hover:translate-x-1">&rarr;</span>
                    </a>
                    <a href="#katalog-lab" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3.5 text-sm font-semibold text-white bg-white/10 hover:bg-white/20 border border-white/30 hover:border-white/50 rounded-md backdrop-blur-sm transition-all duration-300 transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white">
                        Katalog Layanan Lab
                    </a>
                @endauth
            </div>

            <!-- Quick Sample Tracking Widget -->
            <div class="mt-12 w-full max-w-xl bg-white/95 backdrop-blur-md text-gray-900 rounded-xl p-5 shadow-2xl border border-white/30 text-left transition-all duration-300 hover:shadow-racing-red/10">
                <form id="trackingForm" onsubmit="event.preventDefault(); trackSample();" class="flex flex-col sm:flex-row gap-2">
                    <input type="text" id="sampleTrackingInput" placeholder="Contoh: TU-LAB-2026-0042" value="TU-LAB-2026-0042"
                        class="flex-1 px-3.5 py-2.5 text-sm bg-gray-50 border border-gray-300 rounded-md text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-racing-red focus:border-racing-red transition-all">
                    <button type="submit"
                        class="px-5 py-2.5 text-sm font-semibold text-white bg-racing-red hover:bg-tiger-flame active:scale-95 rounded-md transition-all duration-200 shadow-sm hover:shadow whitespace-nowrap flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Cari Status
                    </button>
                </form>

                <!-- Interactive Result Display -->
                <div id="trackingResult" class="mt-3.5 pt-3.5 border-t border-gray-100 text-xs transition-all duration-300">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <span class="text-gray-500">ID Sampel:</span>
                            <span class="font-semibold text-gray-900 ml-1 font-mono">TU-LAB-2026-0042</span>
                            <span class="text-gray-400 mx-1">&bull;</span>
                            <span class="text-gray-600">Spektrofotometri UV-Vis</span>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold bg-jasmine/30 text-amber-900 border border-jasmine">
                            Sedang Diuji di Lab
                        </span>
                    </div>
                    <div class="mt-2 flex items-center gap-1.5 text-xs text-gray-500">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Laboran PIC: <strong>Ahmad Fauzi, S.Si.</strong> (Lab Terpadu Kampus) &bull; Estimasi Selesai: 28 Okt 2026
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Metrics & Service Highlights with Smooth Hover Lift -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6 sm:-mt-10 relative z-20">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="group bg-white border border-gray-200 rounded-lg p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-racing-red/30 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-0 h-1 bg-gradient-to-r from-racing-red to-tiger-flame transition-all duration-300 group-hover:w-full"></div>
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Laboratorium</span>
                <p class="mt-1 text-2xl sm:text-3xl font-extrabold text-racing-red transition-transform duration-300 group-hover:scale-105 origin-left">14+</p>
                <p class="text-xs text-gray-600 mt-1">Fakultas & Pusat Riset Kampus</p>
            </div>

            <div class="group bg-white border border-gray-200 rounded-lg p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-tiger-flame/30 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-0 h-1 bg-gradient-to-r from-tiger-flame to-racing-red transition-all duration-300 group-hover:w-full"></div>
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Alat & Instrumen</span>
                <p class="mt-1 text-2xl sm:text-3xl font-extrabold text-tiger-flame transition-transform duration-300 group-hover:scale-105 origin-left">50+</p>
                <p class="text-xs text-gray-600 mt-1">Terkalibrasi dan bersertifikasi</p>
            </div>

            <div class="group bg-white border border-gray-200 rounded-lg p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-gray-400 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-0 h-1 bg-gray-900 transition-all duration-300 group-hover:w-full"></div>
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Akses Civitas</span>
                <p class="mt-1 text-2xl sm:text-3xl font-extrabold text-gray-900 transition-transform duration-300 group-hover:scale-105 origin-left">100%</p>
                <p class="text-xs text-gray-600 mt-1">Fasilitas riset akademik internal</p>
            </div>

            <div class="group bg-white border border-gray-200 rounded-lg p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-tiger-flame/30 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-0 h-1 bg-gradient-to-r from-tiger-flame to-racing-red transition-all duration-300 group-hover:w-full"></div>
                <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Rata-rata Durasi Uji</span>
                <p class="mt-1 text-2xl sm:text-3xl font-extrabold text-tiger-flame transition-transform duration-300 group-hover:scale-105 origin-left">&lt; 5 Hari</p>
                <p class="text-xs text-gray-600 mt-1">SLA cepat dari penyerahan sampel</p>
            </div>
        </div>
    </div>

    <!-- Alur Standar Pengujian Sampel (Serpentine Ribbon Timeline Process) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
        <div class="text-center max-w-2xl mx-auto mb-10">
            <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-gray-900 mt-1">ALUR STANDAR PENGUJIAN SAMPEL</h2>
        </div>

        <!-- Desktop Infographic: Serpentine Stepped Ribbon Process -->
        <div class="hidden lg:block bg-white border border-gray-200 rounded-xl p-8 shadow-sm overflow-hidden">
            <svg viewBox="0 0 1200 500" class="w-full h-auto" xmlns="http://www.w3.org/2000/svg">
                <!-- ================= TOP CALLOUTS (Step 02 & Step 04) ================= -->
                <!-- Callout 02 (Above Step 02) -->
                <g transform="translate(300, 25)">
                    <text x="0" y="0" font-family="Inter, sans-serif" font-weight="700" font-size="13" fill="#F26A2D">VALIDASI LABORAN</text>
                    <text x="0" y="20" font-family="Inter, sans-serif" font-size="11.5" fill="#4B5563">1. Verifikasi berkas permohonan</text>
                    <text x="0" y="38" font-family="Inter, sans-serif" font-size="11.5" fill="#4B5563">2. Penyerahan spesimen di lab</text>
                    <text x="0" y="56" font-family="Inter, sans-serif" font-size="11.5" fill="#4B5563">3. Pengecekan wadah & kuantitas</text>
                    <text x="0" y="74" font-family="Inter, sans-serif" font-size="11.5" fill="#4B5563">4. Penetapan antrean instrumen</text>
                </g>

                <!-- Callout 04 (Above Step 04) -->
                <g transform="translate(745, 25)">
                    <text x="0" y="0" font-family="Inter, sans-serif" font-weight="700" font-size="13" fill="#3F6212">EKSEKUSI PENGUJIAN</text>
                    <text x="0" y="20" font-family="Inter, sans-serif" font-size="11.5" fill="#4B5563">1. Pengujian instrumen terakreditasi</text>
                    <text x="0" y="38" font-family="Inter, sans-serif" font-size="11.5" fill="#4B5563">2. Akuisisi spektrum & sinyal</text>
                    <text x="0" y="56" font-family="Inter, sans-serif" font-size="11.5" fill="#4B5563">3. Replikasi uji untuk akurasi data</text>
                    <text x="0" y="74" font-family="Inter, sans-serif" font-size="11.5" fill="#4B5563">4. Input rekapitulasi data hasil uji</text>
                </g>

                <!-- ================= PIN LINES & SQUARE MARKERS ================= -->
                <!-- START Pin Marker (Far Left) -->
                <line x1="94" y1="230" x2="94" y2="152" stroke="#ED1E28" stroke-width="2" />
                <rect x="88" y="146" width="12" height="12" rx="2" fill="#ED1E28" />
                <text x="94" y="136" font-family="Inter, sans-serif" font-weight="800" font-size="11" fill="#ED1E28" text-anchor="middle" letter-spacing="0.05em">START</text>

                <!-- Pin 01 (Down to Callout 01) -->
                <line x1="181.5" y1="328" x2="181.5" y2="385" stroke="#ED1E28" stroke-width="2" />
                <rect x="175.5" y="380" width="12" height="12" rx="1" fill="#ED1E28" />

                <!-- Pin 02 (Up to Callout 02) -->
                <line x1="384" y1="190" x2="384" y2="135" stroke="#F26A2D" stroke-width="2" />
                <rect x="378" y="129" width="12" height="12" rx="1" fill="#F26A2D" />

                <!-- Pin 03 (Down to Callout 03 - Jasmine) -->
                <line x1="609" y1="328" x2="609" y2="385" stroke="#B45309" stroke-width="2" />
                <rect x="603" y="380" width="12" height="12" rx="1" fill="#F7D488" stroke="#B45309" stroke-width="1.5" />

                <!-- Pin 04 (Up to Callout 04 - Cream) -->
                <line x1="829" y1="190" x2="829" y2="135" stroke="#4D7C0F" stroke-width="2" />
                <rect x="823" y="129" width="12" height="12" rx="1" fill="#EAEFB1" stroke="#4D7C0F" stroke-width="1.5" />

                <!-- Pin 05 (Down to Callout 05 - Frosted Mint) -->
                <line x1="1031.5" y1="328" x2="1031.5" y2="385" stroke="#047857" stroke-width="2" />
                <rect x="1025.5" y="380" width="12" height="12" rx="1" fill="#E9F7CA" stroke="#047857" stroke-width="1.5" />

                <!-- ================= CONTINUOUS SERPENTINE RIBBON PATHS & CHEVRONS ================= -->
                <!-- Step 01 Ribbon (Racing Red: Down left -> Right bottom -> Up right -> Forward chevron at top) -->
                <path d="M 80 230 L 108 230 L 108 300 L 255 300 L 255 190 L 300 190 L 314 204 L 300 218 L 283 218 L 283 328 L 80 328 Z" fill="#ED1E28" />

                <!-- Step 02 Ribbon (Tiger Flame: Chevron indent at top -> Right top -> Down right -> Forward chevron at bottom) - Siku fixed with (485, 328) -->
                <path d="M 300 190 L 513 190 L 513 300 L 530 300 L 544 314 L 530 328 L 485 328 L 485 218 L 300 218 L 314 204 Z" fill="#F26A2D" />

                <!-- Step 03 Ribbon (Jasmine #F7D488: Chevron indent at bottom -> Right bottom -> Up right -> Forward chevron at top) -->
                <path d="M 530 300 L 705 300 L 705 190 L 750 190 L 764 204 L 750 218 L 733 218 L 733 328 L 530 328 L 544 314 Z" fill="#F7D488" stroke="#E5BA6A" stroke-width="1.5" />

                <!-- Step 04 Ribbon (Cream #EAEFB1: Chevron indent at top -> Right top -> Down right -> Forward chevron at bottom) - Siku fixed with (925, 328) -->
                <path d="M 750 190 L 953 190 L 953 300 L 970 300 L 984 314 L 970 328 L 925 328 L 925 218 L 750 218 L 764 204 Z" fill="#EAEFB1" stroke="#CAD67E" stroke-width="1.5" />

                <!-- Step 05 Ribbon (Frosted Mint #E9F7CA: Chevron indent at bottom -> Right bottom -> Terminating arrow point) -->
                <path d="M 970 300 L 1110 300 L 1110 292 L 1142 314 L 1110 336 L 1110 328 L 970 328 L 984 314 Z" fill="#E9F7CA" stroke="#B5D98B" stroke-width="1.5" />

                <!-- ================= NODE CONTENT: NUMBER, TITLE, ICON ================= -->
                <!-- Node 01 (Inside Step 01 Alcove) -->
                <g>
                    <text x="181.5" y="228" font-family="Inter, sans-serif" font-weight="800" font-size="28" fill="#111827" text-anchor="middle">01</text>
                    <text x="181.5" y="248" font-family="Inter, sans-serif" font-weight="700" font-size="12" fill="#ED1E28" text-anchor="middle" letter-spacing="0.06em">PENGAJUAN</text>
                    <!-- Magnifying Glass Search Icon -->
                    <circle cx="178" cy="272" r="6" stroke="#ED1E28" stroke-width="2" fill="none" />
                    <line x1="182.5" y1="276.5" x2="187" y2="281" stroke="#ED1E28" stroke-width="2" stroke-linecap="round" />
                </g>

                <!-- Node 02 (Inside Step 02 Alcove) -->
                <g>
                    <text x="384" y="254" font-family="Inter, sans-serif" font-weight="800" font-size="28" fill="#111827" text-anchor="middle">02</text>
                    <text x="384" y="274" font-family="Inter, sans-serif" font-weight="700" font-size="12" fill="#F26A2D" text-anchor="middle" letter-spacing="0.06em">VERIFIKASI</text>
                    <!-- User Silhouette Icon -->
                    <circle cx="384" cy="292" r="4.5" fill="#F26A2D" />
                    <path d="M 376 305 C 376 299, 392 299, 392 305" stroke="#F26A2D" stroke-width="2" fill="none" stroke-linecap="round" />
                </g>

                <!-- Node 03 (Inside Step 03 Alcove - Jasmine) -->
                <g>
                    <text x="609" y="228" font-family="Inter, sans-serif" font-weight="800" font-size="28" fill="#111827" text-anchor="middle">03</text>
                    <text x="609" y="248" font-family="Inter, sans-serif" font-weight="700" font-size="12" fill="#B45309" text-anchor="middle" letter-spacing="0.06em">PREPARASI</text>
                    <!-- Lightbulb / Creation Icon -->
                    <path d="M 605 272 A 4.5 4.5 0 0 1 613 272 C 613 274.5, 611 276, 611 278 L 607 278 C 607 276, 605 274.5, 605 272 Z" stroke="#B45309" stroke-width="1.8" fill="none" />
                    <line x1="607" y1="280" x2="611" y2="280" stroke="#B45309" stroke-width="1.8" />
                    <line x1="609" y1="265" x2="609" y2="263" stroke="#B45309" stroke-width="1.5" />
                    <line x1="602" y1="268" x2="600" y2="266" stroke="#B45309" stroke-width="1.5" />
                    <line x1="616" y1="268" x2="618" y2="266" stroke="#B45309" stroke-width="1.5" />
                </g>

                <!-- Node 04 (Inside Step 04 Alcove - Cream) -->
                <g>
                    <text x="829" y="254" font-family="Inter, sans-serif" font-weight="800" font-size="28" fill="#111827" text-anchor="middle">04</text>
                    <text x="829" y="274" font-family="Inter, sans-serif" font-weight="700" font-size="12" fill="#3F6212" text-anchor="middle" letter-spacing="0.06em">PENGUJIAN</text>
                    <!-- Checklist / Test Sheet Icon -->
                    <rect x="822" y="288" width="14" height="17" rx="2" stroke="#3F6212" stroke-width="1.8" fill="none" />
                    <path d="M 825 294 L 827 296 L 831 292" stroke="#3F6212" stroke-width="1.6" fill="none" stroke-linecap="round" stroke-linejoin="round" />
                    <line x1="825" y1="300" x2="833" y2="300" stroke="#3F6212" stroke-width="1.6" stroke-linecap="round" />
                </g>

                <!-- Node 05 (Inside Step 05 Alcove - Frosted Mint) -->
                <g>
                    <text x="1031.5" y="228" font-family="Inter, sans-serif" font-weight="800" font-size="28" fill="#111827" text-anchor="middle">05</text>
                    <text x="1031.5" y="248" font-family="Inter, sans-serif" font-weight="700" font-size="12" fill="#065F46" text-anchor="middle" letter-spacing="0.06em">SERTIFIKASI</text>
                    <!-- Bar Chart / Results Progress Icon -->
                    <rect x="1022" y="274" width="4" height="7" fill="#047857" rx="0.5" />
                    <rect x="1028" y="270" width="4" height="11" fill="#047857" rx="0.5" />
                    <rect x="1034" y="266" width="4" height="15" fill="#047857" rx="0.5" />
                    <rect x="1040" y="262" width="4" height="19" fill="#047857" rx="0.5" />
                </g>

                <!-- ================= BOTTOM CALLOUTS (Step 01, Step 03, Step 05) ================= -->
                <!-- Callout 01 (Below Step 01) -->
                <g transform="translate(95, 412)">
                    <text x="0" y="0" font-family="Inter, sans-serif" font-weight="700" font-size="13" fill="#ED1E28">REGISTRASI ONLINE</text>
                    <text x="0" y="20" font-family="Inter, sans-serif" font-size="11.5" fill="#4B5563">1. Pilih instrumen & parameter uji</text>
                    <text x="0" y="38" font-family="Inter, sans-serif" font-size="11.5" fill="#4B5563">2. Unggah berkas (Surat/MSDS)</text>
                    <text x="0" y="56" font-family="Inter, sans-serif" font-size="11.5" fill="#4B5563">3. Tentukan jumlah & sifat sampel</text>
                    <text x="0" y="74" font-family="Inter, sans-serif" font-size="11.5" fill="#4B5563">4. Dapatkan No. Resi & E-Ticket</text>
                </g>

                <!-- Callout 03 (Below Step 03 - Jasmine) -->
                <g transform="translate(520, 412)">
                    <text x="0" y="0" font-family="Inter, sans-serif" font-weight="700" font-size="13" fill="#B45309">PERSIAPAN & KALIBRASI</text>
                    <text x="0" y="20" font-family="Inter, sans-serif" font-size="11.5" fill="#4B5563">1. Preparasi kimiawi & fisik sampel</text>
                    <text x="0" y="38" font-family="Inter, sans-serif" font-size="11.5" fill="#4B5563">2. Kalibrasi instrumen sesuai SOP</text>
                    <text x="0" y="56" font-family="Inter, sans-serif" font-size="11.5" fill="#4B5563">3. Blank testing & setup baseline</text>
                    <text x="0" y="74" font-family="Inter, sans-serif" font-size="11.5" fill="#4B5563">4. Pencatatan log lingkungan lab</text>
                </g>

                <!-- Callout 05 (Below Step 05 - Frosted Mint) -->
                <g transform="translate(945, 412)">
                    <text x="0" y="0" font-family="Inter, sans-serif" font-weight="700" font-size="13" fill="#065F46">PENERBITAN LHU RESMI</text>
                    <text x="0" y="20" font-family="Inter, sans-serif" font-size="11.5" fill="#4B5563">1. Validasi oleh Kepala Lab</text>
                    <text x="0" y="38" font-family="Inter, sans-serif" font-size="11.5" fill="#4B5563">2. Pengesahan digital & QR Barcode</text>
                    <text x="0" y="56" font-family="Inter, sans-serif" font-size="11.5" fill="#4B5563">3. Laporan Hasil Uji (LHU) terbit</text>
                    <text x="0" y="74" font-family="Inter, sans-serif" font-size="11.5" fill="#4B5563">4. Unduh PDF & ambil spesimen</text>
                </g>
            </svg>
        </div>

        <!-- Mobile & Tablet Layout: Responsive Stepper with Matching Colors & Flow -->
        <div class="lg:hidden space-y-4">
            <!-- Step 1 Mobile -->
            <div class="group bg-white border border-gray-200 border-l-4 border-l-racing-red rounded-lg p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:bg-cream/10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded bg-racing-red text-white flex items-center justify-center font-bold text-xs shadow-sm">01</span>
                        <span class="font-bold text-sm text-racing-red">PENGAJUAN &bull; REGISTRASI ONLINE</span>
                    </div>
                    <span class="text-xs px-2.5 py-0.5 rounded font-semibold bg-red-50 text-racing-red border border-red-200">Tahap 1</span>
                </div>
                <ul class="mt-3 text-xs text-gray-600 space-y-1.5 pl-1">
                    <li>1. Pilih instrumen & parameter pengujian yang dibutuhkan</li>
                    <li>2. Unggah dokumen pendukung (Surat Pengantar / MSDS)</li>
                    <li>3. Tentukan jumlah & sifat spesimen sampel</li>
                    <li>4. Dapatkan Nomor Resi & E-Ticket Pengujian resmi</li>
                </ul>
            </div>

            <!-- Step 2 Mobile -->
            <div class="group bg-white border border-gray-200 border-l-4 border-l-tiger-flame rounded-lg p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:bg-cream/10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded bg-tiger-flame text-white flex items-center justify-center font-bold text-xs shadow-sm">02</span>
                        <span class="font-bold text-sm text-tiger-flame">VERIFIKASI &bull; VALIDASI LABORAN</span>
                    </div>
                    <span class="text-xs px-2.5 py-0.5 rounded font-semibold bg-orange-50 text-tiger-flame border border-orange-200">Tahap 2</span>
                </div>
                <ul class="mt-3 text-xs text-gray-600 space-y-1.5 pl-1">
                    <li>1. Pemeriksaan kelayakan berkas online oleh laboran</li>
                    <li>2. Penyerahan fisik spesimen di laboratorium terkait</li>
                    <li>3. Validasi kondisi spesimen & kelayakan wadah uji</li>
                    <li>4. Penetapan jadwal antrean dan kesiapan instrumen</li>
                </ul>
            </div>

            <!-- Step 3 Mobile (Jasmine) -->
            <div class="group bg-white border border-gray-200 border-l-4 border-l-[#F7D488] rounded-lg p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:bg-cream/10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded bg-[#F7D488] text-amber-950 font-bold flex items-center justify-center text-xs shadow-sm border border-amber-300">03</span>
                        <span class="font-bold text-sm text-amber-800">PREPARASI &bull; PERSIAPAN & KALIBRASI</span>
                    </div>
                    <span class="text-xs px-2.5 py-0.5 rounded font-semibold bg-[#F7D488]/30 text-amber-900 border border-[#F7D488]">Tahap 3</span>
                </div>
                <ul class="mt-3 text-xs text-gray-600 space-y-1.5 pl-1">
                    <li>1. Preparasi kimiawi & fisik spesimen oleh tim laboran</li>
                    <li>2. Kalibrasi instrumen uji sesuai SOP baku</li>
                    <li>3. Blank testing & setup baseline pengukuran alat</li>
                    <li>4. Pencatatan log book kondisi lingkungan lab</li>
                </ul>
            </div>

            <!-- Step 4 Mobile (Cream) -->
            <div class="group bg-white border border-gray-200 border-l-4 border-l-[#EAEFB1] rounded-lg p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:bg-cream/10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded bg-[#EAEFB1] text-lime-950 font-bold flex items-center justify-center text-xs shadow-sm border border-lime-300">04</span>
                        <span class="font-bold text-sm text-lime-800">PENGUJIAN &bull; EKSEKUSI INSTRUMEN</span>
                    </div>
                    <span class="text-xs px-2.5 py-0.5 rounded font-semibold bg-[#EAEFB1] text-lime-900 border border-lime-300">Tahap 4</span>
                </div>
                <ul class="mt-3 text-xs text-gray-600 space-y-1.5 pl-1">
                    <li>1. Pengujian dengan alat laboratorium terakreditasi</li>
                    <li>2. Akumulasi data spektrum / pengukuran kuantitatif</li>
                    <li>3. Replikasi pengujian untuk menjamin presisi data</li>
                    <li>4. Input rekapitulasi data hasil uji oleh laboran</li>
                </ul>
            </div>

            <!-- Step 5 Mobile (Frosted Mint) -->
            <div class="group bg-white border border-gray-200 border-l-4 border-l-[#E9F7CA] rounded-lg p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:bg-cream/10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="w-7 h-7 rounded bg-[#E9F7CA] text-emerald-950 font-bold flex items-center justify-center text-xs shadow-sm border border-emerald-300">05</span>
                        <span class="font-bold text-sm text-emerald-800">SERTIFIKASI &bull; PENERBITAN LHU RESMI</span>
                    </div>
                    <span class="text-xs px-2.5 py-0.5 rounded font-semibold bg-[#E9F7CA] text-emerald-900 border border-emerald-300">Tahap 5</span>
                </div>
                <ul class="mt-3 text-xs text-gray-600 space-y-1.5 pl-1">
                    <li>1. Validasi data hasil pengujian oleh Kepala Lab</li>
                    <li>2. Pengesahan dokumen digital dengan QR Barcode resmi</li>
                    <li>3. Laporan Hasil Uji (LHU) terbit di sistem Telyuji</li>
                    <li>4. Unduh sertifikat PDF resmi & pengambilan residu sampel</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Interactive Laboratory Domain Catalog Carousel -->
    <section id="katalog-lab" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between mb-6 gap-4">
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900">Katalog Laboratorium & Instrumen Uji</h2>
                <p class="text-sm text-gray-600 mt-2">Daftar instrumen dan parameter pengujian yang tersedia di seluruh rumpun laboratorium kampus Telkom University.</p>
            </div>

            <!-- Carousel Navigation Controls: Prev, Next -->
            <div class="flex items-center gap-2 self-start md:self-auto">
                <button type="button" onclick="scrollCarousel('left')" aria-label="Geser ke Kiri" title="Geser ke Kiri"
                    class="w-10 h-10 rounded-full border border-gray-300 bg-white text-gray-700 flex items-center justify-center hover:bg-racing-red hover:text-white hover:border-racing-red transition-all duration-200 active:scale-90 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button type="button" onclick="scrollCarousel('right')" aria-label="Geser ke Kanan" title="Geser ke Kanan"
                    class="w-10 h-10 rounded-full border border-gray-300 bg-white text-gray-700 flex items-center justify-center hover:bg-racing-red hover:text-white hover:border-racing-red transition-all duration-200 active:scale-90 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Category Filter Tabs -->
        <div class="flex flex-wrap items-center gap-2 mb-6">
            <button type="button" onclick="switchLabTab('all')" id="tab-all"
                class="lab-tab-btn px-4 py-2 text-xs sm:text-sm font-semibold rounded-full border transition-all duration-200 active:scale-95 bg-racing-red text-white border-racing-red shadow-sm">
                Semua Instrumen
            </button>
            <button type="button" onclick="switchLabTab('kimia')" id="tab-kimia"
                class="lab-tab-btn px-4 py-2 text-xs sm:text-sm font-semibold rounded-full border transition-all duration-200 active:scale-95 bg-white text-gray-700 border-gray-300 hover:border-tiger-flame hover:text-tiger-flame">
                Material & Kimia Terapan
            </button>
            <button type="button" onclick="switchLabTab('telekomunikasi')" id="tab-telekomunikasi"
                class="lab-tab-btn px-4 py-2 text-xs sm:text-sm font-semibold rounded-full border transition-all duration-200 active:scale-95 bg-white text-gray-700 border-gray-300 hover:border-tiger-flame hover:text-tiger-flame">
                Telekomunikasi, RF & IoT
            </button>
            <button type="button" onclick="switchLabTab('bioteknologi')" id="tab-bioteknologi"
                class="lab-tab-btn px-4 py-2 text-xs sm:text-sm font-semibold rounded-full border transition-all duration-200 active:scale-95 bg-white text-gray-700 border-gray-300 hover:border-tiger-flame hover:text-tiger-flame">
                Bioteknologi & Lingkungan
            </button>
            <button type="button" onclick="switchLabTab('komputasi')" id="tab-komputasi"
                class="lab-tab-btn px-4 py-2 text-xs sm:text-sm font-semibold rounded-full border transition-all duration-200 active:scale-95 bg-white text-gray-700 border-gray-300 hover:border-tiger-flame hover:text-tiger-flame">
                Simulasi & Komputasi Riset
            </button>
        </div>

        <!-- Carousel Viewport with Ambient Edge Fade & Infinite Motion -->
        <div class="relative group/carousel overflow-hidden rounded-xl">
            <!-- Left Gradient Fade Mask -->
            <div class="pointer-events-none absolute left-0 top-0 bottom-0 w-8 sm:w-16 bg-gradient-to-r from-gray-50/90 to-transparent z-10 hidden sm:block"></div>
            <!-- Right Gradient Fade Mask -->
            <div class="pointer-events-none absolute right-0 top-0 bottom-0 w-8 sm:w-16 bg-gradient-to-l from-gray-50/90 to-transparent z-10 hidden sm:block"></div>

            <!-- Dynamic Horizontal Carousel Scroll Track -->
            <div id="carousel-track"
                class="flex gap-6 overflow-x-auto no-scrollbar py-4 px-2 cursor-grab active:cursor-grabbing select-none">
                <div class="group/card w-[310px] sm:w-[350px] flex-shrink-0 bg-white border border-gray-200 rounded-xl p-5 sm:p-6 flex flex-col justify-between transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl hover:border-racing-red/40 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-0 h-1 bg-gradient-to-r from-racing-red to-tiger-flame transition-all duration-300 group-hover/card:w-full"></div>
                    <div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold bg-cream text-gray-800 border border-jasmine/60">Lab Material Maju</span>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 mt-2.5 group-hover/card:text-racing-red transition-colors line-clamp-1">Spektrofotometer UV-Vis</h3>
                        <p class="text-xs text-gray-600 mt-1.5 leading-relaxed line-clamp-2">Pengukuran absorbansi larutan, penentuan konsentrasi sampel cair, dan analisis spektral panjang gelombang 190–1100 nm.</p>
                        <div class="mt-4 pt-3 border-t border-gray-100 text-xs text-gray-500 space-y-1.5">
                            <div class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-tiger-flame flex-shrink-0"></span><span class="truncate">Kapasitas: Kuarsa / Kaca 10 mm</span></div>
                            <div class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-tiger-flame flex-shrink-0"></span><span>Standar SLA: 2-3 Hari Kerja</span></div>
                        </div>
                    </div>
                    <div class="mt-5 pt-3 border-t border-gray-50 flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded text-xs font-semibold bg-frosted-mint text-emerald-900 border border-emerald-300/70"><span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>Siap Menerima Sampel</span>
                        <a href="{{ route('register') }}" class="text-xs font-bold text-racing-red hover:text-tiger-flame transition-colors flex items-center gap-1 group-hover/card:translate-x-0.5">Ajukan &rarr;</a>
                    </div>
                </div>

                <div class="group/card w-[310px] sm:w-[350px] flex-shrink-0 bg-white border border-gray-200 rounded-xl p-5 sm:p-6 flex flex-col justify-between transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl hover:border-racing-red/40 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-0 h-1 bg-gradient-to-r from-racing-red to-tiger-flame transition-all duration-300 group-hover/card:w-full"></div>
                    <div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold bg-cream text-gray-800 border border-jasmine/60">Lab Kimia Terpadu</span>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 mt-2.5 group-hover/card:text-racing-red transition-colors line-clamp-1">FTIR Spectrometer</h3>
                        <p class="text-xs text-gray-600 mt-1.5 leading-relaxed line-clamp-2">Identifikasi gugus fungsi senyawa organik, polimer, dan analisis struktur molekuler material serbuk maupun film tipis.</p>
                        <div class="mt-4 pt-3 border-t border-gray-100 text-xs text-gray-500 space-y-1.5">
                            <div class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-tiger-flame flex-shrink-0"></span><span class="truncate">Resolusi: hingga 0.5 cm⁻¹</span></div>
                            <div class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-tiger-flame flex-shrink-0"></span><span>Standar SLA: 3-4 Hari Kerja</span></div>
                        </div>
                    </div>
                    <div class="mt-5 pt-3 border-t border-gray-50 flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded text-xs font-semibold bg-frosted-mint text-emerald-900 border border-emerald-300/70"><span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>Siap Menerima Sampel</span>
                        <a href="{{ route('register') }}" class="text-xs font-bold text-racing-red hover:text-tiger-flame transition-colors flex items-center gap-1 group-hover/card:translate-x-0.5">Ajukan &rarr;</a>
                    </div>
                </div>

                <div class="group/card w-[310px] sm:w-[350px] flex-shrink-0 bg-white border border-gray-200 rounded-xl p-5 sm:p-6 flex flex-col justify-between transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl hover:border-racing-red/40 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-0 h-1 bg-gradient-to-r from-racing-red to-tiger-flame transition-all duration-300 group-hover/card:w-full"></div>
                    <div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold bg-cream text-gray-800 border border-jasmine/60">Lab Uji Mekanik</span>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 mt-2.5 group-hover/card:text-racing-red transition-colors line-clamp-1">Universal Testing Machine (UTM)</h3>
                        <p class="text-xs text-gray-600 mt-1.5 leading-relaxed line-clamp-2">Pengujian mekanik material meliputi uji tarik (tensile), uji tekan (compression), dan bending spesimen standar ASTM.</p>
                        <div class="mt-4 pt-3 border-t border-gray-100 text-xs text-gray-500 space-y-1.5">
                            <div class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-tiger-flame flex-shrink-0"></span><span class="truncate">Kapasitas beban: Hingga 50 kN</span></div>
                            <div class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-tiger-flame flex-shrink-0"></span><span>Standar SLA: 3-5 Hari Kerja</span></div>
                        </div>
                    </div>
                    <div class="mt-5 pt-3 border-t border-gray-50 flex items-center justify-between">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded text-xs font-semibold bg-frosted-mint text-emerald-900 border border-emerald-300/70"><span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>Siap Menerima Sampel</span>
                        <a href="{{ route('register') }}" class="text-xs font-bold text-racing-red hover:text-tiger-flame transition-colors flex items-center gap-1 group-hover/card:translate-x-0.5">Ajukan &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactive FAQ Accordion -->
    <section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 border-t border-gray-200 pt-16">
        <div class="text-center mb-10">
            <span class="text-xs font-bold text-racing-red uppercase tracking-wider">Pusat Informasi</span>
            <h2 class="text-2xl font-bold tracking-tight text-gray-900 mt-1">Pertanyaan Umum (FAQ)</h2>
            <p class="text-xs sm:text-sm text-gray-600 mt-1">Panduan praktis prosedur pengujian laboratorium di Telkom University.</p>
        </div>

        <div class="space-y-3">
            <div id="faq-item-1" class="border border-gray-200 rounded-lg overflow-hidden bg-white transition-all duration-300 hover:border-racing-red/30">
                <button type="button" onclick="toggleFaq(1)" class="w-full px-5 py-4 text-left font-semibold text-sm text-gray-900 flex justify-between items-center hover:bg-gray-50/80 transition-colors group">
                    <span class="group-hover:text-racing-red transition-colors">Siapa saja yang dapat mengajukan pengujian sampel di Telyuji?</span>
                    <span id="faq-icon-1" class="w-6 h-6 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center text-xs transition-all duration-300 group-hover:bg-racing-red group-hover:text-white">
                        <svg class="w-3.5 h-3.5 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </span>
                </button>
                <div id="faq-content-1" class="hidden px-5 pb-5 text-xs text-gray-600 leading-relaxed border-t border-gray-100 pt-3.5 bg-cream/10">
                    Seluruh mahasiswa, dosen, dan peneliti aktif di lingkungan Telkom University dapat mendaftar dengan akun kampus untuk keperluan tugas akhir, riset mandiri, maupun penelitian hibah.
                </div>
            </div>

            <div id="faq-item-2" class="border border-gray-200 rounded-lg overflow-hidden bg-white transition-all duration-300 hover:border-racing-red/30">
                <button type="button" onclick="toggleFaq(2)" class="w-full px-5 py-4 text-left font-semibold text-sm text-gray-900 flex justify-between items-center hover:bg-gray-50/80 transition-colors group">
                    <span class="group-hover:text-racing-red transition-colors">Apakah layanan pengujian ini dikenakan biaya?</span>
                    <span id="faq-icon-2" class="w-6 h-6 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center text-xs transition-all duration-300 group-hover:bg-racing-red group-hover:text-white">
                        <svg class="w-3.5 h-3.5 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </span>
                </button>
                <div id="faq-content-2" class="hidden px-5 pb-5 text-xs text-gray-600 leading-relaxed border-t border-gray-100 pt-3.5 bg-cream/10">
                    Sesuai kebijakan kampus, pengujian laboratorium untuk keperluan akademik civitas internal Telkom University difasilitasi tanpa biaya (100% bebas biaya fasilitas lab kampus).
                </div>
            </div>

            <div id="faq-item-3" class="border border-gray-200 rounded-lg overflow-hidden bg-white transition-all duration-300 hover:border-racing-red/30">
                <button type="button" onclick="toggleFaq(3)" class="w-full px-5 py-4 text-left font-semibold text-sm text-gray-900 flex justify-between items-center hover:bg-gray-50/80 transition-colors group">
                    <span class="group-hover:text-racing-red transition-colors">Di mana lokasi penyerahan fisik sampel di kampus?</span>
                    <span id="faq-icon-3" class="w-6 h-6 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center text-xs transition-all duration-300 group-hover:bg-racing-red group-hover:text-white">
                        <svg class="w-3.5 h-3.5 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </span>
                </button>
                <div id="faq-content-3" class="hidden px-5 pb-5 text-xs text-gray-600 leading-relaxed border-t border-gray-100 pt-3.5 bg-cream/10">
                    Setelah pengajuan diverifikasi secara online, sampel diserahkan langsung ke Ruang Laboran pada laboratorium fakultas terkait atau Pusat Lab Terpadu TULT sesuai rincian pada E-Ticket Pengujian Anda.
                </div>
            </div>

            <div id="faq-item-4" class="border border-gray-200 rounded-lg overflow-hidden bg-white transition-all duration-300 hover:border-racing-red/30">
                <button type="button" onclick="toggleFaq(4)" class="w-full px-5 py-4 text-left font-semibold text-sm text-gray-900 flex justify-between items-center hover:bg-gray-50/80 transition-colors group">
                    <span class="group-hover:text-racing-red transition-colors">Berapa lama rata-rata durasi waktu pengujian sampel?</span>
                    <span id="faq-icon-4" class="w-6 h-6 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center text-xs transition-all duration-300 group-hover:bg-racing-red group-hover:text-white">
                        <svg class="w-3.5 h-3.5 transform transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </span>
                </button>
                <div id="faq-content-4" class="hidden px-5 pb-5 text-xs text-gray-600 leading-relaxed border-t border-gray-100 pt-3.5 bg-cream/10">
                    Rata-rata waktu pengujian adalah 2–5 hari kerja terhitung sejak spesimen fisik diterima dan divalidasi oleh laboran, tergantung pada kompleksitas instrumen dan parameter uji yang dipilih.
                </div>
            </div>
        </div>
    </section>

    <!-- Bottom CTA Card with Ambient Gradient Glow -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-br from-gray-950 via-gray-900 to-gray-950 rounded-xl p-8 sm:p-14 text-center text-white relative overflow-hidden shadow-xl border border-gray-800">
            <!-- Decorative Ambient Glowing Orbs -->
            <div class="absolute -top-24 -right-24 w-80 h-80 bg-racing-red/15 rounded-full blur-3xl pointer-events-none animate-pulse"></div>
            <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-tiger-flame/15 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 max-w-2xl mx-auto">
                <h3 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Siap Melakukan Pengujian Sampel Riset Anda?</h3>
                <p class="mt-3 text-sm text-gray-300 leading-relaxed">
                    Daftarkan akun dan ajukan pengujian instrumen laboratorium Anda hari ini melalui sistem Telyuji. Bebas biaya bagi seluruh civitas akademika Telkom University.
                </p>
                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3.5">
                    <a href="{{ route('register') }}" class="group w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-3.5 text-sm font-semibold text-white bg-racing-red hover:bg-tiger-flame rounded-md shadow-lg shadow-racing-red/25 hover:shadow-tiger-flame/35 transition-all duration-300 transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-racing-red">
                        <span>Daftar Akun Pemohon</span>
                        <span class="transition-transform duration-300 group-hover:translate-x-1">&rarr;</span>
                    </a>
                    <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-3.5 text-sm font-semibold text-gray-200 hover:text-white border border-white/20 hover:border-white/40 bg-white/5 hover:bg-white/10 rounded-md backdrop-blur-sm transition-all duration-300 transform hover:-translate-y-0.5">
                        Sudah Memiliki Akun? Masuk
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Interactive Client Logic -->
<script>
    // All 12 laboratory instruments across categories
    const allInstruments = [
        // Material & Kimia
        {
            category: 'kimia',
            title: "Spektrofotometer UV-Vis",
            location: "Lab Material Maju",
            desc: "Pengukuran absorbansi larutan, penentuan konsentrasi sampel cair, dan analisis spektral panjang gelombang 190–1100 nm.",
            specs: "Kapasitas: Kuarsa / Kaca 10 mm",
            sla: "2-3 Hari Kerja"
        },
        {
            category: 'kimia',
            title: "FTIR Spectrometer",
            location: "Lab Kimia Terpadu",
            desc: "Identifikasi gugus fungsi senyawa organik, polimer, dan analisis struktur molekuler material serbuk maupun film tipis.",
            specs: "Resolusi: hingga 0.5 cm⁻¹",
            sla: "3-4 Hari Kerja"
        },
        {
            category: 'kimia',
            title: "Universal Testing Machine (UTM)",
            location: "Lab Uji Mekanik",
            desc: "Pengujian mekanik material meliputi uji tarik (tensile), uji tekan (compression), dan bending spesimen standar ASTM.",
            specs: "Kapasitas beban: Hingga 50 kN",
            sla: "3-5 Hari Kerja"
        },
        // Telekomunikasi & IoT
        {
            category: 'telekomunikasi',
            title: "Vector Network Analyzer (VNA)",
            location: "Lab Gelombang Mikro FTE",
            desc: "Analisis parameter S (S11, S21), impedansi frekuensi tinggi, dan karakterisasi rangkaian gelombang mikro hingga 14 GHz.",
            specs: "Rentang frekuensi: 100 kHz - 14 GHz",
            sla: "2-4 Hari Kerja"
        },
        {
            category: 'telekomunikasi',
            title: "Anechoic Chamber & Antena Testing",
            location: "Lab Antena & Propagasi",
            desc: "Pengukuran pola radiasi antena (radiation pattern), gain antena, dan polarisasi tanpa interferensi sinyal eksternal.",
            specs: "Ruang kedap gelombang RF standar IEEE",
            sla: "3-5 Hari Kerja"
        },
        {
            category: 'telekomunikasi',
            title: "Digital Oscilloscope 1 GHz",
            location: "Lab Embedded & IoT",
            desc: "Verifikasi integritas sinyal digital kecepatan tinggi, analisis protokol komunikasi serial I2C/SPI/UART.",
            specs: "Sampling rate: 5 GSa/s, 4 Channel",
            sla: "1-2 Hari Kerja"
        },
        // Bioteknologi & Lingkungan
        {
            category: 'bioteknologi',
            title: "Automated Water Quality Analyzer",
            location: "Lab Rekayasa Lingkungan",
            desc: "Pengujian parameter COD, BOD, TSS, kekeruhan, dan keasaman sampel perairan lingkungan secara terotomasi.",
            specs: "Sesuai baku mutu air standar SNI",
            sla: "2-4 Hari Kerja"
        },
        {
            category: 'bioteknologi',
            title: "Inkubator Mikrobiologi Presisi",
            location: "Lab Biosains & Terapan",
            desc: "Kultivasi bakteri dan mikroorganisme dengan kontrol suhu stabil 0.1°C untuk uji antibakteri dan bioassay.",
            specs: "Rentang suhu: 5°C - 70°C",
            sla: "3-5 Hari Kerja"
        },
        {
            category: 'bioteknologi',
            title: "Centrifuge Kecepatan Tinggi",
            location: "Lab Sentral Riset",
            desc: "Separasi fraksi seluler, presipitasi protein, dan isolasi DNA/RNA dengan pendinginan suhu terkontrol.",
            specs: "Kecepatan maks: 15.000 RPM",
            sla: "1-2 Hari Kerja"
        },
        // Simulasi & Komputasi Riset
        {
            category: 'komputasi',
            title: "Cluster Server Simulasi Pemodelan",
            location: "Lab High Performance Computing",
            desc: "Komputasi paralel untuk simulasi dinamika fluida (CFD), kalkulasi kimia komputasi DFT, dan pemodelan numerik.",
            specs: "Node komputasi multi-GPU NVIDIA A100",
            sla: "2-5 Hari Kerja"
        },
        {
            category: 'komputasi',
            title: "Testing Rig Embedded AI & Edge",
            location: "Lab AI & Robotika",
            desc: "Uji performa inferensi model kecerdasan buatan, konsumsi daya instrumen, dan stabilitas pada perangkat edge.",
            specs: "Power meter analitik & thermal camera",
            sla: "2-3 Hari Kerja"
        },
        {
            category: 'komputasi',
            title: "Stasiun Kerja Citra Biomedis",
            location: "Lab Rekayasa Biomedika",
            desc: "Pemrosesan citra medis DICOM, segmentasi organ 3D, dan pelatihan deep learning klasifikasi data laboratorium.",
            specs: "Monitor kalibrasi warna medis 4K",
            sla: "1-3 Hari Kerja"
        }
    ];

    let currentCategory = 'all';
    let isCarouselPlaying = true;
    let isUserHovered = false;
    let isDragging = false;
    let startX = 0;
    let startScrollLeft = 0;
    let scrollSpeed = 0.8; // smooth continuous drift
    let animFrameId = null;

    function renderCard(item) {
        return `
            <div class="group/card w-[310px] sm:w-[350px] flex-shrink-0 bg-white border border-gray-200 rounded-xl p-5 sm:p-6 flex flex-col justify-between transition-all duration-300 hover:-translate-y-1.5 hover:shadow-xl hover:border-racing-red/40 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-0 h-1 bg-gradient-to-r from-racing-red to-tiger-flame transition-all duration-300 group-hover/card:w-full"></div>
                <div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-semibold bg-cream text-gray-800 border border-jasmine/60">${item.location}</span>
                    <h3 class="text-base sm:text-lg font-bold text-gray-900 mt-2.5 group-hover/card:text-racing-red transition-colors line-clamp-1">${item.title}</h3>
                    <p class="text-xs text-gray-600 mt-1.5 leading-relaxed line-clamp-2">${item.desc}</p>
                    <div class="mt-4 pt-3 border-t border-gray-100 text-xs text-gray-500 space-y-1.5">
                        <div class="flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-tiger-flame flex-shrink-0"></span>
                            <span class="truncate">${item.specs}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-tiger-flame flex-shrink-0"></span>
                            <span>Standar SLA: ${item.sla}</span>
                        </div>
                    </div>
                </div>
                <div class="mt-5 pt-3 border-t border-gray-50 flex items-center justify-between">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded text-xs font-semibold bg-frosted-mint text-emerald-900 border border-emerald-300/70">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                        Siap Menerima Sampel
                    </span>
                    <a href="{{ route('register') }}" class="text-xs font-bold text-racing-red hover:text-tiger-flame transition-colors flex items-center gap-1 group-hover/card:translate-x-0.5">
                        Ajukan &rarr;
                    </a>
                </div>
            </div>
        `;
    }

    function initCarousel(category = 'all') {
        const track = document.getElementById('carousel-track');
        if (!track) return;

        currentCategory = category;
        const filtered = category === 'all' 
            ? allInstruments 
            : allInstruments.filter(item => item.category === category);

        // Repeat items for seamless infinite wrap
        let repeatCount = 2;
        if (filtered.length <= 3) repeatCount = 4;
        else if (filtered.length <= 6) repeatCount = 3;

        let html = '';
        for (let r = 0; r < repeatCount; r++) {
            html += filtered.map(item => renderCard(item)).join('');
        }
        track.innerHTML = html;

        // Position in the middle so left-to-right drift has space to roll smoothly
        requestAnimationFrame(() => {
            const singleSetWidth = track.scrollWidth / repeatCount;
            track.scrollLeft = singleSetWidth;
        });
    }

    // Continuous flow animation: moves from left to right (decreasing scrollLeft)
    function runCarouselAnimation() {
        const track = document.getElementById('carousel-track');
        if (track && isCarouselPlaying && !isUserHovered && !isDragging) {
            // Decreasing scrollLeft makes items glide towards the right ("kiri ke kanan")
            track.scrollLeft -= scrollSpeed;

            // When reaching the left edge, seamlessly shift to middle
            if (track.scrollLeft <= 2) {
                const singleSetWidth = track.scrollWidth / 2;
                track.scrollLeft += singleSetWidth;
            }
        }
        animFrameId = requestAnimationFrame(runCarouselAnimation);
    }

    function scrollCarousel(dir) {
        const track = document.getElementById('carousel-track');
        if (!track) return;
        const cardWidth = 374; // approx card width + gap
        if (dir === 'left') {
            track.scrollBy({ left: -cardWidth, behavior: 'smooth' });
            setTimeout(() => {
                if (track.scrollLeft <= 10) {
                    track.scrollLeft += track.scrollWidth / 2;
                }
            }, 350);
        } else {
            track.scrollBy({ left: cardWidth, behavior: 'smooth' });
            setTimeout(() => {
                if (track.scrollLeft >= track.scrollWidth - track.clientWidth - 10) {
                    track.scrollLeft -= track.scrollWidth / 2;
                }
            }, 350);
        }
    }

    function switchLabTab(key) {
        document.querySelectorAll('.lab-tab-btn').forEach(btn => {
            btn.className = 'lab-tab-btn px-4 py-2 text-xs sm:text-sm font-semibold rounded-full border transition-all duration-200 active:scale-95 bg-white text-gray-700 border-gray-300 hover:border-tiger-flame hover:text-tiger-flame';
        });

        const activeBtn = document.getElementById('tab-' + key);
        if (activeBtn) {
            activeBtn.className = 'lab-tab-btn px-4 py-2 text-xs sm:text-sm font-semibold rounded-full border transition-all duration-200 active:scale-95 bg-racing-red text-white border-racing-red shadow-sm';
        }

        initCarousel(key);
    }

    function setupCarousel() {
        initCarousel('all');
        runCarouselAnimation();

        const track = document.getElementById('carousel-track');
        if (track) {
            // Hover pause
            track.addEventListener('mouseenter', () => { isUserHovered = true; });
            track.addEventListener('mouseleave', () => { 
                isUserHovered = false; 
                isDragging = false; 
            });

            // Mouse drag
            track.addEventListener('mousedown', (e) => {
                isDragging = true;
                startX = e.pageX - track.offsetLeft;
                startScrollLeft = track.scrollLeft;
            });

            track.addEventListener('mousemove', (e) => {
                if (!isDragging) return;
                e.preventDefault();
                const x = e.pageX - track.offsetLeft;
                const walk = (x - startX) * 1.5;
                track.scrollLeft = startScrollLeft - walk;
            });

            track.addEventListener('mouseup', () => { isDragging = false; });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupCarousel);
    } else {
        setupCarousel();
    }

    // Quick Sample Tracking Simulator with animated loading state
    function trackSample() {
        const input = document.getElementById('sampleTrackingInput').value.trim();
        const result = document.getElementById('trackingResult');

        if (!input) {
            alert('Silakan masukkan nomor resi / ID sampel uji.');
            return;
        }

        // Show animated loader
        result.innerHTML = `
            <div class="flex items-center gap-2.5 py-2.5 text-xs text-gray-600 animate-pulse">
                <svg class="animate-spin h-4 w-4 text-racing-red" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
                <span>Menghubungi basis data laboratorium Telkom University...</span>
            </div>
        `;

        setTimeout(() => {
            result.innerHTML = `
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <span class="text-gray-500">ID Sampel:</span>
                        <span class="font-semibold text-gray-900 ml-1 font-mono">${input}</span>
                        <span class="text-gray-400 mx-1">&bull;</span>
                        <span class="text-gray-600">Terverifikasi di Sistem Telyuji</span>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded text-xs font-semibold bg-jasmine/30 text-amber-900 border border-jasmine">
                        Dalam Antrean Pengujian
                    </span>
                </div>
                <div class="mt-2 flex items-center gap-1.5 text-xs text-gray-500">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Lokasi: Laboratorium Riset Telkom University &bull; Sampel fisik telah diterima laboran.
                </div>
            `;
        }, 350);
    }

    // FAQ Accordion toggle with smooth icon rotation
    function toggleFaq(num) {
        const item = document.getElementById('faq-item-' + num);
        const content = document.getElementById('faq-content-' + num);
        const icon = document.getElementById('faq-icon-' + num);
        const svg = icon ? icon.querySelector('svg') : null;

        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            if (item) {
                item.classList.add('border-racing-red/40', 'shadow-sm');
            }
            if (icon) {
                icon.classList.add('bg-racing-red', 'text-white');
                icon.classList.remove('bg-gray-100', 'text-gray-500');
            }
            if (svg) {
                svg.classList.add('rotate-45');
            }
        } else {
            content.classList.add('hidden');
            if (item) {
                item.classList.remove('border-racing-red/40', 'shadow-sm');
            }
            if (icon) {
                icon.classList.remove('bg-racing-red', 'text-white');
                icon.classList.add('bg-gray-100', 'text-gray-500');
            }
            if (svg) {
                svg.classList.remove('rotate-45');
            }
        }
    }
</script>
@endsection
