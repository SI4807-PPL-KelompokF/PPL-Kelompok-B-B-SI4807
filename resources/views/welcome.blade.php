@extends('layouts.app')

@section('content')
<!-- ============================ HERO ============================ -->
<section class="relative overflow-hidden">
    <div class="tu-grid-bg absolute inset-0" aria-hidden="true"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-16 sm:pt-16 lg:pt-20 lg:pb-28">
        <div class="grid lg:grid-cols-12 gap-14 lg:gap-10 items-center">
            <!-- Copy -->
            <div class="lg:col-span-7">
                <h1 class="font-extrabold tracking-tight text-gray-950 leading-[1.04] text-4xl sm:text-6xl lg:text-[3.5rem] xl:text-[4.25rem]">
                    <span class="tu-line [--d:100ms]"><span>Layanan Pengujian</span></span>
                    <span class="tu-line [--d:220ms]"><span>Laboratorium Riset</span></span>
                    <span class="tu-line [--d:340ms]"><span>&amp; Akademik <span class="tu-mark-underline">Terpadu</span></span></span>
                </h1>

                <p class="mt-7 max-w-xl text-base sm:text-lg leading-relaxed text-gray-600 tu-reveal [--d:450ms]">
                    Fasilitas pengujian instrumen modern terakreditasi lintas fakultas dan pusat riset Telkom University. Pengajuan digital, verifikasi transparan, dan penerbitan LHU resmi.
                </p>

                <div class="mt-9 flex flex-col sm:flex-row sm:items-center gap-3 tu-reveal [--d:560ms]">
                    @auth
                        <a href="{{ route('home') }}" class="tu-btn">
                            <span>Buka Dashboard Pengujian</span>
                            <span class="arr" aria-hidden="true">&rarr;</span>
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="tu-btn">
                            <span>Mulai Pengajuan Sampel</span>
                            <span class="arr" aria-hidden="true">&rarr;</span>
                        </a>
                        <a href="#katalog-lab" class="tu-btn tu-btn--ghost">
                            <span>Katalog Layanan Lab</span>
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Instrument & Facility Showcase Frame -->
            <div class="lg:col-span-5 relative">
                <div id="scanFrame" class="tu-frame aspect-[4/5] sm:aspect-[5/6] lg:aspect-[4/5] select-none">
                    @php
                        $heroSlides = [
                            ['src' => asset('images/tult.jpg'), 'alt' => 'Gedung Laboratorium Terpadu TULT Telkom University'],
                            ['src' => asset('images/buildings/tult-tower.jpg'), 'alt' => 'Fasad Menara TULT Telkom University'],
                            ['src' => asset('images/buildings/btp.jpg'), 'alt' => 'Kawasan Bandung Techno Park Telkom University'],
                            ['src' => asset('images/buildings/gku.jpg'), 'alt' => 'Gedung Kuliah Umum Telkom University'],
                            ['src' => asset('images/buildings/gedung-bangkit.jpg'), 'alt' => 'Gedung Bangkit Rektorat Telkom University'],
                            ['src' => asset('images/buildings/lab-kedokteran.jpg'), 'alt' => 'Laboratorium Biosains dan Medis Telkom University'],
                            ['src' => asset('images/buildings/lab-fabrikasi.jpg'), 'alt' => 'Laboratorium Fabrikasi dan Prototipe Telkom University'],
                            ['src' => asset('images/buildings/lab-praktikum.jpg'), 'alt' => 'Laboratorium Elektronika dan Instrumen Telkom University'],
                        ];
                    @endphp

                    <div id="heroSlides" class="absolute inset-0">
                        @foreach ($heroSlides as $idx => $slide)
                            <div class="tu-slide {{ $idx === 0 ? 'is-active' : '' }}" data-slide="{{ $idx }}" aria-hidden="{{ $idx === 0 ? 'false' : 'true' }}">
                                <img src="{{ $slide['src'] }}" alt="{{ $slide['alt'] }}" @if($idx > 1) loading="lazy" @endif>
                            </div>
                        @endforeach
                    </div>

                    <div class="tu-frame-scrim pointer-events-none absolute inset-0 z-[3]"></div>
                    <span id="heroScanBeam" class="tu-scan-beam" aria-hidden="true"></span>

                    <span class="tu-bracket tu-bracket--tl"></span>
                    <span class="tu-bracket tu-bracket--tr"></span>
                    <span class="tu-bracket tu-bracket--bl"></span>
                    <span class="tu-bracket tu-bracket--br"></span>
                </div>

                <!-- Sample tracking -->
                <div class="mt-4 lg:mt-0 lg:absolute lg:-bottom-12 lg:-left-14 lg:right-6 bg-white border border-gray-200 p-4 sm:p-5 shadow-tu-float tu-reveal [--d:650ms]">
                    <form id="trackingForm" onsubmit="event.preventDefault(); trackSample();" class="flex gap-2" novalidate>
                        <label for="sampleTrackingInput" class="sr-only">Lacak status sampel (resi / ID)</label>
                        <input type="text" id="sampleTrackingInput" placeholder="TU-LAB-2026-0042" value="TU-LAB-2026-0042" autocomplete="off" spellcheck="false"
                            class="tu-input tu-mono min-w-0 flex-1 !py-2.5 !text-[13px]">
                        <button type="submit" class="tu-btn !px-4 !py-2.5" aria-label="Cari status sampel">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <span class="hidden sm:inline">Cari</span>
                        </button>
                    </form>

                    <div id="trackingResult" class="mt-4 pt-4 border-t border-gray-100" aria-live="polite"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================ METRICS ============================ -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" aria-label="Ringkasan layanan">
    <div class="border-y border-gray-200 grid grid-cols-2 lg:grid-cols-4">
        <div class="tu-metric tu-reveal px-5 sm:px-8 py-9 border-r border-b lg:border-b-0 border-gray-200 [--c:#ED1E28]">
            <span class="tu-tick"></span>
            <p class="tu-eyebrow mt-6 text-gray-500">Laboratorium</p>
            <p class="mt-2 text-5xl sm:text-6xl font-extrabold tracking-tight text-gray-950 tabular-nums whitespace-nowrap"><span data-count="14" data-suffix="+">14+</span></p>
            <p class="mt-2 text-xs text-gray-600">Fakultas &amp; Pusat Riset Kampus</p>
        </div>
        <div class="tu-metric tu-reveal px-5 sm:px-8 py-9 border-b lg:border-b-0 lg:border-r border-gray-200 [--c:#F26A2D] [--d:90ms]">
            <span class="tu-tick"></span>
            <p class="tu-eyebrow mt-6 text-gray-500">Alat &amp; Instrumen</p>
            <p class="mt-2 text-5xl sm:text-6xl font-extrabold tracking-tight text-gray-950 tabular-nums whitespace-nowrap"><span data-count="50" data-suffix="+">50+</span></p>
            <p class="mt-2 text-xs text-gray-600">Terkalibrasi dan bersertifikasi</p>
        </div>
        <div class="tu-metric tu-reveal px-5 sm:px-8 py-9 border-r border-gray-200 [--c:#111827] [--d:180ms]">
            <span class="tu-tick"></span>
            <p class="tu-eyebrow mt-6 text-gray-500">Akses Civitas</p>
            <p class="mt-2 text-5xl sm:text-6xl font-extrabold tracking-tight text-gray-950 tabular-nums whitespace-nowrap"><span data-count="100" data-suffix="%">100%</span></p>
            <p class="mt-2 text-xs text-gray-600">Fasilitas riset akademik internal</p>
        </div>
        <div class="tu-metric tu-reveal px-5 sm:px-8 py-9 [--c:#F7D488] [--d:270ms]">
            <span class="tu-tick"></span>
            <p class="tu-eyebrow mt-6 text-gray-500">Rata-rata Durasi Uji</p>
            <p class="mt-2 text-5xl sm:text-6xl font-extrabold tracking-tight text-gray-950 tabular-nums whitespace-nowrap"><span data-count="5" data-prefix="&lt; ">&lt; 5</span><span class="ml-2 text-base font-semibold tracking-normal text-gray-500">Hari</span></p>
            <p class="mt-2 text-xs text-gray-600">SLA cepat dari penyerahan sampel</p>
        </div>
    </div>
    <div class="tu-ruler tu-reveal mt-3 opacity-70" aria-hidden="true"></div>
