@extends('layouts.app')

@section('content')
<!-- ============================ HERO ============================ -->
<section class="relative overflow-hidden">
    <x-bg-pattern uid="hero-pat" />
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 pb-16 sm:pt-16 lg:pt-20 lg:pb-28">
        <div class="grid lg:grid-cols-12 gap-14 lg:gap-10 items-center">
            <!-- Copy -->
            <div class="lg:col-span-7">
                <h1 class="font-bold tracking-tight text-gray-950 leading-[1.06] text-4xl sm:text-6xl lg:text-[3.5rem] xl:text-[4.15rem]">
                    <span class="tu-line [--d:100ms]"><span>Layanan Pengujian</span></span>
                    <span class="tu-line [--d:220ms]"><span>Laboratorium Riset</span></span>
                    <span class="tu-line [--d:340ms]"><span>&amp; Akademik <span class="tu-mark-underline">Terpadu</span></span></span>
                </h1>

                <p class="mt-7 max-w-xl text-base sm:text-lg leading-relaxed text-gray-600 tu-reveal [--d:450ms]">
                    Fasilitas pengujian instrumen modern terakreditasi lintas fakultas dan pusat riset Telkom University. Pengajuan digital, verifikasi transparan, dan penerbitan LHU resmi.
                </p>

                <div class="mt-8 flex flex-col sm:flex-row sm:items-center gap-3 tu-reveal [--d:560ms]">
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

                <!-- Sample tracking -->
                <div data-ui class="mt-9 max-w-xl bg-white border border-gray-200 rounded-xl p-4 sm:p-5 shadow-tu-soft tu-reveal [--d:650ms]">
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

            <!-- Instrument & Facility Showcase Frame -->
            <div class="lg:col-span-5 relative">
                <div id="scanFrame" class="tu-frame aspect-[4/5] sm:aspect-[5/6] lg:aspect-[4/5] select-none">
                    <div id="heroSlides" class="absolute inset-0">
                        @foreach ($heroSlides as $idx => $slide)
                            <div class="tu-slide {{ $idx === 0 ? 'is-active' : '' }}" data-slide="{{ $idx }}" aria-hidden="{{ $idx === 0 ? 'false' : 'true' }}">
                                <img src="{{ $slide['src'] }}" alt="{{ $slide['alt'] }}" @if($idx > 1) loading="lazy" @endif>
                            </div>
                        @endforeach
                    </div>

                    <span id="heroScanBeam" class="tu-scan-beam" aria-hidden="true"></span>

                    <span class="tu-bracket tu-bracket--tl"></span>
                    <span class="tu-bracket tu-bracket--tr"></span>
                    <span class="tu-bracket tu-bracket--bl"></span>
                    <span class="tu-bracket tu-bracket--br"></span>
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
            <p class="tu-eyebrow mt-6 uppercase tracking-[.14em] text-[11px] text-gray-500">Laboratorium</p>
            <p class="mt-2 text-5xl sm:text-6xl font-extrabold tracking-tight text-gray-950 tabular-nums whitespace-nowrap"><span data-count="14" data-suffix="+">14+</span></p>
            <p class="mt-2 text-xs text-gray-600">Fakultas &amp; Pusat Riset Kampus</p>
        </div>
        <div class="tu-metric tu-reveal px-5 sm:px-8 py-9 border-b lg:border-b-0 lg:border-r border-gray-200 [--c:#F26A2D] [--d:90ms]">
            <span class="tu-tick"></span>
            <p class="tu-eyebrow mt-6 uppercase tracking-[.14em] text-[11px] text-gray-500">Alat &amp; Instrumen</p>
            <p class="mt-2 text-5xl sm:text-6xl font-extrabold tracking-tight text-gray-950 tabular-nums whitespace-nowrap"><span data-count="50" data-suffix="+">50+</span></p>
            <p class="mt-2 text-xs text-gray-600">Terkalibrasi dan bersertifikasi</p>
        </div>
        <div class="tu-metric tu-reveal px-5 sm:px-8 py-9 border-r border-gray-200 [--c:#111827] [--d:180ms]">
            <span class="tu-tick"></span>
            <p class="tu-eyebrow mt-6 uppercase tracking-[.14em] text-[11px] text-gray-500">Akses Civitas</p>
            <p class="mt-2 text-5xl sm:text-6xl font-extrabold tracking-tight text-gray-950 tabular-nums whitespace-nowrap"><span data-count="100" data-suffix="%">100%</span></p>
            <p class="mt-2 text-xs text-gray-600">Fasilitas riset akademik internal</p>
        </div>
        <div class="tu-metric tu-reveal px-5 sm:px-8 py-9 [--c:#F7D488] [--d:270ms]">
            <span class="tu-tick"></span>
            <p class="tu-eyebrow mt-6 uppercase tracking-[.14em] text-[11px] text-gray-500">Rata-rata Durasi Uji</p>
            <p class="mt-2 text-5xl sm:text-6xl font-extrabold tracking-tight text-gray-950 tabular-nums whitespace-nowrap"><span data-count="5" data-prefix="&lt; ">&lt; 5</span><span class="ml-2 text-base font-semibold tracking-normal text-gray-500">Hari</span></p>
            <p class="mt-2 text-xs text-gray-600">SLA cepat dari penyerahan sampel</p>
        </div>
    </div>
    <div class="tu-ruler tu-reveal mt-3 opacity-70" aria-hidden="true">
        <svg class="w-full h-3.5 block" preserveAspectRatio="none">
            <defs>
                <pattern id="tu-ruler-minor" width="10" height="14" patternUnits="userSpaceOnUse">
                    <line x1="0.5" y1="0" x2="0.5" y2="6" stroke="#9ca3af" stroke-width="1" />
                </pattern>
                <pattern id="tu-ruler-major" width="100" height="14" patternUnits="userSpaceOnUse">
                    <line x1="0.5" y1="0" x2="0.5" y2="14" stroke="#4b5563" stroke-width="1" />
                </pattern>
            </defs>
            <rect width="100%" height="6" fill="url(#tu-ruler-minor)" />
            <rect width="100%" height="14" fill="url(#tu-ruler-major)" />
        </svg>
    </div>
