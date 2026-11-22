@extends('layouts.app')

@section('content')
@php
    $isValidated = $user->isValidasiKtm();
    $isRejected = $user->isKtmDitolak();
@endphp
<div class="relative overflow-hidden" id="profil-root">
    <x-bg-pattern uid="profil-pat" />
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 space-y-8">
        <!-- Breadcrumb & Header -->
        <div class="space-y-3">
            <nav class="flex items-center gap-2 text-xs text-gray-500" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="transition-colors hover:text-racing-red">Dashboard</a>
                <span aria-hidden="true">&rarr;</span>
                <span class="font-semibold text-gray-900">Profil &amp; Validasi KTM</span>
            </nav>

            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                <div class="max-w-2xl">
                    <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-gray-950 leading-[1.05]">
                        Profil Akademik &amp; Validasi Identitas
                    </h1>
                    <p class="mt-4 text-sm sm:text-base leading-relaxed text-gray-600">
                        Kelola data akademik dan unggah kartu identitas (KTM / ID) Anda sebagai identitas resmi layanan pengujian laboratorium Telkom University.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3 shrink-0">
                    <a href="{{ route('home') }}" class="tu-btn tu-btn--ghost tu-btn--sm">
                        <span>&larr; Dashboard</span>
                    </a>
                    @if (Route::has('pengajuan.create'))
                        <a href="{{ route('pengajuan.create') }}" class="tu-btn tu-btn--sm">
                            <span>+ Buat Pengajuan</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        @if (session('status'))
            <div class="tu-alert" dusk="alert-status" role="alert">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any() && !$errors->has('foto_identitas') && !$errors->has('nama_lengkap') && !$errors->has('nomor_induk') && !$errors->has('fakultas_id') && !$errors->has('prodi_id'))
            <div class="tu-alert tu-alert--danger" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Grid: Profil Akademik (kiri) + Status Validasi & Unggah KTM (kanan) -->
        <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start">
            <!-- Kartu Data Profil Akademik -->
            <section class="xl:col-span-7 bg-white border border-gray-200 rounded-lg overflow-hidden" aria-labelledby="profil-form-heading">
                <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-200">
                    <span class="inline-grid place-items-center w-8 h-8 rounded-[3px] bg-racing-red text-white text-xs font-bold shrink-0">01</span>
                    <div>
                        <h2 id="profil-form-heading" class="text-base font-bold tracking-tight text-gray-950">Data Profil Akademik</h2>
                        <p class="text-xs text-gray-500 mt-0.5">Pastikan data sesuai dengan identitas resmi pada KTM / kartu pegawai Anda.</p>
                    </div>
                </div>

                <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data" id="form-profil" class="p-6 space-y-5" novalidate>
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="nama_lengkap" class="tu-label">
                            Nama Lengkap <span class="text-racing-red">*</span>
                        </label>
                        <input
                            type="text"
                            id="nama_lengkap"
                            name="nama_lengkap"
                            dusk="input-nama-lengkap"
                            class="tu-input @error('nama_lengkap') is-invalid @enderror"
                            value="{{ old('nama_lengkap', $user->nama_lengkap ?? $user->name) }}"
                            placeholder="Masukkan nama lengkap sesuai KTM"
                            maxlength="150"
                            required
                            autocomplete="name"
                        >
                        @error('nama_lengkap')
                            <p class="tu-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nomor_induk" class="tu-label">
                            Nomor Induk (NIM / NIDN / NIP) <span class="text-racing-red">*</span>
                        </label>
                        <input
                            type="text"
                            id="nomor_induk"
                            name="nomor_induk"
                            dusk="input-nomor-induk"
                            class="tu-input tu-mono @error('nomor_induk') is-invalid @enderror"
                            value="{{ old('nomor_induk', $user->nomor_induk) }}"
                            placeholder="Contoh: 102022400228"
                            maxlength="50"
                            required
                            autocomplete="off"
                        >
                        @error('nomor_induk')
                            <p class="tu-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email_institusi" class="tu-label">Email Institusi</label>
                        <input
                            type="email"
                            id="email_institusi"
                            dusk="input-email-institusi"
                            class="tu-input opacity-75 cursor-not-allowed bg-gray-100"
                            value="{{ $user->email }}"
                            disabled
                            readonly
                            aria-describedby="email-profile-hint"
                        >
                        <p id="email-profile-hint" class="text-xs text-gray-500 mt-1.5">
                            Email institusi terhubung dengan akun dan tidak dapat diubah.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="fakultas_id" class="tu-label">
                                Pilih Fakultas <span class="text-racing-red">*</span>
                            </label>
                            <select
                                id="fakultas_id"
                                name="fakultas_id"
                                dusk="select-fakultas"
                                class="tu-input @error('fakultas_id') is-invalid @enderror"
                                required
                                onchange="updateProdiOptions()"
                            >
                                <option value="">-- Pilih Fakultas --</option>
                                @foreach ($fakultasProdiMap as $fak => $prodis)
                                    <option value="{{ $fak }}" {{ old('fakultas_id', $user->fakultas_id) === $fak ? 'selected' : '' }}>
                                        {{ $fak }}
                                    </option>
                                @endforeach
                            </select>
                            @error('fakultas_id')
                                <p class="tu-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="prodi_id" class="tu-label">
                                Program Studi <span class="text-racing-red">*</span>
                            </label>
                            <select
                                id="prodi_id"
                                name="prodi_id"
                                dusk="select-prodi"
                                class="tu-input @error('prodi_id') is-invalid @enderror"
                                required
                            >
                                <option value="">-- Pilih Program Studi --</option>
                            </select>
                            @error('prodi_id')
                                <p class="tu-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                        <a href="{{ route('home') }}" class="tu-btn tu-btn--ghost tu-btn--sm">Batal</a>
                        <button type="submit" dusk="btn-submit-profil" class="tu-btn tu-btn--sm">
                            <span>Simpan Perubahan Profil</span>
                            <span class="arr" aria-hidden="true">&rarr;</span>
                        </button>
                    </div>
                </form>
            </section>

            <!-- Kolom Kanan: Status Validasi + Unggah KTM -->
            <div class="xl:col-span-5 space-y-6">
                <!-- Kartu Status Validasi KTM -->
                <section class="bg-white border border-gray-200 rounded-lg p-6 space-y-3" aria-labelledby="status-ktm-heading">
                    <h2 id="status-ktm-heading" class="text-sm font-bold text-gray-950">Status Validasi KTM</h2>

                    <div class="flex flex-wrap items-center gap-2.5">
                        <div dusk="pill-status-validasi">
                            @if ($isValidated)
                                <span class="tu-chip tu-chip--mint">Tervalidasi</span>
                            @elseif ($isRejected)
                                <span class="tu-chip bg-red-100 text-red-800 border-red-200">Ditolak</span>
                            @else
                                <span class="tu-chip tu-chip--jasmine">Belum Tervalidasi</span>
                            @endif
                        </div>
                        @if ($isValidated)
                            <p class="text-xs text-gray-600">Identitas Anda telah diverifikasi oleh Laboran.</p>
                        @elseif ($isRejected)
                            <p class="text-xs text-red-700 font-medium">Validasi KTM ditolak. Silakan unggah ulang foto KTM yang valid.</p>
                        @else
                            <p class="text-xs text-gray-600">Menunggu pemeriksaan oleh Laboran.</p>
                        @endif
                    </div>

                    @if (!$isValidated && !$isRejected)
                        <p class="text-xs text-gray-500 leading-relaxed pt-1 border-t border-gray-100">
                            Lengkapi data profil dan unggah foto KTM Anda, lalu Laboran akan memvalidasi identitas dalam 1&times;24 jam kerja.
                        </p>
                    @elseif ($isRejected)
                        <p class="text-xs text-red-600 leading-relaxed pt-1 border-t border-gray-100">
                            Pastikan nama dan NIM pada berkas KTM terbaca jelas dan sesuai dengan profil akademik Anda.
                        </p>
                    @endif
                </section>

                <!-- Kartu Unggah Identitas KTM -->
                <section class="bg-white border border-gray-200 rounded-lg overflow-hidden" aria-labelledby="ktm-upload-heading">
                    <div class="flex items-center gap-3 px-6 py-5 border-b border-gray-200">
                        <span class="inline-grid place-items-center w-8 h-8 rounded-[3px] bg-tiger-flame text-white text-xs font-bold shrink-0">02</span>
                        <div>
                            <h2 id="ktm-upload-heading" class="text-base font-bold tracking-tight text-gray-950">Unggah Identitas KTM / ID</h2>
                            <p class="text-xs text-gray-500 mt-0.5">JPG, PNG, atau PDF &middot; Maks. 2 MB</p>
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        @if ($user->foto_identitas)
                            <div id="berkas-preview" dusk="berkas-preview" class="rounded-[3px] border border-gray-200 bg-gray-50 p-4">
                                <p class="tu-eyebrow !text-[10px] uppercase tracking-wider text-gray-500 mb-3">Berkas Terunggah</p>
                                <div class="flex items-center gap-3.5">
                                    @php
                                        $ext = strtolower(pathinfo($user->foto_identitas, PATHINFO_EXTENSION));
                                    @endphp
                                    @if (in_array($ext, ['jpg', 'jpeg', 'png']))
                                        <img src="{{ route('profil.identitas.preview') }}"
                                             onerror="if(!this.dataset.fallback){this.dataset.fallback='1';this.src='{{ asset('storage/' . ltrim($user->foto_identitas, '/')) }}';}else{this.onerror=null;this.classList.add('hidden');}"
                                             alt="Foto Identitas KTM"
                                             class="w-20 h-20 object-cover rounded-[3px] border border-gray-200 shrink-0">
                                    @else
                                        <div class="w-20 h-20 rounded-[3px] border border-gray-200 bg-red-50 flex items-center justify-center shrink-0">
                                            <span class="lu-badge lu-badge--pdf !w-12 !h-14">PDF</span>
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-gray-900 truncate">{{ basename($user->foto_identitas) }}</p>
                                        <p class="tu-mono text-[11px] text-gray-500 uppercase mt-0.5">{{ strtoupper($ext) }}</p>
                                        <div class="flex flex-wrap items-center gap-2 mt-3">
                                            <a href="{{ route('profil.identitas.preview') }}"
                                               target="_blank"
                                               rel="noopener noreferrer"
                                               dusk="btn-lihat-berkas"
                                               class="tu-link text-xs font-semibold text-racing-red">
                                                Lihat Berkas &rarr;
                                            </a>
                                            <button type="button"
                                                    onclick="triggerFileInput()"
                                                    dusk="btn-ganti-berkas"
                                                    class="tu-btn tu-btn--ghost tu-btn--sm !py-1 !px-2.5 text-xs">
                                                Ganti Berkas
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div id="dropzone"
                                 dusk="dropzone-ktm"
                                 class="lu-drop cursor-pointer"
                                 onclick="triggerFileInput()">
                                <span class="tu-bracket tu-bracket--tl" aria-hidden="true"></span>
                                <span class="tu-bracket tu-bracket--tr" aria-hidden="true"></span>
                                <span class="tu-bracket tu-bracket--bl" aria-hidden="true"></span>
                                <span class="tu-bracket tu-bracket--br" aria-hidden="true"></span>
                                <svg class="w-9 h-9 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-sm font-semibold text-gray-900">Klik untuk unggah KTM / ID</p>
                                <p class="text-xs text-gray-500 mt-1">JPG, PNG, atau PDF &middot; Maks. 2 MB</p>
                            </div>
                        @endif

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

                        <div id="new-file-preview" class="hidden rounded-[3px] border border-emerald-200 bg-emerald-50 p-4">
                            <p class="tu-eyebrow !text-[10px] uppercase tracking-wider text-emerald-800 mb-2">Berkas Siap Diunggah</p>
                            <div class="flex items-center gap-3">
                                <p id="new-file-name" class="text-xs font-semibold text-gray-900 truncate flex-1"></p>
                                <button type="button" onclick="clearFileInput()" class="text-xs font-semibold text-gray-500 hover:text-racing-red">
                                    Batal
                                </button>
                            </div>
                        </div>

                        @error('foto_identitas')
                            <p class="tu-error">{{ $message }}</p>
                        @enderror
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const prodiByFakultas = @json($fakultasProdiMap);
    const oldProdi = @json(old('prodi_id', $user->prodi_id ?? ''));

    document.addEventListener('DOMContentLoaded', function () {
        updateProdiOptions();
    });

    function updateProdiOptions() {
        const fakSelect = document.getElementById('fakultas_id');
        const prodiSelect = document.getElementById('prodi_id');
        const selectedFak = fakSelect ? fakSelect.value : '';

        prodiSelect.innerHTML = '<option value="">-- Pilih Program Studi --</option>';

        if (selectedFak && prodiByFakultas[selectedFak]) {
            prodiByFakultas[selectedFak].forEach(function (prodi) {
                const opt = document.createElement('option');
                opt.value = prodi;
                opt.textContent = prodi;
                if (prodi === oldProdi) {
                    opt.selected = true;
                }
                prodiSelect.appendChild(opt);
            });
        }
    }

    function triggerFileInput() {
        document.getElementById('foto_identitas_input').click();
    }

    function handleFileSelect(input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        const maxBytes = 2 * 1024 * 1024;
        if (file.size > maxBytes) {
            alert('Ukuran berkas melebihi batas 2 MB. Silakan pilih berkas yang lebih kecil.');
            clearFileInput();
            return;
        }
        document.getElementById('new-file-name').textContent = file.name;
        document.getElementById('new-file-preview').classList.remove('hidden');

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
</script>
@endpush
