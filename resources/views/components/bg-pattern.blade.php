@props([
    'variant' => 'light',
    'uid' => uniqid('tu-pat-'),
])

@if ($variant === 'dark')
    <div class="pointer-events-none absolute inset-0 overflow-hidden select-none" aria-hidden="true">
        <svg class="h-full w-full" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%">
            <defs>
                <pattern id="{{ $uid }}-grid" width="64" height="64" patternUnits="userSpaceOnUse">
                    <path d="M 64 0 L 0 0 0 64" fill="none" stroke="#ffffff" stroke-width="1" stroke-opacity="0.055" />
                    <path d="M 29 32 L 35 32 M 32 29 L 32 35" fill="none" stroke="#ffffff" stroke-width="1" stroke-opacity="0.11" />
                    <rect x="0" y="0" width="2.5" height="2.5" fill="#ED1E28" fill-opacity="0.35" />
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#{{ $uid }}-grid)" />
        </svg>

        <!-- Peripheral Animated Calibration Layer (Dark) -->
        <svg data-pat-parallax="10" class="tu-pat-parallax absolute inset-0 h-full w-full" viewBox="0 0 1440 560" preserveAspectRatio="xMidYMin slice" xmlns="http://www.w3.org/2000/svg">
            <!-- Left & Right Peripheral Color Blocks (Breathing) -->
            <rect class="tu-pat-tile" x="64" y="64" width="64" height="64" fill="#ED1E28" fill-opacity="0.14" />
            <rect class="tu-pat-tile [--d:3s]" x="1280" y="64" width="64" height="64" fill="#F26A2D" fill-opacity="0.14" />
            <rect class="tu-pat-tile [--d:5s]" x="1216" y="128" width="64" height="64" fill="#F7D488" fill-opacity="0.09" />
            <rect class="tu-pat-tile [--d:2s]" x="128" y="256" width="64" height="64" fill="#E9F7CA" fill-opacity="0.08" />

            <!-- Signal Pulse Traces -->
            <path class="tu-pat-trace" d="M 64 128 H 320 V 256" fill="none" stroke="#ED1E28" stroke-width="1.5" />
            <path class="tu-pat-trace [--d:4s]" d="M 1344 192 H 1088 V 64" fill="none" stroke="#F26A2D" stroke-width="1.5" />

            <!-- Rotating Optical Reticles -->
            <g class="tu-pat-node" transform="translate(192, 128)">
                <circle cx="0" cy="0" r="14" fill="none" stroke="#ED1E28" stroke-width="1.2" stroke-opacity="0.55" stroke-dasharray="6 4" />
                <path d="M -9 0 H 9 M 0 -9 V 9" stroke="#ffffff" stroke-width="1.2" stroke-opacity="0.5" />
            </g>
            <g class="tu-pat-node [--d:4s]" transform="translate(1280, 192)">
                <rect x="-12" y="-12" width="24" height="24" fill="none" stroke="#F26A2D" stroke-width="1.2" stroke-opacity="0.55" />
                <path d="M -7 0 H 7 M 0 -7 V 7" stroke="#F7D488" stroke-width="1.2" stroke-opacity="0.65" />
            </g>
            <g class="tu-pat-node [--d:8s]" transform="translate(1152, 320)">
                <circle cx="0" cy="0" r="10" fill="none" stroke="#E9F7CA" stroke-width="1.2" stroke-opacity="0.4" />
                <path d="M -6 0 H 6 M 0 -6 V 6" stroke="#E9F7CA" stroke-width="1.2" stroke-opacity="0.5" />
            </g>
        </svg>
    </div>