</section>

<!-- ============================ ALUR ============================ -->
<section id="alur" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-28">
    <div class="grid lg:grid-cols-12 gap-6 items-end">
        <div class="lg:col-span-7 tu-reveal">
            <h2 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-gray-950">Alur Standar Pengujian Sampel</h2>
        </div>
        <p class="lg:col-span-5 text-sm sm:text-base leading-relaxed text-gray-600 tu-reveal [--d:100ms]">
            Lima tahap dari registrasi hingga LHU resmi terbit. Pilih salah satu tahap untuk melihat apa saja yang terjadi di dalamnya.
        </p>
    </div>

    <div id="flow" class="tu-flow mt-12 tu-reveal">
        <div id="flowTabs" class="grid grid-cols-5 gap-2 sm:gap-3" role="tablist" aria-label="Tahapan pengujian sampel"></div>
        <div id="flowPanel" class="relative mt-5 bg-white border border-gray-200 overflow-hidden lg:min-h-[23rem]" role="tabpanel" aria-live="polite"></div>
    </div>
</section>

<!-- ============================ KATALOG ============================ -->
<section id="katalog-lab" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-28">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
        <div class="tu-reveal max-w-2xl">
            <h2 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-gray-950">Katalog Laboratorium &amp; Instrumen Uji</h2>
            <p class="mt-4 text-sm sm:text-base leading-relaxed text-gray-600">Daftar instrumen dan parameter pengujian yang tersedia di seluruh rumpun laboratorium kampus Telkom University.</p>
        </div>

        <!-- Carousel controls -->
        <div class="flex items-center gap-2 self-start md:self-auto tu-reveal [--d:100ms]">
            <button type="button" onclick="scrollCarousel('left')" aria-label="Geser ke kiri" title="Geser ke kiri" class="tu-icon-btn">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </button>
            <button type="button" onclick="scrollCarousel('right')" aria-label="Geser ke kanan" title="Geser ke kanan" class="tu-icon-btn">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </button>
        </div>
    </div>

    <!-- Category tabs -->
    <div class="mt-10 flex items-end justify-between gap-6 border-b border-gray-200 tu-reveal">
        <div class="flex overflow-x-auto no-scrollbar" role="tablist" aria-label="Kategori instrumen">
            <button type="button" role="tab" aria-selected="true" onclick="switchLabTab('all')" id="tab-all" class="lab-tab-btn tu-tab">Semua Instrumen</button>
            <button type="button" role="tab" aria-selected="false" onclick="switchLabTab('kimia')" id="tab-kimia" class="lab-tab-btn tu-tab">Material &amp; Kimia Terapan</button>
            <button type="button" role="tab" aria-selected="false" onclick="switchLabTab('telekomunikasi')" id="tab-telekomunikasi" class="lab-tab-btn tu-tab">Telekomunikasi, RF &amp; IoT</button>
            <button type="button" role="tab" aria-selected="false" onclick="switchLabTab('bioteknologi')" id="tab-bioteknologi" class="lab-tab-btn tu-tab">Bioteknologi &amp; Lingkungan</button>
            <button type="button" role="tab" aria-selected="false" onclick="switchLabTab('komputasi')" id="tab-komputasi" class="lab-tab-btn tu-tab">Simulasi &amp; Komputasi Riset</button>
        </div>
        <p id="catalogCount" class="tu-mono hidden lg:block pb-3 text-xs text-gray-500 whitespace-nowrap">12 instrumen</p>
    </div>

    <!-- Carousel viewport -->
    <div id="carouselWrap" class="tu-fade-edges relative mt-6 -mx-4 sm:-mx-6 lg:mx-0">
        <div id="carousel-track" class="flex gap-5 overflow-x-auto no-scrollbar px-4 sm:px-6 lg:px-0 py-4 cursor-grab active:cursor-grabbing select-none"></div>
    </div>
</section>

