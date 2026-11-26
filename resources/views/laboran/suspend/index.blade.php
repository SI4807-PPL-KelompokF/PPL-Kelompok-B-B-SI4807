<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F7F9FA]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Manajemen Pelanggaran &amp; Penangguhan (Suspend) - Telyuji</title>

    <!-- Inter Font & Tailwind via App.css -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <script src="{{ mix('js/app.js') }}" defer></script>

    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #F7F9FA;
        }
    </style>
</head>
<body class="h-full text-gray-900 antialiased flex">

    <!-- Sidebar Kiri -->
    <aside class="w-64 bg-white border-r border-gray-200 flex flex-col justify-between shrink-0 min-h-screen">
        <div>
            <!-- Brand -->
            <div class="p-6 border-b border-gray-100">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <span class="text-2xl font-extrabold tracking-tight text-[#800000]">TELYUJI</span>
                </a>
                <p class="text-xs text-gray-500 mt-0.5">Teliti dan Teruji</p>
            </div>

            <!-- Navigation Links -->
            <div class="px-4 py-6">
                <p class="text-[10px] font-semibold text-gray-500 tracking-wider uppercase mb-3 px-3">OPERASIONAL LAB</p>
                <nav class="space-y-1 text-sm font-medium">
                    <a href="{{ Route::has('laboran.antrean.index') ? route('laboran.antrean.index') : '#' }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <span>Antrean Aktif</span>
                    </a>
                    <a href="{{ Route::has('laboran.sop.index') ? route('laboran.sop.index') : '#' }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-colors">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Kelola SOP Lab</span>
                    </a>
                    <!-- Active State: Kelola Sanksi / Suspend -->
                    <a href="{{ route('laboran.suspend.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-[#F9EEEE] text-[#800000] font-semibold border-l-[3px] border-[#800000] transition-colors">
                        <svg class="w-5 h-5 text-[#800000]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <span>Kelola Sanksi / Suspend</span>
                    </a>
                    @if (Route::has('laboran.validasi.ktm.index'))
                        <a href="{{ route('laboran.validasi.ktm.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-colors">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                            </svg>
                            <span>Validasi KTM</span>
                        </a>
                    @endif
                    @if (Route::has('laboran.pengujian.index'))
                        <a href="{{ route('laboran.pengujian.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-colors">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <span>Unggah Hasil Uji</span>
                        </a>
                    @endif
                </nav>
            </div>
        </div>

        <!-- Sidebar Bottom: Bantuan & Profil User -->
        <div class="p-4 space-y-4">
            <!-- Kartu Bantuan -->
            <div class="bg-[#F8F9FA] rounded-lg p-3.5 border border-gray-200">
                <p class="text-xs font-semibold text-gray-900">Butuh bantuan?</p>
                <p class="text-[11px] text-gray-500 mt-1">Panduan tata tertib &amp; sanksi lab</p>
                <a href="#panduan" class="inline-flex items-center gap-1 text-[11px] font-semibold text-[#800000] mt-2 hover:underline">
                    <span>Buka panduan ↗</span>
                </a>
            </div>

            <!-- Profil Laboran Login -->
            <div class="pt-3 border-t border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-9 h-9 rounded-full bg-[#F9EEEE] flex items-center justify-center text-[#800000] font-semibold text-sm shrink-0">
                        {{ strtoupper(substr(Auth::user()->name ?? 'Raka', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-gray-900 truncate">{{ Auth::user()->name ?? 'Raka Pratama, S.T.' }}</p>
                        <p class="text-[10px] text-gray-500 truncate">Laboran &middot; Operator Lab</p>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="shrink-0">
                    @csrf
                    <button type="submit" title="Logout" class="text-xs text-gray-400 hover:text-red-600 p-1 rounded transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-h-screen overflow-y-auto">
        <!-- Top Bar -->
        <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between sticky top-0 z-10">
            <div class="flex items-center gap-2 text-xs text-gray-500">
                <span>Operasional Lab</span>
                <span class="text-gray-300">&gt;</span>
                <span class="font-medium text-gray-900">Kelola Sanksi / Suspend Pemohon</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#F8F9FA] border border-gray-200 text-xs font-medium text-gray-700">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Portal Laboran</span>
                </span>
            </div>
        </header>

        <!-- Body Container -->
        <main class="p-8 max-w-7xl w-full mx-auto space-y-6">
            <!-- Alert Messages -->
            @if (session('success'))
                <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800 flex items-center justify-between" role="alert">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">
                    <p class="font-semibold">Terjadi kesalahan validasi:</p>
                    <ul class="list-disc pl-5 mt-1 space-y-0.5 text-xs">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Page Header + Counters -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900">Manajemen Pelanggaran &amp; Penangguhan (Suspend) Pemohon</h1>
                    <p class="mt-1 text-sm text-gray-500">Catat pelanggaran aturan laboratorium (seperti no-show/ghosting 3x24 jam) dan tangguhkan sementara akses pengajuan Pemohon.</p>
                </div>
                <!-- Dynamic Counters -->
                <div class="flex items-center gap-3 shrink-0">
                    <div class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-xs font-semibold text-gray-600 shadow-sm flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Pemohon Aktif: <strong class="text-gray-900 font-bold ml-1">{{ $totalAktif }}</strong></span>
                    </div>
                    <div class="px-4 py-2 bg-[#F9EEEE] border border-[#D7B6B6] rounded-lg text-xs font-semibold text-[#800000] shadow-sm flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-[#800000]"></span>
                        <span>Akun Ditangguhkan: <strong class="text-[#800000] font-bold ml-1">{{ $totalDitangguhkan }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- Card Utama -->
            <div class="bg-white rounded-xl border border-gray-300 shadow-sm p-6 space-y-6">
                <!-- Card Header -->
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-[#F9EEEE] flex items-center justify-center text-[#800000] shrink-0">
                        <svg class="w-5 h-5 text-[#800000]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Daftar Akun Pemohon &amp; Status Akses</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Kelola penangguhan (suspend) dan pencabutan sanksi akun Pemohon.</p>
                    </div>
                </div>

                <!-- Toolbar Filter & Search -->
                <form method="GET" action="{{ route('laboran.suspend.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
                    <div class="w-full sm:w-48 shrink-0">
                        <select name="status" onchange="this.form.submit()" class="w-full text-xs rounded-lg border-gray-300 focus:border-[#800000] focus:ring-[#800000] bg-white py-2 px-3 text-gray-700">
                            <option value="semua" {{ $statusFilter === 'semua' ? 'selected' : '' }}>Semua Status</option>
                            <option value="aktif" {{ $statusFilter === 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="ditangguhkan" {{ $statusFilter === 'ditangguhkan' ? 'selected' : '' }}>Ditangguhkan</option>
                        </select>
                    </div>
                    <div class="relative w-full">
                        <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z"/>
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, NIM/NIDN, atau email pemohon..." class="w-full text-xs pl-9 pr-3 py-2 rounded-lg border-gray-300 focus:border-[#800000] focus:ring-[#800000] text-gray-800 placeholder-gray-400">
                    </div>
                    <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-[#800000] text-white text-xs font-semibold rounded-lg hover:bg-[#660000] transition-colors shrink-0">
                        Cari
                    </button>
                    @if ($search || $statusFilter !== 'semua')
                        <a href="{{ route('laboran.suspend.index') }}" class="w-full sm:w-auto px-3 py-2 border border-gray-300 text-gray-600 hover:text-gray-900 text-xs font-medium rounded-lg hover:bg-gray-50 transition-colors shrink-0 text-center">
                            Reset
                        </a>
                    @endif
                </form>

                <!-- Semantic Table -->
                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 border-b border-gray-200 text-gray-500 font-semibold uppercase tracking-wider text-[11px]">
                            <tr>
                                <th scope="col" class="px-4 py-3.5 w-12 text-center">No</th>
                                <th scope="col" class="px-4 py-3.5">Pemohon &amp; NIM</th>
                                <th scope="col" class="px-4 py-3.5 w-32">Status Akun</th>
                                <th scope="col" class="px-4 py-3.5">Alasan Pelanggaran / Suspend</th>
                                <th scope="col" class="px-4 py-3.5 w-44 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse ($pemohonList as $p)
                                <tr class="hover:bg-gray-50/70 transition-colors">
                                    <td class="px-4 py-3.5 text-center text-gray-500 font-medium align-top">
                                        {{ $pemohonList->firstItem() + $loop->index }}
                                    </td>
                                    <td class="px-4 py-3.5 align-top">
                                        <p class="font-bold text-sm text-[#111827]">{{ $p->nama_lengkap ?: $p->name }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            {{ $p->nomor_induk ?: 'NIM: -' }} &middot; <span class="text-gray-400">{{ $p->email }}</span>
                                        </p>
                                        <p class="text-[11px] text-gray-400 mt-0.5">
                                            {{ $p->fakultas->nama_fakultas ?? $p->fakultas_id ?? '-' }} &middot; {{ $p->prodi->nama_prodi ?? $p->prodi_id ?? '-' }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-3.5 align-top whitespace-nowrap">
                                        @if ($p->is_suspended)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#FEE2E2] text-[#B91C1C]">
                                                Ditangguhkan
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-semibold bg-[#EAF7EF] text-[#15803D]">
                                                Aktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 align-top">
                                        @if ($p->is_suspended && !empty($p->alasan_suspend))
                                            <p class="text-xs text-gray-800 leading-relaxed font-medium bg-red-50/60 p-2 rounded border border-red-100">
                                                {{ $p->alasan_suspend }}
                                            </p>
                                        @else
                                            <span class="text-gray-400">–</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 align-top text-right whitespace-nowrap">
                                        @if ($p->is_suspended)
                                            <!-- Form Cabut Suspend -->
                                            <form method="POST" action="{{ route('laboran.suspend.unsuspend', $p->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin mencabut penangguhan akun {{ $p->nama_lengkap ?: $p->name }}?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-[#B7DCC2] text-[#15803D] hover:bg-green-50 font-semibold text-xs transition-colors shadow-sm">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    <span>Cabut Suspend</span>
                                                </button>
                                            </form>
                                        @else
                                            <!-- Button Buka Modal Suspend -->
                                            <button type="button" 
                                                    onclick="openSuspendModal('{{ $p->id }}', '{{ addslashes($p->nama_lengkap ?: $p->name) }}', '{{ addslashes($p->nomor_induk ?: '-') }}')"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-[#D7B6B6] text-[#800000] hover:bg-[#F9EEEE] font-semibold text-xs transition-colors shadow-sm">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                </svg>
                                                <span>Tangguhkan (Suspend)</span>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                        Tidak ada data akun pemohon yang cocok dengan filter atau kata kunci pencarian.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Footer Pagination -->
                <div class="pt-2 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-xs text-gray-500">
                        Menampilkan {{ $pemohonList->firstItem() ?? 0 }} - {{ $pemohonList->lastItem() ?? 0 }} dari {{ $pemohonList->total() }} pemohon
                    </p>
                    <div>
                        {{ $pemohonList->links() }}
                    </div>
                </div>
            </div>

            <!-- Footer Text -->
            <footer class="text-center pt-4 pb-8">
                <p class="text-[11px] text-gray-500">Telyuji Laboran &middot; Manajemen akses pemohon</p>
            </footer>
        </main>
    </div>

    <!-- Modal Dialog — Tangguhkan (Suspend) Akun Pemohon -->
    <div id="modal-tangguhkan" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeSuspendModal()"></div>

        <!-- Modal Dialog Box -->
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-200">
                <!-- Header Modal -->
                <div class="p-6 border-b border-gray-100 flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900" id="modal-title">Tangguhkan (Suspend) Akun Pemohon</h3>
                        <p class="text-xs text-gray-500 mt-1">Akses pengajuan akan dinonaktifkan sementara.</p>
                    </div>
                    <button type="button" onclick="closeSuspendModal()" class="text-gray-400 hover:text-gray-600 text-2xl font-light leading-none p-1">
                        &times;
                    </button>
                </div>

                <!-- Form Suspend -->
                <form id="form-suspend" method="POST" action="" class="p-6 space-y-4">
                    @csrf
                    <!-- Kotak info Pemohon Terpilih -->
                    <div class="bg-[#F9EEEE] rounded-lg p-3 border border-[#D7B6B6]">
                        <p class="text-[10px] font-bold text-[#800000] uppercase tracking-wider">PEMOHON TERPILIH</p>
                        <p id="modal-pemohon-info" class="text-sm font-bold text-gray-900 mt-0.5">Nama Pemohon &middot; NIM</p>
                    </div>

                    <!-- Input 1: Kategori Pelanggaran -->
                    <div>
                        <label for="kategori_pelanggaran" class="block text-xs font-semibold text-gray-700 mb-1">Kategori Pelanggaran</label>
                        <select id="kategori_pelanggaran" name="kategori_pelanggaran" class="w-full text-xs rounded-lg border-gray-300 focus:border-[#800000] focus:ring-[#800000] py-2 px-3 text-gray-800">
                            <option value="No-Show / Ghosting (Melewati batas 3x24 jam)">No-Show / Ghosting (Melewati batas 3x24 jam)</option>
                            <option value="Sampel Berbahaya / Melanggar SOP">Sampel Berbahaya / Melanggar SOP</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                        <p class="text-[10px] text-gray-400 mt-1">Opsi lain: Sampel Berbahaya / Melanggar SOP &middot; Lainnya</p>
                    </div>

                    <!-- Input 2: Alasan Suspend -->
                    <div>
                        <label for="alasan_suspend" class="block text-xs font-semibold text-gray-700 mb-1">Catatan Alasan Penangguhan (Wajib)</label>
                        <textarea id="alasan_suspend" name="alasan_suspend" rows="3" required placeholder="Tuliskan detail pelanggaran yang dilakukan pemohon..." class="w-full text-xs rounded-lg border-gray-300 focus:border-[#800000] focus:ring-[#800000] p-3 text-gray-800 placeholder-gray-400"></textarea>
                    </div>

                    <!-- Tombol Footer -->
                    <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                        <button type="button" onclick="closeSuspendModal()" class="px-4 py-2 border border-gray-300 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-[#800000] text-white text-xs font-semibold rounded-lg hover:bg-[#660000] transition-colors shadow-sm">
                            Konfirmasi Suspend
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script Kontrol Modal -->
    <script>
        function openSuspendModal(userId, nama, nim) {
            var form = document.getElementById('form-suspend');
            var info = document.getElementById('modal-pemohon-info');
            var modal = document.getElementById('modal-tangguhkan');

            form.action = "{{ url('/laboran/suspend') }}/" + userId;
            info.textContent = nama + " · " + nim;
            document.getElementById('alasan_suspend').value = '';

            modal.classList.remove('hidden');
        }

        function closeSuspendModal() {
            var modal = document.getElementById('modal-tangguhkan');
            modal.classList.add('hidden');
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeSuspendModal();
            }
        });
    </script>
</body>
</html>