</section>

<!-- ============================ ALUR ============================ -->
<section id="alur" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-28">
    <div id="flowTrack" class="tu-journey-track">
        <div id="flow" class="tu-flow tu-journey-sticky tu-reveal">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                <h2 class="text-3xl sm:text-5xl font-bold tracking-tight text-gray-950">Alur Standar Pengujian Sampel</h2>
            </div>

            <div id="flowTabs" class="mt-8 grid grid-cols-5 gap-3 sm:gap-6" role="tablist" aria-label="Tahapan pengujian sampel"></div>
            <div id="flowPanel" class="relative mt-6 overflow-hidden rounded-3xl bg-white/70 p-6 sm:p-10 lg:min-h-[26rem]" role="tabpanel" aria-live="polite"></div>
        </div>
    </div>
</section>

<!-- ============================ KATALOG ============================ -->
<section id="katalog-lab" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-28">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
        <div class="tu-reveal max-w-2xl">
            <h2 class="text-3xl sm:text-5xl font-bold tracking-tight text-gray-950">Katalog Laboratorium &amp; Instrumen Uji</h2>
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
                <h2 class="text-3xl sm:text-5xl font-bold tracking-tight text-gray-950">Pertanyaan Umum (FAQ)</h2>
                <p class="mt-4 text-sm sm:text-base leading-relaxed text-gray-600">Informasi singkat seputar pengajuan sampel laboratorium di Telkom University.</p>
            </div>
        </div>

        <div class="lg:col-span-8 tu-reveal [--d:100ms]">
            @foreach ($faqs as $faq)
                <div id="faq-item-{{ $loop->iteration }}" class="tu-faq-item">
                    <h3>
                        <button type="button" onclick="toggleFaq({{ $loop->iteration }})" id="faq-btn-{{ $loop->iteration }}" aria-expanded="false" aria-controls="faq-content-{{ $loop->iteration }}" class="group w-full flex items-start gap-5 py-5 text-left">
                            <span class="tu-mono pt-1 text-xs text-gray-400">0{{ $loop->iteration }}</span>
                            <span class="flex-1 text-base sm:text-lg font-semibold leading-snug text-gray-950 transition-colors group-hover:text-racing-red">{{ $faq['question'] }}</span>
                            <span class="tu-plus mt-0.5 text-gray-500" aria-hidden="true"><i></i><i></i></span>
                        </button>
                    </h3>
                    <div id="faq-content-{{ $loop->iteration }}" class="tu-acc" role="region" aria-labelledby="faq-btn-{{ $loop->iteration }}">
                        <div><p class="pl-9 sm:pl-10 pr-10 pb-6 max-w-2xl text-sm sm:text-[15px] leading-relaxed text-gray-600">{{ $faq['answer'] }}</p></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ============================ CTA ============================ -->
