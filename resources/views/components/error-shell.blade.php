@props([
    'code' => '404',
    'strokeColor' => '#ED1E28',
    'title' => 'Halaman Tidak Ditemukan',
    'description' => 'Halaman yang Anda tuju tidak tersedia atau telah dipindahkan.',
])

<div class="relative overflow-hidden py-16 sm:py-24" dusk="error-page-{{ $code }}">
    <x-bg-pattern uid="err-pat-{{ $code }}" />

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid items-center gap-10 lg:grid-cols-12 lg:gap-16">
            <!-- Left: Outlined Numeral + Minimalist Copy & Actions -->
            <div class="lg:col-span-7 tu-reveal" style="--sc: {{ $strokeColor }};">
                <div class="tu-numeral tu-item select-none mb-4" style="--i:0">
                    {{ $code }}
                </div>

                <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-gray-950 leading-[1.08]">
                    {{ $title }}
                </h1>

                <p class="mt-4 max-w-lg text-base sm:text-lg leading-relaxed text-gray-600">
                    {{ $description }}
                </p>

                <div class="mt-8 flex flex-wrap items-center gap-3">
                    @auth
                        <a href="{{ Auth::user()->homeRoute() }}" dusk="error-primary-cta" class="tu-btn">
                            <span>Kembali ke Dashboard</span>
                            <span class="arr" aria-hidden="true">&rarr;</span>
                        </a>
                    @else
                        <a href="{{ url('/') }}" dusk="error-primary-cta" class="tu-btn">
                            <span>Kembali ke Beranda</span>
                            <span class="arr" aria-hidden="true">&rarr;</span>
                        </a>
                    @endauth

                    <button type="button" onclick="window.history.back()" class="tu-btn tu-btn--ghost">
                        <span>Kembali</span>
                    </button>
                </div>
            </div>

            <!-- Right: 2D Flat Geometric Laboratory Scene -->
            <div class="lg:col-span-5 tu-reveal [--d:120ms]">
                <div class="relative border border-gray-200 bg-white p-6 sm:p-8 lg:p-10 shadow-sm">
                    <span class="tu-bracket tu-bracket--tl" aria-hidden="true"></span>
                    <span class="tu-bracket tu-bracket--tr" aria-hidden="true"></span>
                    <span class="tu-bracket tu-bracket--bl" aria-hidden="true"></span>
                    <span class="tu-bracket tu-bracket--br" aria-hidden="true"></span>

                    <!-- Status indicator dots -->
                    <div class="flex items-center gap-2 border-b border-gray-100 pb-4">
                        <span class="h-2.5 w-2.5 rounded-full bg-racing-red"></span>
                        <span class="h-2.5 w-2.5 rounded-full bg-jasmine"></span>
                        <span class="h-2.5 w-2.5 rounded-full bg-frosted-mint"></span>
                    </div>

                    <!-- Animated 2D Laboratory Scene -->
                    <div class="pt-8 pb-3 flex items-center justify-center">
                        @if ($code === '404')
                            <!-- 404: Optical Scanner & Missing Sample Vial -->
                            <svg class="w-full max-w-[240px] h-auto" viewBox="0 0 240 160" fill="none" aria-hidden="true">
                                <line x1="20" y1="136" x2="220" y2="136" stroke="#111827" stroke-width="2.5" />
                                <rect x="26" y="122" width="188" height="14" rx="2" fill="#EAEFB1" stroke="#111827" stroke-width="2" />
                                <!-- Vial 1 -->
                                <rect x="40" y="56" width="20" height="66" rx="4" fill="#FFFFFF" stroke="#111827" stroke-width="2" />
                                <rect x="42" y="82" width="16" height="38" fill="#E9F7CA" />
                                <rect x="37" y="50" width="26" height="6" rx="1.5" fill="#111827" />
                                <!-- Vial 2 -->
                                <rect x="76" y="56" width="20" height="66" rx="4" fill="#FFFFFF" stroke="#111827" stroke-width="2" />
                                <rect x="78" y="74" width="16" height="46" fill="#F7D488" />
                                <rect x="73" y="50" width="26" height="6" rx="1.5" fill="#111827" />
                                <!-- Vial 3 -->
                                <rect x="112" y="56" width="20" height="66" rx="4" fill="#FFFFFF" stroke="#111827" stroke-width="2" />
                                <rect x="114" y="88" width="16" height="32" fill="#E9F7CA" />
                                <rect x="109" y="50" width="26" height="6" rx="1.5" fill="#111827" />
                                <!-- Missing Vial Target -->
                                <g class="tu-scene-float">
                                    <rect x="148" y="52" width="20" height="70" rx="4" fill="none" stroke="#ED1E28" stroke-width="2" stroke-dasharray="4 4" />
                                    <circle class="tu-scene-ping" cx="158" cy="86" r="14" stroke="#ED1E28" stroke-width="1.5" />
                                    <path d="M154 82l8 8M162 82l-8 8" stroke="#ED1E28" stroke-width="2.2" stroke-linecap="round" />
                                </g>
                                <!-- Vial 5 -->
                                <rect x="184" y="56" width="20" height="66" rx="4" fill="#FFFFFF" stroke="#111827" stroke-width="2" />
                                <rect x="186" y="78" width="16" height="42" fill="#F26A2D" />
                                <rect x="181" y="50" width="26" height="6" rx="1.5" fill="#111827" />
                                <!-- Laser Scan Beam -->
                                <g class="tu-scene-scan">
                                    <line x1="28" y1="76" x2="212" y2="76" stroke="#ED1E28" stroke-width="2" />
                                    <rect x="142" y="72" width="32" height="8" rx="2" fill="#ED1E28" fill-opacity="0.2" />
                                </g>
                            </svg>
                        @elseif ($code === '403')
                            <!-- 403: Airlock Security Barrier -->
                            <svg class="w-full max-w-[240px] h-auto" viewBox="0 0 240 160" fill="none" aria-hidden="true">
                                <circle class="tu-scene-spin" cx="120" cy="80" r="58" stroke="#F7D488" stroke-width="2" stroke-dasharray="8 6" />
                                <circle class="tu-scene-rev" cx="120" cy="80" r="44" stroke="#111827" stroke-width="1.5" stroke-dasharray="4 6" />
                                <g class="tu-scene-float">
                                    <path d="M120 34l36 14v28c0 24-15.5 43.5-36 50-20.5-6.5-36-26-36-50V48l36-14z" fill="#FFFFFF" stroke="#111827" stroke-width="2.5" />
                                    <rect x="106" y="74" width="28" height="22" rx="3" fill="#ED1E28" stroke="#111827" stroke-width="2" />
                                    <path d="M112 74v-7a8 8 0 0116 0v7" stroke="#111827" stroke-width="2.2" stroke-linecap="round" />
                                    <circle cx="120" cy="85" r="2.5" fill="#FFFFFF" />
                                </g>
                            </svg>
                        @elseif ($code === '419')
                            <!-- 419: Session Chronometer -->
                            <svg class="w-full max-w-[240px] h-auto" viewBox="0 0 240 160" fill="none" aria-hidden="true">
                                <circle class="tu-scene-spin" cx="120" cy="82" r="54" stroke="#EAEFB1" stroke-width="4" stroke-dasharray="12 6" />
                                <circle cx="120" cy="82" r="44" fill="#FFFFFF" stroke="#111827" stroke-width="2.5" />
                                <rect x="112" y="20" width="16" height="8" rx="2" fill="#F26A2D" stroke="#111827" stroke-width="2" />
                                <line x1="120" y1="28" x2="120" y2="38" stroke="#111827" stroke-width="2.5" />
                                <g class="tu-scene-spin">
                                    <line x1="120" y1="82" x2="120" y2="52" stroke="#ED1E28" stroke-width="2.5" stroke-linecap="round" />
                                    <line x1="120" y1="82" x2="142" y2="82" stroke="#111827" stroke-width="2.5" stroke-linecap="round" />
                                </g>
                                <circle cx="120" cy="82" r="4" fill="#ED1E28" />
                            </svg>
                        @else
                            <!-- 500 / 503 / 429: Diagnostic Waveform -->
                            <svg class="w-full max-w-[240px] h-auto" viewBox="0 0 240 160" fill="none" aria-hidden="true">
                                <rect x="28" y="24" width="184" height="112" rx="6" fill="#FFFFFF" stroke="#111827" stroke-width="2.5" />
                                <line x1="28" y1="44" x2="212" y2="44" stroke="#111827" stroke-width="2" />
                                <circle cx="42" cy="34" r="3" fill="#ED1E28" />
                                <circle cx="54" cy="34" r="3" fill="#F7D488" />
                                <circle cx="66" cy="34" r="3" fill="#E9F7CA" />
                                <g class="tu-scene-wave">
                                    <rect x="48" y="88" width="14" height="32" rx="2" fill="#EAEFB1" stroke="#111827" stroke-width="1.8" />
                                    <rect x="74" y="68" width="14" height="52" rx="2" fill="#F7D488" stroke="#111827" stroke-width="1.8" />
                                    <rect x="100" y="56" width="14" height="64" rx="2" fill="#F26A2D" stroke="#111827" stroke-width="1.8" />
                                    <rect x="126" y="76" width="14" height="44" rx="2" fill="#E9F7CA" stroke="#111827" stroke-width="1.8" />
                                    <rect x="152" y="62" width="14" height="58" rx="2" fill="#ED1E28" stroke="#111827" stroke-width="1.8" />
                                    <rect x="178" y="84" width="14" height="36" rx="2" fill="#EAEFB1" stroke="#111827" stroke-width="1.8" />
                                </g>
                                <g class="tu-scene-scan">
                                    <line x1="36" y1="72" x2="204" y2="72" stroke="#ED1E28" stroke-width="2" stroke-dasharray="5 4" />
                                </g>
                            </svg>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
