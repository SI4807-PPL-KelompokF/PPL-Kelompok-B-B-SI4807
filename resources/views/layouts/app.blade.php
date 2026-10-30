<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#ED1E28">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-telyuji.png') }}">

    <title>{{ config('app.name', 'Telyuji') }} - Sistem Pengujian Laboratorium</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <script>document.documentElement.classList.add('js');</script>
</head>
<body class="h-full flex flex-col font-sans text-gray-900 antialiased selection:bg-racing-red selection:text-white @yield('body_class')">
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:bg-white focus:px-3 focus:py-2 focus:text-sm focus:font-semibold focus:text-racing-red">Lewati ke konten utama</a>
    <div id="tu-progress" class="fixed top-0 inset-x-0 z-50 h-0.5 bg-racing-red origin-left scale-x-0" aria-hidden="true"></div>

    <div id="app" class="min-h-full flex flex-col">
        <header id="tu-header" class="tu-header sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <!-- Brand / Logo -->
                    <a href="{{ url('/') }}" class="tu-brand flex items-center gap-3 rounded-md" aria-label="Telyuji - Beranda">
                        <x-brand-logo />
                    </a>

                    <!-- Section links -->
                    <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600" aria-label="Navigasi utama">
                        <a href="{{ url('/') }}#alur" class="tu-link">Alur</a>
                        <a href="{{ url('/') }}#katalog-lab" class="tu-link">Katalog</a>
                        <a href="{{ url('/') }}#faq" class="tu-link">FAQ</a>
                    </nav>

                    <!-- Auth links -->
                    <div class="flex items-center gap-2 sm:gap-4">
                        @guest
                            @if (Route::has('login'))
                                <a href="{{ route('login') }}" class="tu-link text-sm font-medium text-gray-700">
                                    {{ __('Login') }}
                                </a>
                            @endif

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="tu-btn tu-btn--sm">
                                    <span>{{ __('Daftar') }}</span>
                                </a>
                            @endif
                        @else
                            <div class="flex items-center gap-3">
                                <span class="hidden sm:inline text-sm font-medium text-gray-700">
                                    {{ Auth::user()->name }}
                                </span>
                                <span class="tu-chip tu-chip--cream uppercase tracking-wider">
                                    {{ Auth::user()->role ?? 'pemohon' }}
                                </span>
                                <a href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                   class="tu-btn tu-btn--sm tu-btn--ghost">
                                    {{ __('Logout') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                            </div>
                        @endguest
                    </div>
                </div>
            </div>
        </header>

        <main id="main" class="flex-1 w-full">
            @yield('content')
        </main>

        <footer class="site-footer relative overflow-hidden bg-white border-t border-gray-200 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-6 relative z-10">
                <div class="grid gap-10 md:grid-cols-12">
                    <div class="md:col-span-6">
                        <div class="mb-4">
                            <x-brand-logo size="sm" />
                        </div>
                        <p class="tu-eyebrow text-gray-500">Sistem Informasi Pengujian Laboratorium</p>
                        <p class="mt-3 max-w-md text-lg font-semibold tracking-tight text-gray-950 leading-snug">
                            Dari pengajuan sampel sampai LHU resmi, satu alur yang tercatat dan bisa dilacak.
                        </p>
                    </div>
                    <nav class="md:col-span-3 text-sm space-y-2.5" aria-label="Pintasan">
                        <p class="tu-eyebrow text-gray-400 mb-4">Jelajahi</p>
                        <a href="{{ url('/') }}#alur" class="tu-link block w-fit text-gray-700">Alur pengujian</a>
                        <a href="{{ url('/') }}#katalog-lab" class="tu-link block w-fit text-gray-700">Katalog instrumen</a>
                        <a href="{{ url('/') }}#faq" class="tu-link block w-fit text-gray-700">Pertanyaan umum</a>
                    </nav>
                    <nav class="md:col-span-3 text-sm space-y-2.5" aria-label="Akun">
                        <p class="tu-eyebrow text-gray-400 mb-4">Akun</p>
                        @if (Route::has('login'))
                            <a href="{{ route('login') }}" class="tu-link block w-fit text-gray-700">Masuk</a>
                        @endif
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="tu-link block w-fit text-gray-700">Daftar pemohon</a>
                        @endif
                    </nav>
                </div>

                <p class="tu-wordmark mt-12 -mb-3 text-[24vw] sm:text-[19vw] lg:text-[15rem]" aria-hidden="true">Telyuji</p>
            </div>
            <div class="relative z-10 border-t border-gray-200 bg-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-gray-500">
                    <p>&copy; {{ date('Y') }} Telyuji &mdash; Sistem Informasi Pengujian Laboratorium Telkom University.</p>
                    <p class="tu-mono">Teliti dan Teruji</p>
                </div>
            </div>
        </footer>
    </div>

    <script>
        (function () {
            var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            // Header state + scroll progress
            var header = document.getElementById('tu-header');
            var bar = document.getElementById('tu-progress');
            function onScroll() {
                var y = window.scrollY || 0;
                var max = document.documentElement.scrollHeight - window.innerHeight;
                if (header) header.setAttribute('data-scrolled', y > 8 ? 'true' : 'false');
                if (bar) bar.style.transform = 'scaleX(' + (max > 0 ? Math.min(y / max, 1) : 0) + ')';
            }
            window.addEventListener('scroll', onScroll, { passive: true });
            onScroll();

            // Reveal on scroll
            var reveals = [].slice.call(document.querySelectorAll('.tu-reveal'));
            if ('IntersectionObserver' in window) {
                var io = new IntersectionObserver(function (entries) {
                    entries.forEach(function (e) {
                        if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); }
                    });
                }, { threshold: 0.12, rootMargin: '0px 0px -6% 0px' });
                reveals.forEach(function (el) { io.observe(el); });
            } else {
                reveals.forEach(function (el) { el.classList.add('is-in'); });
            }

            // Count-up numbers: <span data-count="14" data-prefix="" data-suffix="+">
            var counters = [].slice.call(document.querySelectorAll('[data-count]'));
            function paint(el, v) {
                el.textContent = (el.getAttribute('data-prefix') || '') + Math.round(v) + (el.getAttribute('data-suffix') || '');
            }
            function runCounter(el) {
                var to = parseFloat(el.getAttribute('data-count')) || 0;
                if (reduce || to === 0) { paint(el, to); return; }
                var t0 = null, dur = 1400;
                function tick(t) {
                    if (t0 === null) t0 = t;
                    var p = Math.min((t - t0) / dur, 1);
                    paint(el, to * (1 - Math.pow(1 - p, 4)));
                    if (p < 1) requestAnimationFrame(tick);
                }
                requestAnimationFrame(tick);
            }
            if ('IntersectionObserver' in window) {
                var co = new IntersectionObserver(function (entries) {
                    entries.forEach(function (e) {
                        if (e.isIntersecting) { runCounter(e.target); co.unobserve(e.target); }
                    });
                }, { threshold: 0.6 });
                counters.forEach(function (el) { paint(el, 0); co.observe(el); });
            } else {
                counters.forEach(function (el) { runCounter(el); });
            }

            // Show / hide password
            [].forEach.call(document.querySelectorAll('[data-toggle-password]'), function (btn) {
                btn.addEventListener('click', function () {
                    var input = document.getElementById(btn.getAttribute('data-toggle-password'));
                    if (!input) return;
                    var show = input.type === 'password';
                    input.type = show ? 'text' : 'password';
                    btn.textContent = show ? 'Sembunyi' : 'Lihat';
                    btn.setAttribute('aria-pressed', show ? 'true' : 'false');
                });
            });
        })();
    </script>
</body>
</html>