@else
    <div class="tu-bg-pat-light pointer-events-none absolute inset-x-0 top-0 h-[30rem] overflow-hidden select-none" aria-hidden="true">
        <!-- Base Architectural 64px Grid -->
        <svg class="h-full w-full" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%">
            <defs>
                <pattern id="{{ $uid }}-grid" width="64" height="64" patternUnits="userSpaceOnUse">
                    <path d="M 64 0 L 0 0 0 64" fill="none" stroke="#0A0A0B" stroke-width="1" stroke-opacity="0.04" />
                    <path d="M 30 32 L 34 32 M 32 30 L 32 34" fill="none" stroke="#0A0A0B" stroke-width="1" stroke-opacity="0.08" />
                    <rect x="0" y="0" width="2" height="2" fill="#0A0A0B" fill-opacity="0.12" />
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#{{ $uid }}-grid)" />
        </svg>

        <!-- Peripheral Animated Swiss-Lab Calibration Field (Framing Outer Whitespace) -->
        <svg data-pat-parallax="8" class="tu-pat-parallax absolute inset-0 h-full w-full" viewBox="0 0 1440 480" preserveAspectRatio="xMidYMin slice" xmlns="http://www.w3.org/2000/svg">
            <!-- Left Flank Geometric Blocks -->
            <rect class="tu-pat-tile" x="0" y="64" width="64" height="64" fill="#EAEFB1" fill-opacity="0.45" />
            <rect class="tu-pat-tile [--d:2s]" x="64" y="128" width="64" height="64" fill="#F7D488" fill-opacity="0.28" />
            <rect class="tu-pat-tile [--d:5s]" x="0" y="256" width="64" height="64" fill="#E9F7CA" fill-opacity="0.4" />
            <path d="M 64 64 L 80 64 M 64 64 L 64 80" fill="none" stroke="#ED1E28" stroke-width="1.5" stroke-opacity="0.45" />

            <!-- Right Flank Geometric Blocks -->
            <rect class="tu-pat-tile [--d:1s]" x="1312" y="0" width="64" height="64" fill="#EAEFB1" fill-opacity="0.45" />
            <rect class="tu-pat-tile [--d:4s]" x="1376" y="64" width="64" height="64" fill="#F7D488" fill-opacity="0.3" />
            <rect class="tu-pat-tile [--d:6s]" x="1248" y="128" width="64" height="64" fill="#E9F7CA" fill-opacity="0.42" />
            <rect class="tu-pat-tile [--d:3s]" x="1376" y="256" width="64" height="64" fill="#EAEFB1" fill-opacity="0.38" />
            <path d="M 1312 192 L 1296 192 M 1312 192 L 1312 176" fill="none" stroke="#F26A2D" stroke-width="1.5" stroke-opacity="0.5" />

            <!-- Orthogonal Signal Traces Along Peripheral Grid Lines -->
            <path class="tu-pat-trace" d="M 0 128 H 192 V 256 H 64" fill="none" stroke="#ED1E28" stroke-width="1.5" />
            <path class="tu-pat-trace [--d:3s]" d="M 1440 128 H 1184 V 256 H 1312" fill="none" stroke="#F26A2D" stroke-width="1.5" />
            <path class="tu-pat-trace [--d:6s]" d="M 1024 0 V 64 H 1248" fill="none" stroke="#059669" stroke-width="1.5" />

            <!-- Rotating Optical Calibration Nodes (Left & Right Margins) -->
            <g class="tu-pat-node" transform="translate(128, 64)">
                <circle cx="0" cy="0" r="13" fill="none" stroke="#ED1E28" stroke-width="1.2" stroke-opacity="0.45" stroke-dasharray="5 4" />
                <path d="M -8 0 H 8 M 0 -8 V 8" stroke="#ED1E28" stroke-width="1.3" stroke-opacity="0.65" />
            </g>

            <g class="tu-pat-node [--d:4s]" transform="translate(1248, 64)">
                <rect x="-11" y="-11" width="22" height="22" fill="none" stroke="#F26A2D" stroke-width="1.2" stroke-opacity="0.45" />
                <path d="M -7 0 H 7 M 0 -7 V 7" stroke="#F26A2D" stroke-width="1.3" stroke-opacity="0.65" />
            </g>

            <g class="tu-pat-node [--d:7s]" transform="translate(1312, 256)">
                <circle cx="0" cy="0" r="11" fill="none" stroke="#0A0A0B" stroke-width="1.2" stroke-opacity="0.22" />
                <path d="M -6 0 H 6 M 0 -6 V 6" stroke="#0A0A0B" stroke-width="1.2" stroke-opacity="0.35" />
            </g>

            <g class="tu-pat-node [--d:10s]" transform="translate(64, 256)">
                <rect x="-9" y="-9" width="18" height="18" fill="none" stroke="#059669" stroke-width="1.2" stroke-opacity="0.38" />
                <path d="M -5 0 H 5 M 0 -5 V 5" stroke="#059669" stroke-width="1.2" stroke-opacity="0.5" />
            </g>
        </svg>
    </div>
@endif