<!-- ============================ FAQ ============================ -->
<section id="faq" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-28">
    <div class="grid lg:grid-cols-12 gap-10 lg:gap-16">
        <div class="lg:col-span-4">
            <div class="lg:sticky lg:top-28 tu-reveal">
                <h2 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-gray-950">Pertanyaan Umum (FAQ)</h2>
                <p class="mt-4 text-sm sm:text-base leading-relaxed text-gray-600">Panduan praktis prosedur pengujian laboratorium di Telkom University.</p>
            </div>
        </div>

        <div class="lg:col-span-8 tu-reveal [--d:100ms]">
            <div id="faq-item-1" class="tu-faq-item">
                <h3>
                    <button type="button" onclick="toggleFaq(1)" id="faq-btn-1" aria-expanded="false" aria-controls="faq-content-1" class="group w-full flex items-start gap-5 py-6 text-left">
                        <span class="tu-mono pt-1 text-xs text-gray-400">01</span>
                        <span class="flex-1 text-base sm:text-lg font-semibold leading-snug text-gray-950 transition-colors group-hover:text-racing-red">Siapa saja yang dapat mengajukan pengujian sampel di Telyuji?</span>
                        <span class="tu-plus mt-0.5 text-gray-500" aria-hidden="true"><i></i><i></i></span>
                    </button>
                </h3>
                <div id="faq-content-1" class="tu-acc" role="region" aria-labelledby="faq-btn-1">
                    <div><p class="pl-9 sm:pl-10 pr-10 pb-7 max-w-2xl text-sm sm:text-[15px] leading-relaxed text-gray-600">Seluruh mahasiswa, dosen, dan peneliti aktif di lingkungan Telkom University dapat mendaftar dengan akun kampus untuk keperluan tugas akhir, riset mandiri, maupun penelitian hibah.</p></div>
                </div>
            </div>

            <div id="faq-item-2" class="tu-faq-item">
                <h3>
                    <button type="button" onclick="toggleFaq(2)" id="faq-btn-2" aria-expanded="false" aria-controls="faq-content-2" class="group w-full flex items-start gap-5 py-6 text-left">
                        <span class="tu-mono pt-1 text-xs text-gray-400">02</span>
                        <span class="flex-1 text-base sm:text-lg font-semibold leading-snug text-gray-950 transition-colors group-hover:text-racing-red">Apakah layanan pengujian ini dikenakan biaya?</span>
                        <span class="tu-plus mt-0.5 text-gray-500" aria-hidden="true"><i></i><i></i></span>
                    </button>
                </h3>
                <div id="faq-content-2" class="tu-acc" role="region" aria-labelledby="faq-btn-2">
                    <div><p class="pl-9 sm:pl-10 pr-10 pb-7 max-w-2xl text-sm sm:text-[15px] leading-relaxed text-gray-600">Sesuai kebijakan kampus, pengujian laboratorium untuk keperluan akademik civitas internal Telkom University difasilitasi tanpa biaya (100% bebas biaya fasilitas lab kampus).</p></div>
                </div>
            </div>

            <div id="faq-item-3" class="tu-faq-item">
                <h3>
                    <button type="button" onclick="toggleFaq(3)" id="faq-btn-3" aria-expanded="false" aria-controls="faq-content-3" class="group w-full flex items-start gap-5 py-6 text-left">
                        <span class="tu-mono pt-1 text-xs text-gray-400">03</span>
                        <span class="flex-1 text-base sm:text-lg font-semibold leading-snug text-gray-950 transition-colors group-hover:text-racing-red">Di mana lokasi penyerahan fisik sampel di kampus?</span>
                        <span class="tu-plus mt-0.5 text-gray-500" aria-hidden="true"><i></i><i></i></span>
                    </button>
                </h3>
                <div id="faq-content-3" class="tu-acc" role="region" aria-labelledby="faq-btn-3">
                    <div><p class="pl-9 sm:pl-10 pr-10 pb-7 max-w-2xl text-sm sm:text-[15px] leading-relaxed text-gray-600">Setelah pengajuan diverifikasi secara online, sampel diserahkan langsung ke Ruang Laboran pada laboratorium fakultas terkait atau Pusat Lab Terpadu TULT sesuai rincian pada E-Ticket Pengujian Anda.</p></div>
                </div>
            </div>

            <div id="faq-item-4" class="tu-faq-item">
                <h3>
                    <button type="button" onclick="toggleFaq(4)" id="faq-btn-4" aria-expanded="false" aria-controls="faq-content-4" class="group w-full flex items-start gap-5 py-6 text-left">
                        <span class="tu-mono pt-1 text-xs text-gray-400">04</span>
                        <span class="flex-1 text-base sm:text-lg font-semibold leading-snug text-gray-950 transition-colors group-hover:text-racing-red">Berapa lama rata-rata durasi waktu pengujian sampel?</span>
                        <span class="tu-plus mt-0.5 text-gray-500" aria-hidden="true"><i></i><i></i></span>
                    </button>
                </h3>
                <div id="faq-content-4" class="tu-acc" role="region" aria-labelledby="faq-btn-4">
                    <div><p class="pl-9 sm:pl-10 pr-10 pb-7 max-w-2xl text-sm sm:text-[15px] leading-relaxed text-gray-600">Rata-rata waktu pengujian adalah 2&ndash;5 hari kerja terhitung sejak spesimen fisik diterima dan divalidasi oleh laboran, tergantung pada kompleksitas instrumen dan parameter uji yang dipilih.</p></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================ CTA ============================ -->
