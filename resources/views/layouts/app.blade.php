<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Telyuji') }} - Sistem Pengujian Laboratorium</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="h-full flex flex-col font-sans text-gray-900 antialiased selection:bg-racing-red selection:text-white">
    <div id="app" class="min-h-full flex flex-col">
        <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <!-- Brand / Logo -->
                    <div class="flex items-center gap-3">
                        <a href="{{ url('/') }}" class="flex items-center gap-2.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-racing-red rounded-md">
                            <div class="flex flex-col">
                                <span class="font-bold text-lg tracking-tight text-gray-900 leading-tight">
                                    Telyuji
                                </span>
                                <span class="text-xs font-medium text-gray-500 tracking-normal">
                                    Teliti dan Teruji
                                </span>
                            </div>
                        </a>
                    </div>

                    <!-- Navigation Links -->
                    <nav class="flex items-center gap-2 sm:gap-4">
                        @guest
                            @if (Route::has('login'))
                                <a href="{{ route('login') }}" class="px-3.5 py-1.5 text-sm font-medium text-gray-700 hover:text-racing-red rounded-md transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-racing-red">
                                    {{ __('Login') }}
                                </a>
                            @endif

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-racing-red hover:bg-tiger-flame rounded-md shadow-sm transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-racing-red">
                                    {{ __('Daftar') }}
                                </a>
                            @endif
                        @else
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-medium text-gray-700">
                                    {{ Auth::user()->name }}
                                </span>
                                <span class="px-2 py-0.5 text-xs font-semibold rounded bg-cream text-gray-800 uppercase tracking-wider">
                                    {{ Auth::user()->role ?? 'pemohon' }}
                                </span>
                                <a href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                                   class="px-3 py-1.5 text-xs font-medium text-gray-600 hover:text-racing-red border border-gray-300 hover:border-racing-red rounded transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-racing-red">
                                    {{ __('Logout') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                                    @csrf
                                </form>
                            </div>
                        @endguest
                    </nav>
                </div>
            </div>
        </header>

        <main class="flex-1 w-full">
            @yield('content')
        </main>

        <footer class="bg-white border-t border-gray-200 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 flex items-center justify-center text-center text-xs text-gray-500">
                <p>&copy; {{ date('Y') }} Telyuji — Sistem Informasi Pengujian Laboratorium Telkom University.</p>
            </div>
        </footer>
    </div>
</body>
</html>
