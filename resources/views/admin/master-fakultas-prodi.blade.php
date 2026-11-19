{{--
    PBI-03 (UC-04): Kelola Master Data Fakultas dan Program Studi
    Role: Administrator (Syalfa Oktaviana Wijaya) — Branch: Syalfa
    Desain Figma: Tailwind CSS Responsif, Inter Typography, Maroon Theme (#800000)
--}}
<!DOCTYPE html>
<html lang="id" class="h-full bg-[#F7F9FA]">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Master Fakultas &amp; Prodi — {{ config('app.name', 'Telyuji') }} Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Asset CSS Telyuji --}}
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">

    <style>
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }
        .sidebar-item { position: relative; }
        .sidebar-link {
            display: flex; align-items: center; gap: 12px;
            padding: 0 16px; height: 46px; border-radius: 6px;
            font-size: 13px; font-weight: 500; color: #4B5563;
            transition: all .15s;
        }
        .sidebar-link:hover { background: #F9FAFB; color: #111827; }
        .sidebar-link.active {
            background: #F9EEEE; color: #800000; font-weight: 600;
        }
        .sidebar-link.active::before {
            content: ''; position: absolute; left: 0; width: 3px; height: 26px;
            background: #800000; border-radius: 0 2px 2px 0;
        }
    </style>
</head>
<body class="h-full bg-[#F7F9FA] text-[#111827]">

<div class="flex min-h-screen">

    {{-- ========================================================
         SIDEBAR KIRI (w-64 bg-white border-r border-gray-200)
         ======================================================== --}}
    <aside class="hidden lg:flex flex-col w-64 bg-white border-r border-gray-200 flex-shrink-0 justify-between">
        <div>
            {{-- Brand Logo --}}
            <div class="flex items-center gap-3 px-6 pt-6 pb-5 border-b border-gray-200">
                <div class="w-10 h-10 rounded-lg bg-[#800000] flex items-center justify-center flex-shrink-0 shadow-sm">
                    <span class="text-white text-sm font-bold tracking-wider">TU</span>
                </div>
                <div>
                    <p class="text-[#800000] font-bold text-lg leading-6 tracking-tight">TELYUJI</p>
                    <p class="text-[#6B7280] text-xs leading-4">Teliti dan Teruji</p>
                </div>
            </div>

            {{-- Navigasi Manajemen Sistem --}}
            <div class="px-3 pt-6 space-y-1">
                <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-widest px-3 mb-2">MANAJEMEN SISTEM</p>

                <div class="sidebar-item">
                    <a href="{{ route('home') }}" class="sidebar-link">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                        <span>Dashboard Statistik</span>
                    </a>
                </div>

                {{-- Active State: Master Fakultas & Prodi --}}
                <div class="sidebar-item">
                    <a href="{{ route('admin.master.fakultas-prodi.index') }}" class="sidebar-link active">
                        <svg class="w-4 h-4 text-[#800000]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <span>Master Fakultas &amp; Prodi</span>
                    </a>
                </div>

                @if(Route::has('admin.users.index'))
                <div class="sidebar-item">
                    <a href="{{ route('admin.users.index') }}" class="sidebar-link">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span>Manajemen Pengguna</span>
                    </a>
                </div>
                @endif

                <div class="sidebar-item">
                    <a href="#" class="sidebar-link text-gray-400 cursor-not-allowed">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                        </svg>
                        <span>Katalog Mesin &amp; Kuota SLA</span>
                    </a>
                </div>

                <div class="sidebar-item">
                    <a href="#" class="sidebar-link text-gray-400 cursor-not-allowed">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Log Audit Trail</span>
                    </a>
                </div>

                <div class="sidebar-item">
                    <a href="#" class="sidebar-link text-gray-400 cursor-not-allowed">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Laporan &amp; Ekspor</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Bawah: Bantuan & Profile Admin --}}
        <div class="p-4 space-y-4">
            {{-- Kartu Bantuan --}}
            <div class="p-4 bg-[#F9FAFB] rounded-lg border border-gray-100">
                <p class="text-xs font-semibold text-gray-700">Butuh bantuan?</p>
                <p class="text-[11px] text-gray-500 mt-0.5">Panduan pengelolaan master data</p>
                <a href="#panduan" onclick="alert('Panduan: Tambahkan data Fakultas terlebih dahulu sebelum membuat Program Studi. Fakultas atau Prodi yang sudah terhubung dengan data pemohon tidak dapat dihapus demi integritas data.'); return false;"
                   class="text-xs font-semibold text-[#800000] hover:underline mt-2 inline-flex items-center gap-1">
                    Buka panduan ↗
                </a>
            </div>

            {{-- Info Admin & Logout --}}
            <div class="border-t border-gray-200 pt-4">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 rounded-lg bg-[#F9EEEE] flex items-center justify-center flex-shrink-0 text-[#800000]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold text-gray-900 truncate">{{ Auth::user()->name ?? 'Administrator Telyuji' }}</p>
                        <p class="text-[10px] text-gray-500 truncate">Admin Lab &middot; Superuser</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full h-9 rounded text-xs font-semibold text-gray-700 border border-gray-300 bg-white hover:bg-gray-50 transition-colors text-center">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ========================================================
         KONTEN UTAMA KANAN
         ======================================================== --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- Top Bar (Breadcrumb & Portal Admin Badge) --}}
        <header class="bg-white border-b border-gray-200 flex items-center justify-between px-6 lg:px-10 h-20 flex-shrink-0">
            <div class="flex items-center gap-2">
                {{-- Mobile toggle --}}
                <button type="button" id="sidebarToggle" class="lg:hidden mr-2 text-gray-500 hover:text-[#800000]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                <nav class="flex items-center gap-1.5 text-xs text-gray-500" aria-label="Breadcrumb">
                    <span>Master Data</span>
                    <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span class="font-semibold text-gray-800">Master Fakultas &amp; Prodi</span>
                </nav>
            </div>

            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md bg-[#F8F9FA] border border-gray-200 text-xs font-medium text-gray-700">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                <span>Portal Admin</span>
            </div>
        </header>

        {{-- Scrollable Container --}}
        <main class="flex-1 overflow-y-auto px-6 lg:px-10 py-8">

            {{-- Page Header & Counter Pills --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 leading-tight">Kelola Master Data Fakultas &amp; Program Studi</h1>
                    <p class="text-gray-500 text-sm mt-1">Atur daftar unit fakultas dan program studi sebagai referensi dinamis pada formulir pengajuan &amp; profil.</p>
                </div>

                {{-- Counter Pills dari Figma --}}
                <div class="flex items-center gap-2.5 flex-wrap">
                    <span dusk="pill-total-fakultas" class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-semibold bg-[#F9EEEE] text-[#800000] border border-red-200 shadow-xs">
                        Total Fakultas: {{ $totalFakultas }}
                    </span>
                    <span dusk="pill-total-prodi" class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-800 border border-gray-200 shadow-xs">
                        Total Prodi: {{ $totalProdi }}
                    </span>
                </div>
            </div>

            {{-- Flash Alert Sukses / Error --}}
            @if(session('status'))
                <div class="mb-6 flex items-center gap-3 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-lg text-sm text-emerald-800" role="alert">
                    <svg class="w-4 h-4 flex-shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 px-4 py-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-800 space-y-1" role="alert">
                    @foreach($errors->all() as $err)
                        <p class="flex items-center gap-2">
                            <span class="text-red-600 font-bold">&bull;</span>
                            <span>{{ $err }}</span>
                        </p>
                    @endforeach
                </div>
            @endif

            {{-- ========================================================
                 CARD 1: MASTER DATA FAKULTAS
                 ======================================================== --}}
            <div class="bg-white rounded-xl border border-gray-300 p-6 shadow-xs">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-gray-200">
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 leading-6">Master Data Fakultas</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Unit fakultas induk di lingkungan kampus.</p>
                    </div>

                    {{-- Toolbar: Search & Tombol Tambah Fakultas --}}
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <form method="GET" action="{{ route('admin.master.fakultas-prodi.index') }}" class="relative sm:w-64">
                            <input type="hidden" name="search_prodi" value="{{ $searchProdi }}">
                            <input type="hidden" name="filter_fakultas_id" value="{{ $filterFakultas }}">
                            <svg class="pointer-events-none absolute left-3 top-2.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z"/>
                            </svg>
                            <input type="search" name="search_fakultas" dusk="input-search-fakultas" value="{{ $searchFakultas }}"
                                   placeholder="Cari nama atau kode fakultas..."
                                   class="w-full h-9 pl-9 pr-3 rounded-lg border border-gray-300 text-xs text-gray-900 placeholder-gray-400 focus:outline-none focus:border-[#800000]">
                        </form>

                        <button type="button" dusk="btn-tambah-fakultas" onclick="openModal('modalTambahFakultas')"
                                class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-lg bg-[#800000] hover:bg-[#660000] text-white text-xs font-semibold shadow-xs transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>+ Tambah Fakultas</span>
                        </button>
                    </div>
                </div>

                {{-- Tabel Fakultas --}}
                <div class="overflow-x-auto mt-4">
                    <table class="w-full text-left text-xs" dusk="table-fakultas">
                        <thead>
                            <tr class="border-b border-gray-200 text-[11px] font-semibold text-gray-500 uppercase tracking-wider bg-gray-50/50">
                                <th scope="col" class="py-3 px-4 w-12 text-center">No</th>
                                <th scope="col" class="py-3 px-4 w-32">Kode Fakultas</th>
                                <th scope="col" class="py-3 px-4">Nama Fakultas</th>
                                <th scope="col" class="py-3 px-4 w-36 text-center">Jumlah Prodi</th>
                                <th scope="col" class="py-3 px-4 w-32 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($fakultas as $idx => $f)
                                <tr dusk="row-fakultas-{{ $f->id }}" class="hover:bg-gray-50/60 transition-colors">
                                    <td class="py-3.5 px-4 text-center text-gray-500">{{ $fakultas->firstItem() + $idx }}</td>
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded text-[11px] font-bold bg-[#F9EEEE] text-[#800000]">
                                            {{ $f->kode_fakultas }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 font-semibold text-gray-900">{{ $f->nama_fakultas }}</td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-gray-100 text-gray-700">
                                            {{ $f->prodi_count }} Prodi
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="inline-flex items-center gap-1.5 justify-end">
                                            <button type="button"
                                                    dusk="btn-edit-fakultas-{{ $f->id }}"
                                                    onclick="editFakultas('{{ $f->id }}', '{{ addslashes($f->kode_fakultas) }}', '{{ addslashes($f->nama_fakultas) }}')"
                                                    class="px-2.5 py-1 rounded border border-gray-300 bg-white text-gray-700 hover:text-[#800000] hover:border-[#800000] text-xs font-medium transition-colors">
                                                Edit
                                            </button>
                                            <form action="{{ route('admin.fakultas.destroy', $f->id) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus Fakultas {{ addslashes($f->nama_fakultas) }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" dusk="btn-hapus-fakultas-{{ $f->id }}" class="px-2.5 py-1 rounded border border-red-200 bg-white text-red-600 hover:bg-red-50 text-xs font-medium transition-colors">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-500">
                                        Tidak ada data fakultas ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($fakultas->hasPages())
                    <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                        {{ $fakultas->links() }}
                    </div>
                @endif
            </div>

            {{-- ========================================================
                 CARD 2: MASTER DATA PROGRAM STUDI
                 ======================================================== --}}
            <div class="bg-white rounded-xl border border-gray-300 p-6 shadow-xs mt-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-gray-200">
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 leading-6">Master Data Program Studi</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Program studi yang bernaung di bawah masing-masing fakultas.</p>
                    </div>

                    {{-- Toolbar: Filter Fakultas, Search & Tombol Tambah Prodi --}}
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        {{-- Dropdown Filter Fakultas --}}
                        <form method="GET" action="{{ route('admin.master.fakultas-prodi.index') }}" class="flex items-center gap-2">
                            <input type="hidden" name="search_fakultas" value="{{ $searchFakultas }}">
                            <select name="filter_fakultas_id" dusk="select-filter-fakultas" onchange="this.form.submit()"
                                    class="h-9 px-3 rounded-lg border border-gray-300 text-xs text-gray-900 bg-white focus:outline-none focus:border-[#800000]">
                                <option value="">Semua Fakultas</option>
                                @foreach($allFakultas as $af)
                                    <option value="{{ $af->id }}" {{ (string)$filterFakultas === (string)$af->id ? 'selected' : '' }}>
                                        {{ $af->kode_fakultas }} &middot; {{ $af->nama_fakultas }}
                                    </option>
                                @endforeach
                            </select>

                            <div class="relative sm:w-56">
                                <svg class="pointer-events-none absolute left-3 top-2.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z"/>
                                </svg>
                                <input type="search" name="search_prodi" dusk="input-search-prodi" value="{{ $searchProdi }}"
                                       placeholder="Cari kode atau nama prodi..."
                                       class="w-full h-9 pl-9 pr-3 rounded-lg border border-gray-300 text-xs text-gray-900 placeholder-gray-400 focus:outline-none focus:border-[#800000]">
                            </div>
                        </form>

                        <button type="button" dusk="btn-tambah-prodi" onclick="openModal('modalTambahProdi')"
                                class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-lg bg-[#800000] hover:bg-[#660000] text-white text-xs font-semibold shadow-xs transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>+ Tambah Prodi</span>
                        </button>
                    </div>
                </div>

                {{-- Tabel Program Studi --}}
                <div class="overflow-x-auto mt-4">
                    <table class="w-full text-left text-xs" dusk="table-prodi">
                        <thead>
                            <tr class="border-b border-gray-200 text-[11px] font-semibold text-gray-500 uppercase tracking-wider bg-gray-50/50">
                                <th scope="col" class="py-3 px-4 w-12 text-center">No</th>
                                <th scope="col" class="py-3 px-4 w-28">Kode Prodi</th>
                                <th scope="col" class="py-3 px-4">Nama Program Studi</th>
                                <th scope="col" class="py-3 px-4">Fakultas Induk</th>
                                <th scope="col" class="py-3 px-4 w-32 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($prodi as $idx => $p)
                                <tr dusk="row-prodi-{{ $p->id }}" class="hover:bg-gray-50/60 transition-colors">
                                    <td class="py-3.5 px-4 text-center text-gray-500">{{ $prodi->firstItem() + $idx }}</td>
                                    <td class="py-3.5 px-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-[#F3F4F6] text-[#4B5563]">
                                            {{ $p->kode_prodi ?: '-' }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 font-semibold text-gray-900">{{ $p->nama_prodi }}</td>
                                    <td class="py-3.5 px-4 text-gray-600">
                                        <span class="inline-flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#800000]"></span>
                                            <span>{{ $p->fakultas->nama_fakultas ?? '-' }}</span>
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="inline-flex items-center gap-1.5 justify-end">
                                            <button type="button"
                                                    dusk="btn-edit-prodi-{{ $p->id }}"
                                                    onclick="editProdi('{{ $p->id }}', '{{ $p->fakultas_id }}', '{{ addslashes($p->kode_prodi) }}', '{{ addslashes($p->nama_prodi) }}')"
                                                    class="px-2.5 py-1 rounded border border-gray-300 bg-white text-gray-700 hover:text-[#800000] hover:border-[#800000] text-xs font-medium transition-colors">
                                                Edit
                                            </button>
                                            <form action="{{ route('admin.prodi.destroy', $p->id) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus Program Studi {{ addslashes($p->nama_prodi) }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" dusk="btn-hapus-prodi-{{ $p->id }}" class="px-2.5 py-1 rounded border border-red-200 bg-white text-red-600 hover:bg-red-50 text-xs font-medium transition-colors">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-gray-500">
                                        Tidak ada data program studi ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($prodi->hasPages())
                    <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                        {{ $prodi->links() }}
                    </div>
                @endif
            </div>

            {{-- Footer Telyuji Admin --}}
            <footer class="mt-12 text-center text-[11px] text-gray-500 pb-8">
                Telyuji Admin &middot; Master data institusi
            </footer>

        </main>
    </div>
</div>

{{-- ========================================================
     4 MODAL INTERAKTIF (TAMBAH & EDIT FAKULTAS / PRODI)
     ======================================================== --}}

{{-- 1. Modal Tambah Fakultas --}}
<div id="modalTambahFakultas" dusk="modal-tambah-fakultas" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-sm font-bold text-gray-900">Tambah Fakultas</h3>
            <button type="button" onclick="closeModal('modalTambahFakultas')" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        <form action="{{ route('admin.fakultas.store') }}" method="POST">
            @csrf
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kode Fakultas <span class="text-red-600">*</span></label>
                    <input type="text" name="kode_fakultas" dusk="input-tambah-kode-fakultas" required placeholder="Contoh: FRI / FIF / FTE"
                           class="w-full h-10 px-3 border border-gray-300 rounded-lg text-xs uppercase focus:outline-none focus:border-[#800000]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Fakultas <span class="text-red-600">*</span></label>
                    <input type="text" name="nama_fakultas" dusk="input-tambah-nama-fakultas" required placeholder="Contoh: Fakultas Rekayasa Industri (FRI)"
                           class="w-full h-10 px-3 border border-gray-300 rounded-lg text-xs focus:outline-none focus:border-[#800000]">
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modalTambahFakultas')" class="px-4 py-2 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-100">Batal</button>
                <button type="submit" dusk="btn-submit-tambah-fakultas" class="px-4 py-2 bg-[#800000] text-white rounded-lg text-xs font-semibold hover:bg-[#660000]">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

{{-- 2. Modal Edit Fakultas --}}
<div id="modalEditFakultas" dusk="modal-edit-fakultas" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-sm font-bold text-gray-900">Ubah Fakultas</h3>
            <button type="button" onclick="closeModal('modalEditFakultas')" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        <form id="formEditFakultas" action="" method="POST">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kode Fakultas <span class="text-red-600">*</span></label>
                    <input type="text" id="edit_kode_fakultas" name="kode_fakultas" dusk="input-edit-kode-fakultas" required
                           class="w-full h-10 px-3 border border-gray-300 rounded-lg text-xs uppercase focus:outline-none focus:border-[#800000]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Fakultas <span class="text-red-600">*</span></label>
                    <input type="text" id="edit_nama_fakultas" name="nama_fakultas" dusk="input-edit-nama-fakultas" required
                           class="w-full h-10 px-3 border border-gray-300 rounded-lg text-xs focus:outline-none focus:border-[#800000]">
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modalEditFakultas')" class="px-4 py-2 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-100">Batal</button>
                <button type="submit" dusk="btn-submit-edit-fakultas" class="px-4 py-2 bg-[#800000] text-white rounded-lg text-xs font-semibold hover:bg-[#660000]">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

{{-- 3. Modal Tambah Prodi --}}
<div id="modalTambahProdi" dusk="modal-tambah-prodi" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-sm font-bold text-gray-900">Tambah Program Studi</h3>
            <button type="button" onclick="closeModal('modalTambahProdi')" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        <form action="{{ route('admin.prodi.store') }}" method="POST">
            @csrf
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Fakultas Induk <span class="text-red-600">*</span></label>
                    <select name="fakultas_id" dusk="select-tambah-prodi-fakultas" required class="w-full h-10 px-3 border border-gray-300 rounded-lg text-xs bg-white focus:outline-none focus:border-[#800000]">
                        <option value="">-- Pilih Fakultas Induk --</option>
                        @foreach($allFakultas as $af)
                            <option value="{{ $af->id }}">{{ $af->kode_fakultas }} &middot; {{ $af->nama_fakultas }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kode Prodi</label>
                    <input type="text" name="kode_prodi" dusk="input-tambah-kode-prodi" placeholder="Contoh: IF / SI / TI"
                           class="w-full h-10 px-3 border border-gray-300 rounded-lg text-xs uppercase focus:outline-none focus:border-[#800000]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Program Studi <span class="text-red-600">*</span></label>
                    <input type="text" name="nama_prodi" dusk="input-tambah-nama-prodi" required placeholder="Contoh: S1 Sistem Informasi"
                           class="w-full h-10 px-3 border border-gray-300 rounded-lg text-xs focus:outline-none focus:border-[#800000]">
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modalTambahProdi')" class="px-4 py-2 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-100">Batal</button>
                <button type="submit" dusk="btn-submit-tambah-prodi" class="px-4 py-2 bg-[#800000] text-white rounded-lg text-xs font-semibold hover:bg-[#660000]">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

{{-- 4. Modal Edit Prodi --}}
<div id="modalEditProdi" dusk="modal-edit-prodi" class="fixed inset-0 z-50 hidden bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 class="text-sm font-bold text-gray-900">Ubah Program Studi</h3>
            <button type="button" onclick="closeModal('modalEditProdi')" class="text-gray-400 hover:text-gray-600">✕</button>
        </div>
        <form id="formEditProdi" action="" method="POST">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Fakultas Induk <span class="text-red-600">*</span></label>
                    <select id="edit_prodi_fakultas_id" name="fakultas_id" dusk="select-edit-prodi-fakultas" required class="w-full h-10 px-3 border border-gray-300 rounded-lg text-xs bg-white focus:outline-none focus:border-[#800000]">
                        @foreach($allFakultas as $af)
                            <option value="{{ $af->id }}">{{ $af->kode_fakultas }} &middot; {{ $af->nama_fakultas }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kode Prodi</label>
                    <input type="text" id="edit_kode_prodi" name="kode_prodi" dusk="input-edit-kode-prodi"
                           class="w-full h-10 px-3 border border-gray-300 rounded-lg text-xs uppercase focus:outline-none focus:border-[#800000]">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Program Studi <span class="text-red-600">*</span></label>
                    <input type="text" id="edit_nama_prodi" name="nama_prodi" dusk="input-edit-nama-prodi" required
                           class="w-full h-10 px-3 border border-gray-300 rounded-lg text-xs focus:outline-none focus:border-[#800000]">
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal('modalEditProdi')" class="px-4 py-2 border border-gray-300 rounded-lg text-xs font-medium text-gray-700 hover:bg-gray-100">Batal</button>
                <button type="submit" dusk="btn-submit-edit-prodi" class="px-4 py-2 bg-[#800000] text-white rounded-lg text-xs font-semibold hover:bg-[#660000]">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function editFakultas(id, kode, nama) {
        const form = document.getElementById('formEditFakultas');
        form.action = '/admin/fakultas/' + id;
        document.getElementById('edit_kode_fakultas').value = kode;
        document.getElementById('edit_nama_fakultas').value = nama;
        openModal('modalEditFakultas');
    }

    function editProdi(id, fakultasId, kode, nama) {
        const form = document.getElementById('formEditProdi');
        form.action = '/admin/prodi/' + id;
        document.getElementById('edit_prodi_fakultas_id').value = fakultasId;
        document.getElementById('edit_kode_prodi').value = kode;
        document.getElementById('edit_nama_prodi').value = nama;
        openModal('modalEditProdi');
    }

    // Close on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('[id^="modal"]').forEach(el => el.classList.add('hidden'));
        }
    });

    // Mobile sidebar toggle
    const toggleBtn = document.getElementById('sidebarToggle');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            const aside = document.querySelector('aside');
            aside.classList.toggle('hidden');
            aside.classList.toggle('flex');
        });
    }
</script>

</body>
</html>
