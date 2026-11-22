{{--
    PBI-01 (UC-01): Profil Akademik & Validasi Identitas KTM
    Pemohon (Syalfa Oktaviana) — Branch: Syalfa
    Layout: Sidebar Pemohon + Konten utama (responsif, Tailwind, Inter/General Sans)
--}}
<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profil & Validasi KTM — {{ config('app.name', 'Telyuji') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Tailwind dari asset build --}}
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">

    <style>
        body { font-family: 'Inter', 'General Sans', sans-serif; }
        .pv-sidebar-link {
            display: flex; align-items: center; gap: 10px;
            padding: 0 14px; height: 48px; border-radius: 6px;
            font-size: 12px; font-weight: 500; color: #4B5563;
            transition: background .15s;
        }
        .pv-sidebar-link:hover { background: #F9FAFB; }
        .pv-sidebar-link.active {
            background: #FEF2F2; color: #7F1D1D; font-weight: 600;
        }
        .pv-sidebar-link.active::before {
            content: '';
            position: absolute; left: 0; width: 3px; height: 24px;
            background: #7F1D1D; border-radius: 2px;
        }
        .pv-sidebar-item { position: relative; }
        .pv-input {
            width: 100%; height: 48px; padding: 0 14px;
            border-radius: 6px; border: 1px solid #D1D5DB;
            font-size: 14px; color: #111827; background: #fff;
            outline: none; transition: border-color .15s, box-shadow .15s;
        }
        .pv-input:focus { border-color: #7F1D1D; box-shadow: 0 0 0 3px rgba(127,29,29,.1); }
        .pv-input.is-invalid { border-color: #EF4444; }
        .pv-input:disabled, .pv-input[readonly] {
            background: #F3F4F6; color: #6B7280; cursor: not-allowed;
        }
        .pv-label { display: block; font-size: 12px; font-weight: 500; color: #374151; margin-bottom: 6px; }
        .pv-error { font-size: 11px; color: #DC2626; margin-top: 4px; }
        .pv-card { background: #fff; border-radius: 12px; border: 1px solid #E5E7EB; overflow: hidden; }
        .pv-btn-primary {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            padding: 10px 20px; border-radius: 6px; font-size: 13px; font-weight: 600;
            background: #7F1D1D; color: #fff; border: none; cursor: pointer;
            transition: background .15s;
        }
        .pv-btn-primary:hover { background: #991B1B; }
        .pv-btn-secondary {
            display: inline-flex; align-items: center; justify-content: center; gap: 6px;
            padding: 10px 20px; border-radius: 6px; font-size: 13px; font-weight: 600;
            background: #fff; color: #374151; border: 1px solid #D1D5DB; cursor: pointer;
            transition: background .15s;
        }
        .pv-btn-secondary:hover { background: #F9FAFB; }
        .pv-pill-validated {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 4px 12px; border-radius: 999px;
            font-size: 12px; font-weight: 600;
        }
        .pv-pill-validated.validated { background: #ECFDF5; color: #15803D; }
        .pv-pill-validated.not-validated { background: #FEF9C3; color: #A16207; }
        .pv-pill-validated.rejected { background: #FEF2F2; color: #DC2626; }
        .pv-topbar-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 4px 14px; border-radius: 6px; background: #F9FAFB;
            font-size: 12px; font-weight: 500; color: #4B5563;
        }
    </style>
</head>
<body class="h-full" style="background:#F9FAFB">

{{-- ============================================================
     WRAPPER UTAMA: Sidebar (w-64) + Konten (flex-1)
     ============================================================ --}}
<div class="flex min-h-screen">

    {{-- ===== SIDEBAR PEMOHON ===== --}}
    <aside class="hidden lg:flex flex-col w-64 bg-white border-r border-gray-200 flex-shrink-0">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-6 pt-7 pb-5 border-b border-gray-200">
            <div class="w-10 h-10 rounded-lg overflow-hidden bg-red-900 flex items-center justify-center">
                <span class="text-white text-xs font-bold">TU</span>
            </div>
            <div>
                <p class="text-red-900 font-bold text-base leading-5">TELYUJI</p>
                <p class="text-gray-500 text-[10px] leading-4">Teliti dan Teruji</p>
            </div>
        </div>

        {{-- Navigasi Ruang Akademik --}}
        <div class="flex-1 px-4 pt-5 overflow-y-auto">
            <p class="text-gray-500 text-[10px] font-semibold uppercase tracking-widest px-2 mb-2">Ruang Akademik</p>

            <div class="pv-sidebar-item">
                <a href="{{ route('home') }}" class="pv-sidebar-link {{ Route::currentRouteName() === 'home' ? 'active' : '' }}">
                    {{-- ikon beranda --}}
                    <svg class="w-4 h-4 flex-shrink-0 {{ Route::currentRouteName() === 'home' ? 'text-red-900' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 9.75L12 3l9 6.75V21H15v-5h-6v5H3V9.75z"/>
                    </svg>
                    <span>Beranda &amp; Linimasa</span>
                </a>
            </div>

            <div class="pv-sidebar-item mt-1">
                <a href="{{ route('profil.show') }}" class="pv-sidebar-link active">
                    {{-- ikon profil --}}
                    <svg class="w-4 h-4 flex-shrink-0 text-red-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Profil &amp; Validasi KTM</span>
                </a>
            </div>

            @if(Route::has('pengajuan.create'))
            <div class="pv-sidebar-item mt-1">
                <a href="{{ route('pengajuan.create') }}" class="pv-sidebar-link">
                    <svg class="w-4 h-4 flex-shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Buat Pengajuan</span>
                </a>
            </div>
            @endif

            <div class="pv-sidebar-item mt-1">
                <a href="{{ route('home') }}" class="pv-sidebar-link">
                    <svg class="w-4 h-4 flex-shrink-0 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                    </svg>
                    <span>Arsip Hasil Uji</span>
                </a>
            </div>
        </div>

        {{-- Butuh Bantuan --}}
        <div class="mx-5 mb-4 p-4 bg-gray-50 rounded-lg">
            <p class="text-gray-700 text-xs font-semibold leading-5">Butuh bantuan?</p>
            <p class="text-gray-500 text-xs leading-4 mt-0.5">Hubungi pengelola laboratorium</p>
            <a href="mailto:lab@telkomuniversity.ac.id" class="text-red-900 text-xs font-semibold leading-4 mt-1 inline-block hover:underline">
                Pusat bantuan ↗
            </a>
        </div>

        {{-- Divider + user info + logout --}}
        <div class="border-t border-gray-200 px-5 pt-4 pb-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-9 bg-red-50 rounded-lg flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-red-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-gray-900 text-xs font-semibold leading-4">{{ Auth::user()->name }}</p>
                    <p class="text-gray-500 text-[10px] leading-4">
                        Mahasiswa
                        @if(Auth::user()->nomor_induk) · {{ Auth::user()->nomor_induk }} @endif
                    </p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full h-10 px-4 rounded text-xs font-semibold text-gray-900 border border-gray-300 bg-white hover:bg-gray-50 transition-colors text-center">
                    Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- ===== KONTEN UTAMA ===== --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- ---- TOP BAR (breadcrumb + badge) ---- --}}
        <header class="bg-white border-b border-gray-200 flex items-center justify-between px-6 lg:px-10 h-20 flex-shrink-0">
            {{-- Mobile menu toggle --}}
            <div class="flex items-center gap-2">
                <button type="button" id="sidebarToggle" class="lg:hidden mr-2 text-gray-500 hover:text-red-900">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                {{-- Breadcrumb --}}
                <nav class="flex items-center gap-1 text-xs" aria-label="Breadcrumb">
                    <span class="text-gray-500 font-normal">Akun saya</span>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M9 5l7 7-7 7"/>
                    </svg>
                    <span class="text-gray-700 font-medium">Profil &amp; Validasi KTM</span>
                </nav>
            </div>

            {{-- Badge Portal Mahasiswa --}}
            <div class="pv-topbar-badge">
                <svg class="w-4 h-4 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>Portal Mahasiswa</span>
            </div>
        </header>

        {{-- ---- SCROLLABLE BODY ---- --}}
        <main class="flex-1 overflow-y-auto px-5 lg:px-10 py-8">

            {{-- Page heading + status validasi --}}
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 leading-10">Profil Akademik &amp; Validasi Identitas</h1>
                    <p class="text-gray-500 text-sm leading-5 mt-1">Kelola data akademik dan identitas Anda untuk layanan pengujian laboratorium.</p>
                </div>

                {{-- Pill status validasi --}}
                @php
                    $isValidated = $user->isValidasiKtm();
                    $isRejected = $user->isKtmDitolak();
                @endphp
                <div class="flex-shrink-0 self-start sm:self-center" dusk="pill-status-validasi">
                    @if($isValidated)
                        <span class="pv-pill-validated validated">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Tervalidasi
                        </span>
                    @elseif($isRejected)
                        <span class="pv-pill-validated rejected">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Ditolak
                        </span>
                    @else
                        <span class="pv-pill-validated not-validated">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            Belum Tervalidasi
                        </span>
                    @endif
                </div>
            </div>

            {{-- Flash success --}}
            @if(session('status'))
                <div class="mb-6 flex items-center gap-3 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-lg text-sm text-emerald-800" dusk="alert-status" role="alert">
                    <svg class="w-4 h-4 flex-shrink-0 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            {{-- Validasi error global --}}
            @if($errors->any() && !$errors->has('foto_identitas') && !$errors->has('nama_lengkap') && !$errors->has('nomor_induk') && !$errors->has('fakultas_id') && !$errors->has('prodi_id'))
                <div class="mb-6 px-4 py-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-800" role="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- ==============================
                 GRID: Kartu Profil (kiri) + Kartu KTM (kanan)
                 ============================== --}}
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 items-start">

                {{-- ========== KARTU DATA PROFIL AKADEMIK ========== --}}
                <div class="pv-card">
                    {{-- Card header --}}
                    <div class="flex items-center gap-3 px-6 pt-6 pb-4 border-b border-gray-200">
                        <div class="w-9 h-9 bg-red-50 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-red-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-gray-900 text-base font-semibold leading-6">Data Profil Akademik</p>
                            <p class="text-gray-500 text-xs leading-4">Pastikan data sesuai dengan identitas resmi Anda.</p>
                        </div>
                    </div>

                    {{-- Form --}}
                    <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data" id="form-profil" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="px-6 pb-6 pt-5 space-y-5">

                            {{-- Nama Lengkap --}}
                            <div>
                                <label for="nama_lengkap" class="pv-label">
                                    Nama Lengkap <span class="text-red-600">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="nama_lengkap"
                                    name="nama_lengkap"
                                    dusk="input-nama-lengkap"
                                    class="pv-input {{ $errors->has('nama_lengkap') ? 'is-invalid' : '' }}"
                                    value="{{ old('nama_lengkap', $user->nama_lengkap ?? $user->name) }}"
                                    placeholder="Masukkan nama lengkap sesuai KTM"
                                    maxlength="150"
                                    required
                                    autocomplete="name"
                                >
                                @error('nama_lengkap')
                                    <p class="pv-error">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Nomor Induk --}}
                            <div>
                                <label for="nomor_induk" class="pv-label">
                                    Nomor Induk (NIM / NIDN / NIP) <span class="text-red-600">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="nomor_induk"
                                    name="nomor_induk"
                                    dusk="input-nomor-induk"
                                    class="pv-input {{ $errors->has('nomor_induk') ? 'is-invalid' : '' }}"
                                    value="{{ old('nomor_induk', $user->nomor_induk) }}"
                                    placeholder="Contoh: 102022400228"
                                    maxlength="50"
                                    required
                                    autocomplete="off"
                                >
                                @error('nomor_induk')
                                    <p class="pv-error">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Email Institusi (readonly) --}}
                            <div>
                                <label for="email_institusi" class="pv-label">Email Institusi</label>
                                <div class="relative">
                                    <input
                                        type="email"
                                        id="email_institusi"
                                        dusk="input-email-institusi"
                                        class="pv-input pr-10"
                                        value="{{ $user->email }}"
                                        disabled
                                        readonly
                                        aria-describedby="email-hint"
                                    >
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </span>
                                </div>
                                <p id="email-hint" class="text-gray-500 text-xs leading-4 mt-1">
                                    Email institusi terhubung dengan akun dan tidak dapat diubah.
                                </p>
                            </div>

                            {{-- Pilih Fakultas --}}
                            <div>
                                <label for="fakultas_id" class="pv-label">
                                    Pilih Fakultas <span class="text-red-600">*</span>
                                </label>
                                <select
                                    id="fakultas_id"
                                    name="fakultas_id"
                                    dusk="select-fakultas"
                                    class="pv-input {{ $errors->has('fakultas_id') ? 'is-invalid' : '' }}"
                                    required
                                    onchange="updateProdiOptions()"
                                >
                                    <option value="">-- Pilih Fakultas --</option>
                                    @foreach($fakultasProdiMap as $fak => $prodis)
                                        <option value="{{ $fak }}" {{ old('fakultas_id', $user->fakultas_id) === $fak ? 'selected' : '' }}>
                                            {{ $fak }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('fakultas_id')
                                    <p class="pv-error">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Pilih Program Studi --}}
                            <div>
                                <label for="prodi_id" class="pv-label">
                                    Program Studi <span class="text-red-600">*</span>
                                </label>
                                <select
                                    id="prodi_id"
                                    name="prodi_id"
                                    dusk="select-prodi"
                                    class="pv-input {{ $errors->has('prodi_id') ? 'is-invalid' : '' }}"
                                    required
                                >
                                    <option value="">-- Pilih Program Studi --</option>
                                </select>
                                @error('prodi_id')
                                    <p class="pv-error">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Tombol submit --}}
                            <div class="flex items-center justify-end gap-3 pt-2">
                                <a href="{{ route('home') }}" class="pv-btn-secondary">Batal</a>
                                <button type="submit" dusk="btn-submit-profil" class="pv-btn-primary">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Simpan Perubahan Profil
                                </button>
                            </div>

                        </div>
                    </form>
                </div>

                {{-- ========== KARTU VALIDASI KTM / IDENTITAS ========== --}}
                <div class="flex flex-col gap-6">

                    {{-- Kartu Unggah KTM --}}
                    <div class="pv-card">
                        <div class="flex items-center gap-3 px-6 pt-6 pb-4 border-b border-gray-200">
                            <div class="w-9 h-9 bg-red-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-red-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-gray-900 text-base font-semibold leading-6">Unggah Identitas KTM / ID</p>
                                <p class="text-gray-500 text-xs leading-4">JPG, PNG, atau PDF &middot; Maks. 2 MB</p>
                            </div>
                        </div>

                        <div class="px-6 py-5 space-y-4">
                            {{-- Pratinjau berkas yang sudah ada --}}
                            @if($user->foto_identitas)
                                <div id="berkas-preview" dusk="berkas-preview" class="rounded-lg border border-gray-200 bg-gray-50 p-4">
                                    <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-widest mb-3">Berkas Terunggah</p>
                                    <div class="flex items-center gap-3">
                                        @php
                                            $ext = strtolower(pathinfo($user->foto_identitas, PATHINFO_EXTENSION));
                                        @endphp
                                        @if(in_array($ext, ['jpg', 'jpeg', 'png']))
                                            <img src="{{ route('profil.identitas.preview') }}"
                                                 onerror="if(!this.dataset.fallback){this.dataset.fallback='1';this.src='{{ asset('storage/' . ltrim($user->foto_identitas, '/')) }}';}else{this.onerror=null;this.classList.add('hidden');}"
                                                 alt="Foto Identitas KTM"
                                                 class="w-20 h-20 object-cover rounded border border-gray-200 flex-shrink-0">
                                        @else
                                            {{-- PDF preview icon --}}
                                            <div class="w-20 h-20 rounded border border-gray-200 bg-red-50 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-10 h-10 text-red-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-semibold text-gray-700 truncate">
                                                {{ basename($user->foto_identitas) }}
                                            </p>
                                            <p class="text-[11px] text-gray-500 uppercase mt-0.5">{{ strtoupper($ext) }}</p>
                                            <div class="flex gap-2 mt-2 flex-wrap">
                                                <a href="{{ route('profil.identitas.preview') }}"
                                                   target="_blank"
                                                   dusk="btn-lihat-berkas"
                                                   class="text-xs font-semibold text-red-900 hover:underline">
                                                    Lihat ↗
                                                </a>
                                                <button type="button" onclick="triggerFileInput()"
                                                    dusk="btn-ganti-berkas"
                                                    class="text-xs font-semibold text-gray-600 hover:text-gray-900 border border-gray-300 px-2 py-0.5 rounded hover:bg-gray-50">
                                                    Ganti Berkas
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                {{-- Area drop zone --}}
                                <div id="dropzone"
                                     dusk="dropzone-ktm"
                                     class="rounded-lg border-2 border-dashed border-gray-300 bg-gray-50 p-6 text-center cursor-pointer hover:border-red-900 hover:bg-red-50 transition-colors"
                                     onclick="triggerFileInput()">
                                    <svg class="w-10 h-10 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="text-sm font-medium text-gray-700">Klik untuk unggah KTM / ID</p>
                                    <p class="text-xs text-gray-500 mt-1">JPG, PNG, atau PDF &middot; Maks. 2 MB</p>
                                </div>
                            @endif

                            {{-- Input file tersembunyi (selalu ada agar bisa ganti berkas) --}}
                            <input
                                type="file"
                                id="foto_identitas_input"
                                name="foto_identitas"
                                dusk="input-foto-identitas"
                                accept=".jpg,.jpeg,.png,.pdf"
                                class="sr-only"
                                form="form-profil"
                                onchange="handleFileSelect(this)"
                            >

                            {{-- Preview setelah pilih file baru --}}
                            <div id="new-file-preview" class="hidden rounded-lg border border-gray-200 bg-gray-50 p-4">
                                <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-widest mb-2">Berkas Dipilih</p>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-emerald-50 rounded flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <p id="new-file-name" class="text-xs font-semibold text-gray-700 truncate flex-1"></p>
                                    <button type="button" onclick="clearFileInput()"
                                        class="text-xs text-gray-500 hover:text-red-600 ml-2 flex-shrink-0">✕</button>
                                </div>
                            </div>

                            {{-- Error unggah --}}
                            @error('foto_identitas')
                                <p class="pv-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Kartu Info Status Validasi --}}
                    <div class="pv-card px-6 py-5">
                        <p class="text-sm font-semibold text-gray-900 mb-1">Status Validasi KTM</p>
                        <div class="flex items-center gap-2 mt-2">
                            @if($isValidated)
                                <span class="pv-pill-validated validated">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Tervalidasi
                                </span>
                                <p class="text-xs text-gray-500">Identitas Anda telah diverifikasi oleh Laboran.</p>
                            @elseif($isRejected)
                                <span class="pv-pill-validated rejected">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Ditolak
                                </span>
                                <p class="text-xs text-red-700">Validasi KTM ditolak. Silakan unggah ulang foto KTM yang valid.</p>
                            @else
                                <span class="pv-pill-validated not-validated">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    Belum Tervalidasi
                                </span>
                                <p class="text-xs text-gray-500">Menunggu pemeriksaan oleh Laboran.</p>
                            @endif
                        </div>
                        @if(!$isValidated && !$isRejected)
                            <p class="text-xs text-gray-500 mt-3 leading-5">
                                Lengkapi data profil dan unggah foto KTM Anda, lalu Laboran akan memvalidasi identitas dalam 1&times;24 jam kerja.
                            </p>
                        @elseif($isRejected)
                            <p class="text-xs text-red-600 mt-3 leading-5">
                                Pastikan nama dan NIM pada berkas KTM terbaca jelas dan sesuai dengan profil akademik Anda.
                            </p>
                        @endif
                    </div>

                </div>
                {{-- end kolom kanan --}}

            </div>
            {{-- end grid --}}

        </main>
        {{-- end main --}}

    </div>
    {{-- end konten --}}