<section class="relative mt-28 overflow-hidden bg-gray-950 text-white">
    <x-bg-pattern variant="dark" uid="cta-pat" />
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 sm:pt-32 pb-20">
        <div class="max-w-4xl tu-reveal">
            <h2 class="text-4xl sm:text-6xl lg:text-7xl font-bold tracking-tight leading-[1.06]">Siap Melakukan Pengujian Sampel Riset <span class="text-racing-red">Anda?</span></h2>
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

    <!-- Instrument ribbon -->
    <div id="instrumentRibbon" class="relative border-t border-white/10 py-7 overflow-x-hidden no-scrollbar select-none" aria-hidden="true">
        <div class="flex w-max">
            @for ($pass = 0; $pass < 2; $pass++)
                <div class="flex shrink-0 items-center">
                    @foreach ($ribbonInstruments as $instrumentLabel)
                        <span class="tu-outline-text px-6 sm:px-9 text-5xl sm:text-7xl font-bold tracking-tight whitespace-nowrap">{{ $instrumentLabel }}</span><i class="inline-block h-2.5 w-2.5 shrink-0 bg-racing-red"></i>
                    @endforeach
                </div>
            @endfor
        </div>
    </div>
</section>

<!-- Interactive Client Logic -->
<script>
    const allInstruments = @json($catalogInstruments);
    const flowSteps = @json($flowSteps);

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
            return '<span class="h-1.5 flex-1 ' + (current ? 'animate-pulse' : '') + '" style="background:' + (on ? trackingColors[i] : '#e5e7eb') + '"></span>';
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

        function fallbackTrackDisplay(code) {
            if (code.toUpperCase() === defaultSample.id) {
                showTracking(defaultSample);
                return;
            }
            showTracking({
                id: code,
                note: 'Terverifikasi di Sistem Telyuji',
                status: 'Dalam Antrean Pengujian',
                stage: 2,
                footer: 'Lokasi: Laboratorium Riset Telkom University &bull; Sampel fisik telah diterima laboran.'
            });
        }

        if (window.fetch) {
            fetch('/lacak-sampel/' + encodeURIComponent(value), {
                headers: { 'Accept': 'application/json' }
            }).then(function (res) {
                if (!res.ok) throw new Error('not_found');
                return res.json();
            }).then(function (data) {
                if (data && data.found) {
                    showTracking(data);
                } else {
                    fallbackTrackDisplay(value);
                }
            }).catch(function () {
                fallbackTrackDisplay(value);
            });
            return;
        }

        setTimeout(function () {
            fallbackTrackDisplay(value);
        }, 450);
    }
    showTracking(defaultSample);

    // ------------------------------------------------------------------
    // Flow stepper & Fluid Scroll-Driven Journey
    // ------------------------------------------------------------------
    (function () {
        var tabs = document.getElementById('flowTabs');
        var panel = document.getElementById('flowPanel');
        var trackEl = document.getElementById('flowTrack');
        if (!tabs || !panel) return;

        var arcOffsets = ['0px', '18px', '28px', '10px'];
        var current = -1;
        var manualLockY = null;

        function renderStageScene(i) {
            if (i === 0) {
                return '' +
                    '<svg viewBox="0 0 160 160" class="w-full h-full overflow-visible" aria-hidden="true">' +
                        '<circle cx="80" cy="80" r="64" fill="none" stroke="#f3f4f6" stroke-width="2"/>' +
                        '<circle cx="80" cy="80" r="64" fill="none" stroke="#ed1e28" stroke-width="2" stroke-dasharray="14 22" class="tu-scene-spin"/>' +
                        '<g class="tu-scene-float">' +
                            '<rect x="38" y="34" width="60" height="82" rx="8" fill="#ffffff" stroke="#111827" stroke-width="2.5"/>' +
                            '<rect x="48" y="47" width="26" height="6" rx="3" fill="#ed1e28"/>' +
                            '<rect x="48" y="61" width="40" height="4" rx="2" fill="#e5e7eb"/>' +
                            '<rect x="48" y="71" width="32" height="4" rx="2" fill="#e5e7eb"/>' +
                            '<rect x="48" y="81" width="36" height="4" rx="2" fill="#e5e7eb"/>' +
                            '<path d="M50 96v10M56 96v10M64 96v10M70 96v10M78 96v10M84 96v10" stroke="#111827" stroke-width="2" stroke-linecap="round"/>' +
                            '<rect x="92" y="58" width="30" height="64" rx="15" fill="#ffffff" stroke="#111827" stroke-width="2.5"/>' +
                            '<rect x="89" y="50" width="36" height="11" rx="4" fill="#ed1e28"/>' +
                            '<path d="M95 88h24v19a12 12 0 01-24 0V88z" fill="#eaefb1"/>' +
                        '</g>' +
                        '<g class="tu-scene-scan">' +
                            '<line x1="24" y1="68" x2="136" y2="68" stroke="#ed1e28" stroke-width="2.5" stroke-linecap="round"/>' +
                            '<circle cx="24" cy="68" r="4" fill="#ed1e28" class="tu-scene-ping"/>' +
                            '<circle cx="136" cy="68" r="4" fill="#ed1e28" class="tu-scene-ping"/>' +
                        '</g>' +
                    '</svg>';
            }
            if (i === 1) {
                return '' +
                    '<svg viewBox="0 0 160 160" class="w-full h-full overflow-visible" aria-hidden="true">' +
                        '<circle cx="80" cy="80" r="66" fill="none" stroke="#fde68a" stroke-width="2" stroke-dasharray="10 14" class="tu-scene-spin"/>' +
                        '<circle cx="80" cy="80" r="52" fill="none" stroke="#f26a2d" stroke-width="2" stroke-dasharray="28 18" class="tu-scene-rev"/>' +
                        '<g class="tu-scene-float">' +
                            '<path d="M68 42h24M73 42v20l-21 36a10 10 0 008.6 15h38.8a10 10 0 008.6-15L87 62V42" fill="#ffffff" stroke="#111827" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>' +
                            '<path d="M57 92h46l6 10a5 5 0 01-4.3 7.5H55.3A5 5 0 0151 102l6-10z" fill="#f7d488"/>' +
                        '</g>' +
                        '<g class="tu-scene-scan">' +
                            '<line x1="34" y1="78" x2="126" y2="78" stroke="#f26a2d" stroke-width="2.5" stroke-linecap="round"/>' +
                        '</g>' +
                        '<circle cx="116" cy="46" r="15" fill="#111827"/>' +
                        '<path d="M109 46l5 5 9-10" fill="none" stroke="#f7d488" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="tu-scene-draw"/>' +
                    '</svg>';
            }
            if (i === 2) {
                return '' +
                    '<svg viewBox="0 0 160 160" class="w-full h-full overflow-visible" aria-hidden="true">' +
                        '<circle cx="80" cy="80" r="64" fill="none" stroke="#fef08a" stroke-width="2.5"/>' +
                        '<circle cx="80" cy="80" r="64" fill="none" stroke="#b45309" stroke-width="2.5" stroke-dasharray="8 16" class="tu-scene-spin"/>' +
                        '<path d="M74 18h12v18l-3 12h-6l-3-12V18z" fill="#111827"/>' +
                        '<circle cx="80" cy="56" r="4.5" fill="#f26a2d" class="tu-scene-drop"/>' +
                        '<g class="tu-scene-float">' +
                            '<rect x="50" y="68" width="60" height="62" rx="12" fill="#ffffff" stroke="#111827" stroke-width="2.5"/>' +
                            '<rect x="55" y="92" width="50" height="33" rx="8" fill="#f7d488" class="tu-scene-wave"/>' +
                            '<line x1="58" y1="80" x2="70" y2="80" stroke="#111827" stroke-width="2" stroke-linecap="round"/>' +
                            '<line x1="58" y1="92" x2="66" y2="92" stroke="#111827" stroke-width="2" stroke-linecap="round"/>' +
                            '<line x1="58" y1="104" x2="70" y2="104" stroke="#111827" stroke-width="2" stroke-linecap="round"/>' +
                            '<circle cx="74" cy="112" r="3.5" fill="#ffffff" class="tu-scene-ping"/>' +
                            '<circle cx="90" cy="103" r="2.5" fill="#ffffff" class="tu-scene-ping"/>' +
                        '</g>' +
                    '</svg>';
            }
            if (i === 3) {
                return '' +
                    '<svg viewBox="0 0 160 160" class="w-full h-full overflow-visible" aria-hidden="true">' +
                        '<circle cx="80" cy="80" r="66" fill="none" stroke="#eaefb1" stroke-width="3"/>' +
                        '<circle cx="80" cy="80" r="66" fill="none" stroke="#111827" stroke-width="2" stroke-dasharray="16 20" class="tu-scene-rev"/>' +
                        '<rect x="30" y="42" width="100" height="76" rx="12" fill="#111827"/>' +
                        '<rect x="44" y="66" width="8" height="36" rx="4" fill="#eaefb1" class="tu-scene-wave"/>' +
                        '<rect x="60" y="54" width="8" height="48" rx="4" fill="#ed1e28" class="tu-scene-wave" style="animation-delay:.2s"/>' +
                        '<rect x="76" y="60" width="8" height="42" rx="4" fill="#f7d488" class="tu-scene-wave" style="animation-delay:.4s"/>' +
                        '<rect x="92" y="50" width="8" height="52" rx="4" fill="#e9f7ca" class="tu-scene-wave" style="animation-delay:.15s"/>' +
                        '<rect x="108" y="64" width="8" height="38" rx="4" fill="#f26a2d" class="tu-scene-wave" style="animation-delay:.35s"/>' +
                        '<path d="M36 82 Q 58 52, 80 82 T 124 82" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" class="tu-scene-draw"/>' +
                        '<circle cx="124" cy="42" r="6" fill="#ed1e28" class="tu-scene-ping"/>' +
                    '</svg>';
            }
            return '' +
                '<svg viewBox="0 0 160 160" class="w-full h-full overflow-visible" aria-hidden="true">' +
                    '<circle cx="80" cy="80" r="66" fill="none" stroke="#bbf7d0" stroke-width="2.5" stroke-dasharray="12 12" class="tu-scene-spin"/>' +
                    '<circle cx="80" cy="80" r="54" fill="none" stroke="#059669" stroke-width="1.75" stroke-dasharray="24 14" class="tu-scene-rev"/>' +
                    '<g class="tu-scene-float">' +
                        '<rect x="42" y="28" width="76" height="98" rx="10" fill="#ffffff" stroke="#111827" stroke-width="2.5"/>' +
                        '<rect x="54" y="42" width="32" height="6" rx="3" fill="#ed1e28"/>' +
                        '<rect x="54" y="56" width="52" height="4" rx="2" fill="#e5e7eb"/>' +
                        '<rect x="54" y="66" width="44" height="4" rx="2" fill="#e5e7eb"/>' +
                        '<rect x="54" y="84" width="22" height="22" rx="4" fill="#111827"/>' +
                        '<rect x="58" y="88" width="6" height="6" fill="#ffffff"/>' +
                        '<rect x="66" y="96" width="6" height="6" fill="#ffffff"/>' +
                        '<circle cx="98" cy="96" r="18" fill="#e9f7ca" stroke="#059669" stroke-width="2.5"/>' +
                        '<path d="M90 96l5 5 11-11" fill="none" stroke="#065f46" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="tu-scene-draw"/>' +
                    '</g>' +
                    '<circle cx="34" cy="48" r="4" fill="#059669" class="tu-scene-ping"/>' +
                    '<circle cx="128" cy="112" r="4" fill="#ed1e28" class="tu-scene-ping"/>' +
                '</svg>';
        }

        tabs.innerHTML = flowSteps.map(function (s, i) {
            return '' +
                '<button type="button" role="tab" id="flow-tab-' + i + '" aria-controls="flowPanel" class="tu-step" data-i="' + i + '" style="--sc:' + s.sc + ';--st:' + s.st + ';--p:0">' +
                    '<div class="flex items-center gap-2.5">' +
                        '<span class="tu-orb" aria-hidden="true"></span>' +
                        '<span class="tu-mono num text-xs font-bold">' + s.n + '</span>' +
                        '<span class="lbl hidden sm:inline text-sm font-semibold truncate">' + s.title + '</span>' +
                    '</div>' +
                    '<span class="bar mt-3"><i></i></span>' +
                '</button>';
        }).join('');

        function renderPanel(i) {
            var s = flowSteps[i];
            var curveP = ((i + 1) / flowSteps.length).toFixed(3);
            panel.setAttribute('aria-labelledby', 'flow-tab-' + i);
            panel.style.setProperty('--sc', s.st);
            panel.style.setProperty('--sc-bar', s.sc);
            panel.innerHTML = '' +
                '<svg class="pointer-events-none absolute inset-0 h-full w-full opacity-90" viewBox="0 0 1200 420" fill="none" preserveAspectRatio="none" aria-hidden="true">' +
                    '<path class="tu-journey-guide" d="M 30 330 C 280 50, 520 370, 780 120 C 940 -10, 1060 280, 1170 70" stroke="#f3f4f6" stroke-width="3" stroke-dasharray="8 10" stroke-linecap="round"/>' +
                    '<path id="journeyCurvePath" pathLength="1000" class="tu-journey-curve" style="--curve-p:' + curveP + '" d="M 30 330 C 280 50, 520 370, 780 120 C 940 -10, 1060 280, 1170 70" stroke="' + s.sc + '" stroke-width="4" stroke-linecap="round"/>' +
                '</svg>' +
                '<div class="relative z-10 grid lg:grid-cols-12 gap-8 lg:gap-10 items-center">' +
                    '<div class="lg:col-span-5 flex flex-col justify-between gap-6" style="--sc:' + s.st + '">' +
                        '<div class="flex items-center gap-5 sm:gap-7">' +
                            '<div class="tu-scene shrink-0">' + renderStageScene(i) + '</div>' +
                            '<div class="tu-numeral tu-item select-none" style="--i:0">' + s.n + '</div>' +
                        '</div>' +
                        '<div class="tu-item" style="--i:1">' +
                            '<h3 class="text-3xl sm:text-4xl font-bold tracking-tight text-gray-950">' + s.title + '</h3>' +
                            '<p class="mt-1.5 text-sm sm:text-base font-medium text-gray-500">' + s.sub + '</p>' +
                        '</div>' +
                    '</div>' +
                    '<div class="lg:col-span-7">' +
                        '<ol id="journeyList" class="space-y-3">' +
                            s.items.map(function (t, k) {
                                var arc = arcOffsets[k] || '0px';
                                return '<li class="tu-checkpoint tu-item flex items-center gap-4 p-3.5 sm:px-5 sm:py-4" data-ckpt="' + k + '" style="--i:' + (k + 1) + ';--sc:' + s.st + ';--arc:' + arc + '">' +
                                    '<span class="tu-checkpoint-node tu-mono shrink-0 text-xs font-bold">0' + (k + 1) + '</span>' +
                                    '<span class="text-sm sm:text-base font-medium leading-relaxed text-gray-900">' + t + '</span>' +
                                '</li>';
                            }).join('') +
                        '</ol>' +
                    '</div>' +
                '</div>';
        }

        function updateSubProgress(stepIdx, localP) {
            var s = flowSteps[stepIdx];
            var activeSub = Math.min(s.items.length - 1, Math.floor(localP * s.items.length));

            [].forEach.call(tabs.children, function (btn, idx) {
                var p = idx < stepIdx ? 1 : (idx === stepIdx ? Math.max(0.18, localP) : 0);
                btn.style.setProperty('--p', p.toFixed(3));
            });

            var curve = document.getElementById('journeyCurvePath');
            if (curve) {
                var overall = Math.min(1, Math.max(0.12, (stepIdx + localP) / flowSteps.length));
                curve.style.setProperty('--curve-p', overall.toFixed(3));
            }

            var ckpts = panel.querySelectorAll('.tu-checkpoint');
            [].forEach.call(ckpts, function (li, k) {
                li.classList.toggle('is-reached', k <= activeSub);
                li.classList.toggle('is-current', k === activeSub);
            });
        }

        function select(i, localP) {
            var p = typeof localP === 'number' ? localP : 1;
            var changed = current !== i;
            current = i;
            [].forEach.call(tabs.children, function (btn, idx) {
                btn.classList.toggle('is-active', idx === i);
                btn.classList.toggle('is-done', idx < i);
                btn.setAttribute('aria-selected', idx === i ? 'true' : 'false');
                btn.tabIndex = idx === i ? 0 : -1;
            });
            if (changed) {
                renderPanel(i);
            }
            updateSubProgress(i, p);
        }

        tabs.addEventListener('click', function (e) {
            var btn = e.target.closest('.tu-step');
            if (!btn) return;
            manualLockY = window.scrollY || 0;
            select(parseInt(btn.getAttribute('data-i'), 10), 1);
        });

        tabs.addEventListener('keydown', function (e) {
            if (e.key !== 'ArrowRight' && e.key !== 'ArrowLeft') return;
            var next = (current + (e.key === 'ArrowRight' ? 1 : flowSteps.length - 1)) % flowSteps.length;
            manualLockY = window.scrollY || 0;
            select(next, 1);
            tabs.children[next].focus();
        });

        function onJourneyScroll() {
            if (!trackEl || prefersReducedMotion) return;
            var y = window.scrollY || 0;
            if (manualLockY !== null) {
                if (Math.abs(y - manualLockY) < 24) return;
                manualLockY = null;
            }
            var rect = trackEl.getBoundingClientRect();
            var vh = window.innerHeight || 1;
            var scrollable = rect.height - vh * 0.72;
            if (scrollable <= 40) return;

            var scrolled = (vh * 0.22) - rect.top;
            if (scrolled < -vh * 0.3 || scrolled > scrollable + vh * 0.5) return;

            var progress = Math.min(0.9999, Math.max(0, scrolled / scrollable));
            var scaled = progress * flowSteps.length;
            var stepIdx = Math.min(flowSteps.length - 1, Math.floor(scaled));
            var localP = Math.min(1, Math.max(0.08, scaled - stepIdx));
            select(stepIdx, localP);
        }

        select(0, 0.35);
        window.addEventListener('scroll', onJourneyScroll, { passive: true });
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
            var gradId = 'scrim-' + idx + (clone ? '-c' : '');
            return '' +
                '<article class="tu-card group/card w-[300px] sm:w-[336px] h-[430px] sm:h-[448px] shrink-0 flex flex-col justify-end p-6 sm:p-7 text-white"' + (clone ? ' aria-hidden="true"' : '') + '>' +
                    '<img src="' + item.image + '" onerror="this.onerror=null;this.src=\'' + item.fallback + '\';" alt="' + escapeHtml(item.title) + '" class="tu-card-img" loading="lazy">' +
                    '<svg class="tu-card-scrim h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none" aria-hidden="true">' +
                        '<defs>' +
                            '<linearGradient id="' + gradId + '" x1="0" y1="0" x2="0" y2="1">' +
                                '<stop offset="0" stop-color="#0b0d11" stop-opacity="0" />' +
                                '<stop offset="0.34" stop-color="#0b0d11" stop-opacity="0.06" />' +
                                '<stop offset="0.54" stop-color="#0b0d11" stop-opacity="0.78" />' +
                                '<stop offset="0.74" stop-color="#0b0d11" stop-opacity="0.93" />' +
                                '<stop offset="1" stop-color="#0b0d11" stop-opacity="0.98" />' +
                            '</linearGradient>' +
                        '</defs>' +
                        '<rect width="100" height="100" fill="url(#' + gradId + ')" />' +
                    '</svg>' +
                    '<div class="relative z-10 flex flex-col">' +
                        '<h3 class="line-clamp-1 text-xl sm:text-[22px] font-bold leading-snug tracking-tight text-white">' + item.title + '</h3>' +
                        '<p class="mt-2 line-clamp-3 text-xs sm:text-[13px] leading-relaxed text-white/80">' + item.desc + '</p>' +
                        '<div class="mt-4 flex flex-wrap items-center gap-2">' +
                            '<span class="tu-chip tu-chip--cream truncate max-w-[185px]">' + item.location + '</span>' +
                            '<span class="tu-card-pill shrink-0">' + item.sla + '</span>' +
                        '</div>' +
                        '<a href="{{ route('register') }}" class="tu-card-btn mt-5"' + (clone ? ' tabindex="-1"' : '') + '>Ajukan Pengujian</a>' +
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
    // Instrument ribbon continuous drift (scrollLeft via rAF)
    // ------------------------------------------------------------------
    (function () {
        var ribbon = document.getElementById('instrumentRibbon');
        if (!ribbon || prefersReducedMotion) return;
        var paused = false;
        var pos = 0;
        ribbon.addEventListener('mouseenter', function () { paused = true; });
        ribbon.addEventListener('mouseleave', function () { paused = false; });
        function tick() {
            if (!paused) {
                var half = ribbon.scrollWidth / 2;
                pos += 0.65;
                if (half > 0 && pos >= half) pos -= half;
                ribbon.scrollLeft = pos;
            }
            requestAnimationFrame(tick);
        }
        requestAnimationFrame(tick);
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
