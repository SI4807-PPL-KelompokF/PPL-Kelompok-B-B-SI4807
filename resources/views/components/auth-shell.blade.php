@props([
    'heading' => '',
    'subheading' => '',
    'asideText' => 'Satu akun untuk mengajukan, melacak, dan mengunduh hasil pengujian laboratorium Telkom University.',
])

<div class="grid min-h-[calc(100vh-4rem)] lg:grid-cols-[5fr_6fr]">
    <!-- Brand panel -->
    <aside class="relative hidden lg:flex flex-col justify-center overflow-hidden bg-gray-950 p-12 xl:p-16 text-white" aria-hidden="true">
        <div class="tu-grid-dark absolute inset-0 opacity-60"></div>

        <div class="relative -mt-16">
            <h2 class="text-5xl xl:text-7xl font-extrabold tracking-tight leading-[1.02]">
                <span class="tu-line [--d:100ms]"><span>Teliti</span></span>
                <span class="tu-line [--d:230ms]"><span>dan <span class="text-racing-red">Teruji.</span></span></span>
            </h2>
            <p class="mt-6 max-w-sm text-sm leading-relaxed text-gray-400">
                {{ $asideText }}
            </p>
        </div>

        <!-- Oscilloscope traces -->
        <div class="pointer-events-none absolute inset-x-0 bottom-0 h-40 overflow-hidden">
            <svg class="tu-wave absolute left-0 top-0 h-full" viewBox="0 0 1200 200" preserveAspectRatio="none">
                <path d="M0,100 C40,20 110,20 150,100 S260,180 300,100 S410,20 450,100 S560,180 600,100 S710,20 750,100 S860,180 900,100 S1010,20 1050,100 S1160,180 1200,100" fill="none" stroke="#ED1E28" stroke-width="2" vector-effect="non-scaling-stroke"/>
            </svg>
            <svg class="tu-wave tu-wave--b absolute left-0 top-0 h-full opacity-50" viewBox="0 0 1200 200" preserveAspectRatio="none">
                <path transform="translate(0 100) scale(1 .55) translate(0 -100)" d="M0,100 C40,20 110,20 150,100 S260,180 300,100 S410,20 450,100 S560,180 600,100 S710,20 750,100 S860,180 900,100 S1010,20 1050,100 S1160,180 1200,100" fill="none" stroke="#F26A2D" stroke-width="1.5" vector-effect="non-scaling-stroke"/>
            </svg>
            <svg class="tu-wave tu-wave--c absolute left-0 top-0 h-full opacity-25" viewBox="0 0 1200 200" preserveAspectRatio="none">
                <path transform="translate(0 100) scale(1 .3) translate(0 -100)" d="M0,100 C40,20 110,20 150,100 S260,180 300,100 S410,20 450,100 S560,180 600,100 S710,20 750,100 S860,180 900,100 S1010,20 1050,100 S1160,180 1200,100" fill="none" stroke="#fff" stroke-width="1" vector-effect="non-scaling-stroke"/>
            </svg>
        </div>
    </aside>

    <!-- Form panel -->
    <section class="relative flex items-center justify-center px-4 py-12 sm:px-10">
        <div class="tu-grid-bg absolute inset-0" aria-hidden="true"></div>

        <div class="relative w-full max-w-md bg-white/80 sm:bg-white border border-gray-200 p-6 sm:p-10 shadow-tu-auth tu-reveal">
            <h1 class="text-3xl font-extrabold tracking-tight text-gray-950">{{ $heading }}</h1>
            <p class="mt-2 text-sm leading-relaxed text-gray-600">{{ $subheading }}</p>

            <div class="mt-8">
                {{ $slot }}
            </div>

            {{ $foot ?? '' }}
        </div>
    </section>
</div>

