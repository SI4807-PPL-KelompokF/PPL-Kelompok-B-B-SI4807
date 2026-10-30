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
    <script src="{{ mix('js/app.js') }}" defer></script>

    <!-- Foundry Typeface: General Sans (Indian Type Foundry) -->
    <link rel="preconnect" href="https://api.fontshare.com">
    <link rel="preconnect" href="https://cdn.fontshare.com" crossorigin>
    <link href="https://api.fontshare.com/v2/css?f[]=general-sans@400,500,600,700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">

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

                    <!-- Section links (Desktop) -->
                    <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600" aria-label="Navigasi utama">
                        <a href="{{ url('/') }}#alur" class="tu-link">Alur</a>
                        <a href="{{ url('/') }}#katalog-lab" class="tu-link">Katalog</a>
                        <a href="{{ url('/') }}#faq" class="tu-link">FAQ</a>
                    </nav>

                    <!-- Auth links + Mobile menu button -->
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
                                <span class="tu-chip tu-chip--cream">
                                    {{ ucfirst(Auth::user()->role ?? 'Pemohon') }}
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

                        <button type="button" id="mobileMenuBtn" aria-expanded="false" aria-controls="mobileMenu" aria-label="Buka menu navigasi"
                            class="md:hidden inline-grid place-items-center w-9 h-9 rounded-[3px] border border-gray-300 bg-white text-gray-700 transition-colors hover:border-tiger-flame hover:text-tiger-flame">
                            <svg id="mobileMenuIconOpen" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg id="mobileMenuIconClose" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile navigation drawer -->
            <nav id="mobileMenu" class="md:hidden hidden border-t border-gray-200 bg-white" aria-label="Navigasi mobile">
                <div class="max-w-7xl mx-auto px-4 py-3 flex flex-col divide-y divide-gray-100 text-sm font-medium text-gray-700">
                    <a href="{{ url('/') }}#alur" data-mobile-link class="py-2.5 flex items-center justify-between transition-colors hover:text-tiger-flame">
                        <span>Alur Pengujian</span>
                        <span class="text-xs text-gray-400" aria-hidden="true">&rarr;</span>
                    </a>
                    <a href="{{ url('/') }}#katalog-lab" data-mobile-link class="py-2.5 flex items-center justify-between transition-colors hover:text-tiger-flame">
                        <span>Katalog Laboratorium</span>
                        <span class="text-xs text-gray-400" aria-hidden="true">&rarr;</span>
                    </a>
                    <a href="{{ url('/') }}#faq" data-mobile-link class="py-2.5 flex items-center justify-between transition-colors hover:text-tiger-flame">
                        <span>Pertanyaan Umum (FAQ)</span>
                        <span class="text-xs text-gray-400" aria-hidden="true">&rarr;</span>
                    </a>
                </div>
            </nav>
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
                        <p class="text-xs font-semibold text-gray-500">Sistem Informasi Pengujian Laboratorium</p>
                        <p class="mt-3 max-w-md text-lg font-semibold tracking-tight text-gray-950 leading-snug">
                            Dari pengajuan sampel sampai LHU resmi, satu alur yang tercatat dan bisa dilacak.
                        </p>
                    </div>
                    <nav class="md:col-span-3 text-sm space-y-2.5" aria-label="Pintasan">
                        <p class="text-xs font-semibold text-gray-400 mb-4">Jelajahi</p>
                        <a href="{{ url('/') }}#alur" class="tu-link block w-fit text-gray-700">Alur pengujian</a>
                        <a href="{{ url('/') }}#katalog-lab" class="tu-link block w-fit text-gray-700">Katalog instrumen</a>
                        <a href="{{ url('/') }}#faq" class="tu-link block w-fit text-gray-700">Pertanyaan umum</a>
                    </nav>
                    <nav class="md:col-span-3 text-sm space-y-2.5" aria-label="Akun">
                        <p class="text-xs font-semibold text-gray-400 mb-4">Akun</p>
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
                    <p>&copy; {{ date('Y') }} Telyuji &middot; Sistem Informasi Pengujian Laboratorium Telkom University.</p>
                    <p class="font-medium text-gray-600">Teliti dan Teruji</p>
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

            // Mobile navigation menu toggle
            var mobileBtn = document.getElementById('mobileMenuBtn');
            var mobileMenu = document.getElementById('mobileMenu');
            var iconOpen = document.getElementById('mobileMenuIconOpen');
            var iconClose = document.getElementById('mobileMenuIconClose');
            function setMobileMenu(open) {
                if (!mobileBtn || !mobileMenu) return;
                mobileMenu.classList.toggle('hidden', !open);
                mobileBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
                if (iconOpen) iconOpen.classList.toggle('hidden', open);
                if (iconClose) iconClose.classList.toggle('hidden', !open);
            }
            if (mobileBtn && mobileMenu) {
                mobileBtn.addEventListener('click', function () {
                    setMobileMenu(mobileMenu.classList.contains('hidden'));
                });
                [].forEach.call(mobileMenu.querySelectorAll('[data-mobile-link]'), function (link) {
                    link.addEventListener('click', function () { setMobileMenu(false); });
                });
            }

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

            // Live password security requirements indicator
            [].forEach.call(document.querySelectorAll('[data-password-rules]'), function (list) {
                var input = document.getElementById(list.getAttribute('data-password-rules'));
                if (!input) return;
                function check() {
                    var val = input.value || '';
                    var checks = {
                        length: val.length >= 8,
                        case: /[a-z]/.test(val) && /[A-Z]/.test(val),
                        symbol: /[^a-zA-Z0-9]/.test(val)
                    };
                    [].forEach.call(list.querySelectorAll('[data-rule]'), function (li) {
                        var ok = !!checks[li.getAttribute('data-rule')];
                        li.classList.toggle('text-emerald-700', ok);
                        li.classList.toggle('font-medium', ok);
                        li.classList.toggle('text-gray-500', !ok);
                    });
                }
                input.addEventListener('input', check);
                check();
            });

            // Live password confirmation match indicator
            [].forEach.call(document.querySelectorAll('[data-password-match]'), function (hint) {
                var passInput = document.getElementById(hint.getAttribute('data-password-match'));
                var confInput = document.getElementById(hint.getAttribute('data-password-confirm'));
                var textEl = hint.querySelector('[data-match-text]');
                if (!passInput || !confInput || !textEl) return;
                function checkMatch() {
                    var confVal = confInput.value || '';
                    if (!confVal) {
                        hint.classList.add('hidden');
                        hint.classList.remove('flex');
                        confInput.classList.remove('is-invalid');
                        return;
                    }
                    var matched = confVal === (passInput.value || '');
                    hint.classList.remove('hidden');
                    hint.classList.add('flex');
                    hint.classList.toggle('text-emerald-700', matched);
                    hint.classList.toggle('text-racing-red', !matched);
                    confInput.classList.toggle('is-invalid', !matched);
                    textEl.textContent = matched ? 'Konfirmasi kata sandi cocok' : 'Konfirmasi kata sandi belum cocok';
                }
                passInput.addEventListener('input', checkMatch);
                confInput.addEventListener('input', checkMatch);
                checkMatch();
            });
        })();
    </script>
</body>
</html>