<section class="relative mt-28 overflow-hidden bg-gray-950 text-white">
    <div class="tu-grid-dark absolute inset-0 opacity-70" aria-hidden="true"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 sm:pt-32 pb-20">
        <div class="max-w-4xl tu-reveal">
            <h2 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight leading-[1.04]">Siap Melakukan Pengujian Sampel Riset <span class="text-racing-red">Anda?</span></h2>
            <p class="mt-6 max-w-xl text-sm sm:text-base leading-relaxed text-gray-300">
                Daftarkan akun dan ajukan pengujian instrumen laboratorium Anda hari ini melalui sistem Telyuji. Bebas biaya bagi seluruh civitas akademika Telkom University.
            </p>
            <div class="mt-9 flex flex-col sm:flex-row sm:items-center gap-3">
                <a href="{{ route('register') }}" class="tu-btn tu-btn--on-dark">
                    <span>Daftar Akun Pemohon</span>
                    <span class="arr" aria-hidden="true">&rarr;</span>
                </a>
                <a href="{{ route('login') }}" class="tu-btn tu-btn--ghost-dark">
                    <span>Sudah Memiliki Akun? Masuk</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Instrument marquee -->
    <div class="relative border-t border-white/10 py-7 overflow-hidden" aria-hidden="true">
        <div class="tu-marquee">
            <div class="flex shrink-0 items-center">
                <span class="tu-outline-text px-6 sm:px-9 text-5xl sm:text-7xl font-extrabold tracking-tight whitespace-nowrap">UV-Vis</span><i class="inline-block h-2.5 w-2.5 shrink-0 bg-racing-red"></i>
                <span class="tu-outline-text px-6 sm:px-9 text-5xl sm:text-7xl font-extrabold tracking-tight whitespace-nowrap">FTIR</span><i class="inline-block h-2.5 w-2.5 shrink-0 bg-racing-red"></i>
                <span class="tu-outline-text px-6 sm:px-9 text-5xl sm:text-7xl font-extrabold tracking-tight whitespace-nowrap">UTM</span><i class="inline-block h-2.5 w-2.5 shrink-0 bg-racing-red"></i>
                <span class="tu-outline-text px-6 sm:px-9 text-5xl sm:text-7xl font-extrabold tracking-tight whitespace-nowrap">VNA</span><i class="inline-block h-2.5 w-2.5 shrink-0 bg-racing-red"></i>
                <span class="tu-outline-text px-6 sm:px-9 text-5xl sm:text-7xl font-extrabold tracking-tight whitespace-nowrap">Oscilloscope</span><i class="inline-block h-2.5 w-2.5 shrink-0 bg-racing-red"></i>
                <span class="tu-outline-text px-6 sm:px-9 text-5xl sm:text-7xl font-extrabold tracking-tight whitespace-nowrap">Anechoic Chamber</span><i class="inline-block h-2.5 w-2.5 shrink-0 bg-racing-red"></i>
                <span class="tu-outline-text px-6 sm:px-9 text-5xl sm:text-7xl font-extrabold tracking-tight whitespace-nowrap">Water Quality</span><i class="inline-block h-2.5 w-2.5 shrink-0 bg-racing-red"></i>
                <span class="tu-outline-text px-6 sm:px-9 text-5xl sm:text-7xl font-extrabold tracking-tight whitespace-nowrap">Incubator</span><i class="inline-block h-2.5 w-2.5 shrink-0 bg-racing-red"></i>
                <span class="tu-outline-text px-6 sm:px-9 text-5xl sm:text-7xl font-extrabold tracking-tight whitespace-nowrap">Centrifuge</span><i class="inline-block h-2.5 w-2.5 shrink-0 bg-racing-red"></i>
                <span class="tu-outline-text px-6 sm:px-9 text-5xl sm:text-7xl font-extrabold tracking-tight whitespace-nowrap">HPC Cluster</span><i class="inline-block h-2.5 w-2.5 shrink-0 bg-racing-red"></i>
                <span class="tu-outline-text px-6 sm:px-9 text-5xl sm:text-7xl font-extrabold tracking-tight whitespace-nowrap">Edge AI Rig</span><i class="inline-block h-2.5 w-2.5 shrink-0 bg-racing-red"></i>
                <span class="tu-outline-text px-6 sm:px-9 text-5xl sm:text-7xl font-extrabold tracking-tight whitespace-nowrap">Biomedical Imaging</span><i class="inline-block h-2.5 w-2.5 shrink-0 bg-racing-red"></i>
            </div>
            <div class="flex shrink-0 items-center">
                <span class="tu-outline-text px-6 sm:px-9 text-5xl sm:text-7xl font-extrabold tracking-tight whitespace-nowrap">UV-Vis</span><i class="inline-block h-2.5 w-2.5 shrink-0 bg-racing-red"></i>
                <span class="tu-outline-text px-6 sm:px-9 text-5xl sm:text-7xl font-extrabold tracking-tight whitespace-nowrap">FTIR</span><i class="inline-block h-2.5 w-2.5 shrink-0 bg-racing-red"></i>
                <span class="tu-outline-text px-6 sm:px-9 text-5xl sm:text-7xl font-extrabold tracking-tight whitespace-nowrap">UTM</span><i class="inline-block h-2.5 w-2.5 shrink-0 bg-racing-red"></i>
                <span class="tu-outline-text px-6 sm:px-9 text-5xl sm:text-7xl font-extrabold tracking-tight whitespace-nowrap">VNA</span><i class="inline-block h-2.5 w-2.5 shrink-0 bg-racing-red"></i>
                <span class="tu-outline-text px-6 sm:px-9 text-5xl sm:text-7xl font-extrabold tracking-tight whitespace-nowrap">Oscilloscope</span><i class="inline-block h-2.5 w-2.5 shrink-0 bg-racing-red"></i>
                <span class="tu-outline-text px-6 sm:px-9 text-5xl sm:text-7xl font-extrabold tracking-tight whitespace-nowrap">Anechoic Chamber</span><i class="inline-block h-2.5 w-2.5 shrink-0 bg-racing-red"></i>
                <span class="tu-outline-text px-6 sm:px-9 text-5xl sm:text-7xl font-extrabold tracking-tight whitespace-nowrap">Water Quality</span><i class="inline-block h-2.5 w-2.5 shrink-0 bg-racing-red"></i>
                <span class="tu-outline-text px-6 sm:px-9 text-5xl sm:text-7xl font-extrabold tracking-tight whitespace-nowrap">Incubator</span><i class="inline-block h-2.5 w-2.5 shrink-0 bg-racing-red"></i>
                <span class="tu-outline-text px-6 sm:px-9 text-5xl sm:text-7xl font-extrabold tracking-tight whitespace-nowrap">Centrifuge</span><i class="inline-block h-2.5 w-2.5 shrink-0 bg-racing-red"></i>
                <span class="tu-outline-text px-6 sm:px-9 text-5xl sm:text-7xl font-extrabold tracking-tight whitespace-nowrap">HPC Cluster</span><i class="inline-block h-2.5 w-2.5 shrink-0 bg-racing-red"></i>
                <span class="tu-outline-text px-6 sm:px-9 text-5xl sm:text-7xl font-extrabold tracking-tight whitespace-nowrap">Edge AI Rig</span><i class="inline-block h-2.5 w-2.5 shrink-0 bg-racing-red"></i>
                <span class="tu-outline-text px-6 sm:px-9 text-5xl sm:text-7xl font-extrabold tracking-tight whitespace-nowrap">Biomedical Imaging</span><i class="inline-block h-2.5 w-2.5 shrink-0 bg-racing-red"></i>
            </div>
        </div>
    </div>