</div>
{{-- end wrapper --}}

{{-- ============================================================
     JAVASCRIPT: Dependent Dropdown Fakultas → Prodi & File Preview
     ============================================================ --}}
<script>
    // ---- Data master Fakultas → Prodi (server-side rendered) ----
    const prodiByFakultas = @json($fakultasProdiMap);
    const oldProdi        = @json(old('prodi_id', $user->prodi_id ?? ''));

    // Inisialisasi dropdown Prodi saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function () {
        updateProdiOptions();
    });

    function updateProdiOptions() {
        const fakSelect   = document.getElementById('fakultas_id');
        const prodiSelect = document.getElementById('prodi_id');
        const selectedFak = fakSelect ? fakSelect.value : '';

        prodiSelect.innerHTML = '<option value="">-- Pilih Program Studi --</option>';

        if (selectedFak && prodiByFakultas[selectedFak]) {
            prodiByFakultas[selectedFak].forEach(function (prodi) {
                const opt         = document.createElement('option');
                opt.value         = prodi;
                opt.textContent   = prodi;
                if (prodi === oldProdi) {
                    opt.selected = true;
                }
                prodiSelect.appendChild(opt);
            });
        }
    }

    // ---- File input helpers ----
    function triggerFileInput() {
        document.getElementById('foto_identitas_input').click();
    }

    function handleFileSelect(input) {
        if (!input.files || !input.files[0]) return;
        const file     = input.files[0];
        const maxBytes = 2 * 1024 * 1024; // 2 MB
        if (file.size > maxBytes) {
            alert('Ukuran berkas melebihi batas 2 MB. Silakan pilih berkas yang lebih kecil.');
            clearFileInput();
            return;
        }
        document.getElementById('new-file-name').textContent = file.name;
        document.getElementById('new-file-preview').classList.remove('hidden');

        // Sembunyikan dropzone/area berkas lama saat berkas baru dipilih
        const dropzone = document.getElementById('dropzone');
        if (dropzone) dropzone.classList.add('hidden');
    }

    function clearFileInput() {
        const input = document.getElementById('foto_identitas_input');
        input.value = '';
        document.getElementById('new-file-preview').classList.add('hidden');
        const dropzone = document.getElementById('dropzone');
        if (dropzone) dropzone.classList.remove('hidden');
    }

    // ---- Mobile sidebar toggle (opsional) ----
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