</section>

<!-- Interactive Client Logic -->
<script>
    // ------------------------------------------------------------------
    // Data
    // ------------------------------------------------------------------
    const allInstruments = [
        // Material & Kimia
        { category: 'kimia', title: "Spektrofotometer UV-Vis", location: "Lab Material Maju",
          desc: "Pengukuran absorbansi larutan, penentuan konsentrasi sampel cair, dan analisis spektral panjang gelombang 190–1100 nm.",
          specs: "Kapasitas: Kuarsa / Kaca 10 mm", sla: "2-3 Hari Kerja" },
        { category: 'kimia', title: "FTIR Spectrometer", location: "Lab Kimia Terpadu",
          desc: "Identifikasi gugus fungsi senyawa organik, polimer, dan analisis struktur molekuler material serbuk maupun film tipis.",
          specs: "Resolusi: hingga 0.5 cm⁻¹", sla: "3-4 Hari Kerja" },
        { category: 'kimia', title: "Universal Testing Machine (UTM)", location: "Lab Uji Mekanik",
          desc: "Pengujian mekanik material meliputi uji tarik (tensile), uji tekan (compression), dan bending spesimen standar ASTM.",
          specs: "Kapasitas beban: Hingga 50 kN", sla: "3-5 Hari Kerja" },
        // Telekomunikasi & IoT
        { category: 'telekomunikasi', title: "Vector Network Analyzer (VNA)", location: "Lab Gelombang Mikro FTE",
          desc: "Analisis parameter S (S11, S21), impedansi frekuensi tinggi, dan karakterisasi rangkaian gelombang mikro hingga 14 GHz.",
          specs: "Rentang frekuensi: 100 kHz - 14 GHz", sla: "2-4 Hari Kerja" },
        { category: 'telekomunikasi', title: "Anechoic Chamber & Antena Testing", location: "Lab Antena & Propagasi",
          desc: "Pengukuran pola radiasi antena (radiation pattern), gain antena, dan polarisasi tanpa interferensi sinyal eksternal.",
          specs: "Ruang kedap gelombang RF standar IEEE", sla: "3-5 Hari Kerja" },
        { category: 'telekomunikasi', title: "Digital Oscilloscope 1 GHz", location: "Lab Embedded & IoT",
          desc: "Verifikasi integritas sinyal digital kecepatan tinggi, analisis protokol komunikasi serial I2C/SPI/UART.",
          specs: "Sampling rate: 5 GSa/s, 4 Channel", sla: "1-2 Hari Kerja" },
        // Bioteknologi & Lingkungan
        { category: 'bioteknologi', title: "Automated Water Quality Analyzer", location: "Lab Rekayasa Lingkungan",
          desc: "Pengujian parameter COD, BOD, TSS, kekeruhan, dan keasaman sampel perairan lingkungan secara terotomasi.",
          specs: "Sesuai baku mutu air standar SNI", sla: "2-4 Hari Kerja" },
        { category: 'bioteknologi', title: "Inkubator Mikrobiologi Presisi", location: "Lab Biosains & Terapan",
          desc: "Kultivasi bakteri dan mikroorganisme dengan kontrol suhu stabil 0.1°C untuk uji antibakteri dan bioassay.",
          specs: "Rentang suhu: 5°C - 70°C", sla: "3-5 Hari Kerja" },
        { category: 'bioteknologi', title: "Centrifuge Kecepatan Tinggi", location: "Lab Sentral Riset",
          desc: "Separasi fraksi seluler, presipitasi protein, dan isolasi DNA/RNA dengan pendinginan suhu terkontrol.",
          specs: "Kecepatan maks: 15.000 RPM", sla: "1-2 Hari Kerja" },
        // Simulasi & Komputasi Riset
        { category: 'komputasi', title: "Cluster Server Simulasi Pemodelan", location: "Lab High Performance Computing",
          desc: "Komputasi paralel untuk simulasi dinamika fluida (CFD), kalkulasi kimia komputasi DFT, dan pemodelan numerik.",
          specs: "Node komputasi multi-GPU NVIDIA A100", sla: "2-5 Hari Kerja" },
        { category: 'komputasi', title: "Testing Rig Embedded AI & Edge", location: "Lab AI & Robotika",
          desc: "Uji performa inferensi model kecerdasan buatan, konsumsi daya instrumen, dan stabilitas pada perangkat edge.",
          specs: "Power meter analitik & thermal camera", sla: "2-3 Hari Kerja" },
        { category: 'komputasi', title: "Stasiun Kerja Citra Biomedis", location: "Lab Rekayasa Biomedika",
          desc: "Pemrosesan citra medis DICOM, segmentasi organ 3D, dan pelatihan deep learning klasifikasi data laboratorium.",
          specs: "Monitor kalibrasi warna medis 4K", sla: "1-3 Hari Kerja" }
    ];

    const flowSteps = [
        { n: '01', title: 'Pengajuan', sub: 'Registrasi online', sc: '#ED1E28', st: '#ED1E28', items: [
            'Pilih instrumen & parameter pengujian yang dibutuhkan',
            'Unggah dokumen pendukung (Surat Pengantar / MSDS)',
            'Tentukan jumlah & sifat spesimen sampel',
            'Dapatkan Nomor Resi & E-Ticket Pengujian resmi' ] },
        { n: '02', title: 'Verifikasi', sub: 'Validasi laboran', sc: '#F26A2D', st: '#C2410C', items: [
            'Pemeriksaan kelayakan berkas online oleh laboran',
            'Penyerahan fisik spesimen di laboratorium terkait',
            'Validasi kondisi spesimen & kelayakan wadah uji',
            'Penetapan jadwal antrean dan kesiapan instrumen' ] },
        { n: '03', title: 'Preparasi', sub: 'Persiapan & kalibrasi', sc: '#F7D488', st: '#B45309', items: [
            'Preparasi kimiawi & fisik spesimen oleh tim laboran',
            'Kalibrasi instrumen uji sesuai SOP baku',
            'Blank testing & setup baseline pengukuran alat',
            'Pencatatan log book kondisi lingkungan lab' ] },
        { n: '04', title: 'Pengujian', sub: 'Eksekusi instrumen', sc: '#EAEFB1', st: '#3F6212', items: [
            'Pengujian dengan alat laboratorium terakreditasi',
            'Akumulasi data spektrum / pengukuran kuantitatif',
            'Replikasi pengujian untuk menjamin presisi data',
            'Input rekapitulasi data hasil uji oleh laboran' ] },
        { n: '05', title: 'Sertifikasi', sub: 'Penerbitan LHU resmi', sc: '#E9F7CA', st: '#047857', items: [
            'Validasi data hasil pengujian oleh Kepala Lab',
            'Pengesahan dokumen digital dengan QR Barcode resmi',
            'Laporan Hasil Uji (LHU) terbit di sistem Telyuji',
            'Unduh sertifikat PDF resmi & pengambilan residu sampel' ] }
    ];

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function escapeHtml(value) {
        return String(value).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    // ------------------------------------------------------------------
    // Hero automatic illustration rotator (laser-scan aperture wipe)
    // ------------------------------------------------------------------
    (function () {
        var frame = document.getElementById('scanFrame');
        var slidesWrap = document.getElementById('heroSlides');
        var beam = document.getElementById('heroScanBeam');
        if (!frame || !slidesWrap) return;

        var slides = [].slice.call(slidesWrap.querySelectorAll('.tu-slide'));
        var total = slides.length;
        if (total < 2) return;

        var active = 0;
        var prev = -1;
        var busy = false;

        function preloadSlide(i) {
            var target = slides[(i + total) % total];
            if (!target) return;
            var img = target.querySelector('img');
            if (img && img.getAttribute('loading') === 'lazy') {
                img.removeAttribute('loading');
            }
        }

        function nextSlide() {
            if (busy || document.hidden) return;
            busy = true;

            var target = (active + 1) % total;
            preloadSlide(target);
            preloadSlide(target + 1);

            if (prev >= 0 && prev !== active) {
                slides[prev].classList.remove('is-prev', 'is-entering');
            }

            prev = active;
            active = target;

            var outSlide = slides[prev];
            var inSlide = slides[active];

            outSlide.classList.remove('is-active', 'is-entering');
            outSlide.classList.add('is-prev');
            outSlide.setAttribute('aria-hidden', 'true');

            inSlide.classList.remove('is-prev', 'is-entering');
            void inSlide.offsetWidth;
            inSlide.classList.add('is-active', 'is-entering');
            inSlide.setAttribute('aria-hidden', 'false');

            if (beam && !prefersReducedMotion) {
                beam.classList.remove('is-scanning');
                void beam.offsetWidth;
                beam.classList.add('is-scanning');
            }

            frame.classList.add('is-locking');
            setTimeout(function () { frame.classList.remove('is-locking'); }, 480);

            setTimeout(function () {
                outSlide.classList.remove('is-prev');
                inSlide.classList.remove('is-entering');
                busy = false;
            }, prefersReducedMotion ? 40 : 940);
        }

        preloadSlide(1);
        if (!prefersReducedMotion) {
            setInterval(nextSlide, 4200);
        }
    })();

    // ------------------------------------------------------------------
    // Sample tracking
    // ------------------------------------------------------------------
    var trackingStages = ['Pengajuan', 'Verifikasi', 'Preparasi', 'Pengujian', 'Sertifikasi'];
    var trackingColors = ['#ED1E28', '#F26A2D', '#F7D488', '#EAEFB1', '#E9F7CA'];
    var defaultSample = {
        id: 'TU-LAB-2026-0042',
        note: 'Spektrofotometri UV-Vis',
        status: 'Sedang Diuji di Lab',
        stage: 4,
        footer: 'Laboran PIC: <strong>Ahmad Fauzi, S.Si.</strong> (Lab Terpadu Kampus) &bull; Estimasi Selesai: 28 Okt 2026'
    };

    function trackingMarkup(d) {
        var segments = trackingStages.map(function (_, i) {
            var on = i < d.stage;
            var current = i === d.stage - 1;
            return '<span class="h-1.5 flex-1 ' + (current ? 'animate-pulse' : '') + '" style="background:' + (on ? trackingColors[i] : '#e5e7eb') + ';box-shadow:inset 0 0 0 1px rgba(0,0,0,.06)"></span>';
        }).join('');

        return '' +
            '<div class="flex flex-wrap items-start justify-between gap-2">' +
                '<div class="min-w-0">' +
                    '<p class="tu-mono text-sm font-semibold text-gray-950 break-all">' + escapeHtml(d.id) + '</p>' +
                    '<p class="mt-0.5 text-xs text-gray-600">' + d.note + '</p>' +
                '</div>' +
            '</div>' +
            '<div class="mt-3.5 flex gap-1" role="img" aria-label="Tahap ' + d.stage + ' dari 5: ' + trackingStages[d.stage - 1] + '">' + segments + '</div>' +
            '<div class="mt-1.5 flex justify-between tu-mono text-[10px] uppercase tracking-wider text-gray-400">' +
                '<span>Tahap ' + d.stage + '/5 &middot; ' + trackingStages[d.stage - 1] + '</span>' +
            '</div>' +
            '<p class="mt-3 text-xs leading-relaxed text-gray-500">' + d.footer + '</p>';
    }

    function showTracking(d) {
        var result = document.getElementById('trackingResult');
        result.innerHTML = trackingMarkup(d);
        result.style.animation = 'none';
        void result.offsetWidth;
        result.style.animation = 'tu-up .5s var(--ease) both';
    }

    function trackSample() {
        var input = document.getElementById('sampleTrackingInput');
        var result = document.getElementById('trackingResult');
        var value = input.value.trim();

        if (!value) {
            input.classList.add('is-invalid');
            input.style.animation = 'tu-shake .4s';
            result.innerHTML = '<p class="tu-error !mt-0" role="alert">Silakan masukkan nomor resi / ID sampel uji.</p>';
            input.focus();
            setTimeout(function () { input.style.animation = ''; }, 450);
            return;
        }
        input.classList.remove('is-invalid');

        result.innerHTML =
            '<div class="flex items-center gap-2.5 py-2 text-xs text-gray-600">' +
                '<svg class="animate-spin h-4 w-4 text-racing-red" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">' +
                    '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>' +
                    '<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>' +
                '</svg>' +
                '<span>Menghubungi basis data laboratorium Telkom University...</span>' +
            '</div>';

        setTimeout(function () {
            if (value.toUpperCase() === defaultSample.id) { showTracking(defaultSample); return; }
            showTracking({
                id: value,
                note: 'Terverifikasi di Sistem Telyuji',
                status: 'Dalam Antrean Pengujian',
                stage: 2,
                footer: 'Lokasi: Laboratorium Riset Telkom University &bull; Sampel fisik telah diterima laboran.'
            });
        }, 450);
    }
    showTracking(defaultSample);

    // ------------------------------------------------------------------
    // Flow stepper (autoplay when visible, stops after the user picks a step)
    // ------------------------------------------------------------------
    (function () {
        var tabs = document.getElementById('flowTabs');
        var panel = document.getElementById('flowPanel');
        var flow = document.getElementById('flow');
        if (!tabs || !panel) return;

        var current = 0;
        var autoplay = false;
        var duration = 5;

        tabs.innerHTML = flowSteps.map(function (s, i) {
            return '' +
                '<button type="button" role="tab" id="flow-tab-' + i + '" aria-controls="flowPanel" class="tu-step" data-i="' + i + '" style="--sc:' + s.sc + ';--st:' + s.st + ';--dur:' + duration + 's">' +
                    '<span class="bar"><i></i></span>' +
                    '<span class="tu-mono num mt-3 block text-xs font-semibold">' + s.n + '</span>' +
                    '<span class="lbl mt-1 hidden sm:block text-sm font-semibold">' + s.title + '</span>' +
                '</button>';
        }).join('');

        function renderPanel(i) {
            var s = flowSteps[i];
            panel.setAttribute('aria-labelledby', 'flow-tab-' + i);
            panel.innerHTML = '' +
                '<span class="absolute inset-x-0 top-0 h-1.5" style="background:' + s.sc + '"></span>' +
                '<div class="grid lg:grid-cols-12">' +
                    '<div class="lg:col-span-5 flex flex-row lg:flex-col items-end lg:items-start justify-between gap-6 p-6 sm:p-10 lg:border-r border-gray-200" style="--sc:' + s.st + '">' +
                        '<div class="tu-numeral tu-item" style="--i:0">' + s.n + '</div>' +
                        '<div class="tu-item lg:mt-10 text-right lg:text-left" style="--i:1">' +
                            '<p class="tu-eyebrow" style="color:' + s.st + '">Tahap ' + (i + 1) + ' / ' + flowSteps.length + '</p>' +
                            '<h3 class="mt-2 text-2xl sm:text-3xl font-extrabold tracking-tight text-gray-950">' + s.title + '</h3>' +
                            '<p class="mt-1 text-sm text-gray-500">' + s.sub + '</p>' +
                        '</div>' +
                    '</div>' +
                    '<ol class="lg:col-span-7 divide-y divide-gray-100 p-6 sm:p-10 lg:pl-12 self-center">' +
                        s.items.map(function (t, k) {
                            return '<li class="tu-item flex gap-5 py-4" style="--i:' + (k + 1) + '">' +
                                '<span class="tu-mono pt-0.5 text-xs text-gray-400">0' + (k + 1) + '</span>' +
                                '<span class="text-sm sm:text-base leading-relaxed text-gray-800">' + t + '</span>' +
                            '</li>';
                        }).join('') +
                    '</ol>' +
                '</div>';
        }

        function select(i) {
            current = i;
            [].forEach.call(tabs.children, function (btn, idx) {
                var bar = btn.querySelector('.bar i');
                btn.classList.remove('is-active');
                btn.classList.toggle('is-done', idx < i);
                btn.setAttribute('aria-selected', idx === i ? 'true' : 'false');
                btn.tabIndex = idx === i ? 0 : -1;
                bar.style.animation = autoplay ? '' : 'none';
                if (idx === i) { void btn.offsetWidth; btn.classList.add('is-active'); }
            });
            renderPanel(i);
        }

        tabs.addEventListener('click', function (e) {
            var btn = e.target.closest('.tu-step');
            if (!btn) return;
            autoplay = false;
            select(parseInt(btn.getAttribute('data-i'), 10));
        });

        tabs.addEventListener('keydown', function (e) {
            if (e.key !== 'ArrowRight' && e.key !== 'ArrowLeft') return;
            var next = (current + (e.key === 'ArrowRight' ? 1 : flowSteps.length - 1)) % flowSteps.length;
            autoplay = false;
            select(next);
            tabs.children[next].focus();
        });

        tabs.addEventListener('animationend', function (e) {
            if (autoplay && e.animationName === 'tu-fill') select((current + 1) % flowSteps.length);
        });

        select(0);

        if (!prefersReducedMotion && 'IntersectionObserver' in window) {
            var started = false;
            new IntersectionObserver(function (entries, obs) {
                if (entries[0].isIntersecting && !started) {
                    started = true;
                    autoplay = true;
                    select(0);
                    obs.disconnect();
                }
            }, { threshold: 0.45 }).observe(flow);
        }
    })();

    // ------------------------------------------------------------------
    // Catalog carousel (endless drift, drag, nudge, pause)
    // ------------------------------------------------------------------
    (function () {
        var track = document.getElementById('carousel-track');
        var wrap = document.getElementById('carouselWrap');
        if (!track) return;

        var state = { playing: !prefersReducedMotion, hover: false, drag: false, touch: false, pos: 0, setW: 0, boost: 0, speed: 0.7, step: 374 };
        var lastSet = 0;
        var visible = true;

        function pad(n) { return (n < 10 ? '0' : '') + n; }

        function renderCard(item, idx, total, clone) {
            return '' +
                '<article class="tu-card group/card w-[280px] sm:w-[330px] shrink-0 flex flex-col justify-between min-h-[22rem] bg-white border border-gray-200 p-6"' + (clone ? ' aria-hidden="true"' : '') + '>' +
                    '<span class="tu-card-bar"></span>' +
                    '<div>' +
                        '<div class="flex items-center justify-between gap-3">' +
                            '<span class="tu-mono text-xs text-gray-400">' + pad(idx + 1) + ' / ' + pad(total) + '</span>' +
                            '<span class="tu-chip tu-chip--cream">' + item.location + '</span>' +
                        '</div>' +
                        '<h3 class="mt-6 min-h-[3.1rem] line-clamp-2 text-lg font-bold leading-snug tracking-tight text-gray-950 transition-colors duration-300 group-hover/card:text-white">' + item.title + '</h3>' +
                        '<p class="mt-2.5 line-clamp-3 text-[13px] leading-relaxed text-gray-600 transition-colors duration-300 group-hover/card:text-gray-300">' + item.desc + '</p>' +
                        '<dl class="mt-5 space-y-2 border-t border-gray-100 pt-4 text-xs transition-colors duration-300 group-hover/card:border-white/15">' +
                            '<div class="flex gap-3"><dt class="tu-mono w-9 shrink-0 pt-0.5 text-[10px] uppercase text-gray-400">Spek</dt><dd class="text-gray-600 transition-colors duration-300 group-hover/card:text-gray-200">' + item.specs + '</dd></div>' +
                            '<div class="flex gap-3"><dt class="tu-mono w-9 shrink-0 pt-0.5 text-[10px] uppercase text-gray-400">SLA</dt><dd class="text-gray-600 transition-colors duration-300 group-hover/card:text-gray-200">' + item.sla + '</dd></div>' +
                        '</dl>' +
                    '</div>' +
                    '<div class="mt-6 flex items-center pt-4">' +
                        '<a href="{{ route('register') }}" class="tu-card-go text-xs font-bold text-racing-red group-hover/card:text-white"' + (clone ? ' tabindex="-1"' : '') + '>Ajukan &rarr;</a>' +
                    '</div>' +
                '</article>';
        }

        function build(category) {
            var items = category === 'all' ? allInstruments : allInstruments.filter(function (i) { return i.category === category; });
            var html = function (clone) { return items.map(function (it, i) { return renderCard(it, i, items.length, clone); }).join(''); };

            track.innerHTML = html(false);
            var gap = parseFloat(getComputedStyle(track).columnGap) || 20;
            var padL = parseFloat(getComputedStyle(track).paddingLeft) || 0;
            var padR = parseFloat(getComputedStyle(track).paddingRight) || 0;
            var one = track.scrollWidth - padL - padR + gap;
            var reps = one > 0 ? Math.ceil(track.clientWidth / one) + 3 : 3;
            for (var r = 1; r < reps; r++) track.insertAdjacentHTML('beforeend', html(true));

            var first = track.firstElementChild;
            state.step = first ? first.offsetWidth + gap : 374;
            state.setW = one;
            state.pos = one;
            state.boost = 0;
            track.scrollLeft = one;
            lastSet = track.scrollLeft;

            var count = document.getElementById('catalogCount');
            if (count) count.textContent = items.length + ' instrumen';
        }

        function frame() {
            if (visible) {
                if (state.playing && !state.hover && !state.drag && !state.touch) state.pos -= state.speed;
                if (state.boost) {
                    var d = Math.abs(state.boost) < 0.5 ? state.boost : state.boost * 0.14;
                    state.pos += d;
                    state.boost -= d;
                }
                if (state.setW) {
                    if (state.pos < state.setW) state.pos += state.setW;
                    if (state.pos >= state.setW * 2) state.pos -= state.setW;
                }
                if (!state.drag && Math.abs(track.scrollLeft - state.pos) > 0.4) {
                    track.scrollLeft = state.pos;
                    lastSet = track.scrollLeft;
                }
            }
            requestAnimationFrame(frame);
        }

        track.addEventListener('scroll', function () {
            if (Math.abs(track.scrollLeft - lastSet) > 2) {
                state.pos = track.scrollLeft;
                lastSet = track.scrollLeft;
            }
        }, { passive: true });

        wrap.addEventListener('mouseenter', function () { state.hover = true; });
        wrap.addEventListener('mouseleave', function () { state.hover = false; state.drag = false; });
        track.addEventListener('touchstart', function () { state.touch = true; }, { passive: true });
        track.addEventListener('touchend', function () { setTimeout(function () { state.touch = false; }, 1200); }, { passive: true });

        var startX = 0, startScroll = 0, moved = 0;
        track.addEventListener('mousedown', function (e) {
            state.drag = true; moved = 0; startX = e.pageX; startScroll = track.scrollLeft;
        });
        window.addEventListener('mouseup', function () { state.drag = false; });
        track.addEventListener('mousemove', function (e) {
            if (!state.drag) return;
            e.preventDefault();
            moved = Math.abs(e.pageX - startX);
            track.scrollLeft = startScroll - (e.pageX - startX) * 1.2;
        });
        track.addEventListener('click', function (e) { if (moved > 6) { e.preventDefault(); e.stopPropagation(); moved = 0; } }, true);

        if ('IntersectionObserver' in window) {
            new IntersectionObserver(function (e) { visible = e[0].isIntersecting; }).observe(wrap);
        }

        window.scrollCarousel = function (dir) {
            state.boost += (dir === 'left' ? -1 : 1) * state.step;
        };

        window.switchLabTab = function (key) {
            [].forEach.call(document.querySelectorAll('.lab-tab-btn'), function (btn) {
                btn.setAttribute('aria-selected', btn.id === 'tab-' + key ? 'true' : 'false');
            });
            build(key);
        };

        build('all');
        requestAnimationFrame(frame);
    })();

    // ------------------------------------------------------------------
    // FAQ accordion
    // ------------------------------------------------------------------
    function toggleFaq(num) {
        var item = document.getElementById('faq-item-' + num);
        var btn = document.getElementById('faq-btn-' + num);
        var open = !item.classList.contains('is-open');
        item.classList.toggle('is-open', open);
        btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    }
</script>
@endsection
