@extends('layouts.app')

@section('content')
<div class="relative overflow-hidden bg-gray-50 min-h-screen py-8 sm:py-12">
    <x-bg-pattern uid="pengajuan-pat" />

    <div class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <!-- Breadcrumb & Header -->
        <div class="tu-reveal space-y-3">
            <nav class="flex items-center gap-2 text-xs text-gray-500" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="transition-colors hover:text-racing-red">Dashboard</a>
                <span>&rarr;</span>
                <span class="font-semibold text-gray-900">Formulir Pengajuan Uji Lab</span>
            </nav>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-gray-950">
                        Formulir Pengajuan Pengujian Sampel
                    </h1>
                    <p class="mt-1 text-sm sm:text-base text-gray-600">
                        Daftarkan spesimen fisik atau berkas data digital Anda ke laboratorium Telkom University.
                    </p>
                </div>
                <a href="{{ route('home') }}" class="tu-btn tu-btn--ghost tu-btn--sm self-start sm:self-auto">
                    <span>&larr; Kembali ke Dashboard</span>
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="border-l-4 border-racing-red bg-red-50 p-4 rounded-r-md text-sm text-red-800" role="alert" dusk="alert-error-pengajuan">
                <div class="flex items-center gap-2 font-bold mb-1">
                    <svg class="w-5 h-5 text-racing-red shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <span>Terdapat beberapa isian yang perlu diperbaiki:</span>
                </div>
                <ul class="list-disc list-inside space-y-1 text-xs text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('pengajuan.store') }}" method="POST" id="formPengajuanUji" class="space-y-8" novalidate>
            @csrf

            <!-- Section 1: Identitas Akademik Pemohon -->
            <section class="bg-white border border-gray-200 rounded-lg p-6 sm:p-8 space-y-6 shadow-sm tu-reveal">
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <div class="flex items-center gap-3">
                        <span class="inline-grid place-items-center w-7 h-7 rounded-full bg-racing-red text-white text-xs font-bold">1</span>
                        <h2 class="text-base sm:text-lg font-bold text-gray-950">Identitas Akademik Pemohon</h2>
                    </div>
                    <span class="tu-chip tu-chip--cream">Sivitas Akademika</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label for="nama_pemohon" class="tu-label">Nama Lengkap Pemohon <span class="text-racing-red">*</span></label>
                        <input type="text" id="nama_pemohon" name="nama_pemohon" dusk="input-nama-pemohon"
                               value="{{ old('nama_pemohon', $user->name ?? '') }}" required
                               class="tu-input @error('nama_pemohon') is-invalid @enderror"
                               placeholder="Contoh: Giri Pulung Nugroho">
                        @error('nama_pemohon')
                            <p class="tu-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nim_nip" class="tu-label">Nomor Induk (NIM / NIP / NIDN) <span class="text-racing-red">*</span></label>
                        <input type="text" id="nim_nip" name="nim_nip" dusk="input-nim-nip"
                               value="{{ old('nim_nip', $user->nim_nip ?? '102022400284') }}" required
                               class="tu-input @error('nim_nip') is-invalid @enderror"
                               placeholder="Contoh: 102022400284">
                        @error('nim_nip')
                            <p class="tu-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="fakultas" class="tu-label">Fakultas Asal <span class="text-racing-red">*</span></label>
                        <select id="fakultas" name="fakultas" dusk="select-fakultas" required
                                class="tu-input @error('fakultas') is-invalid @enderror" onchange="updateProdiOptions()">
                            <option value="">-- Pilih Fakultas --</option>
                            @foreach ($fakultasProdiMap as $fak => $prodis)
                                <option value="{{ $fak }}" {{ old('fakultas', 'Fakultas Rekayasa Industri (FRI)') === $fak ? 'selected' : '' }}>
                                    {{ $fak }}
                                </option>
                            @endforeach
                        </select>
                        @error('fakultas')
                            <p class="tu-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="prodi" class="tu-label">Program Studi <span class="text-racing-red">*</span></label>
                        <select id="prodi" name="prodi" dusk="select-prodi" required
                                class="tu-input @error('prodi') is-invalid @enderror">
                            <option value="">-- Pilih Program Studi --</option>
                        </select>
                        @error('prodi')
                            <p class="tu-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            <!-- Section 2: Pemilihan Instrumen & Parameter Laboratorium -->
            <section class="bg-white border border-gray-200 rounded-lg p-6 sm:p-8 space-y-6 shadow-sm tu-reveal [--d:80ms]">
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <div class="flex items-center gap-3">
                        <span class="inline-grid place-items-center w-7 h-7 rounded-full bg-racing-red text-white text-xs font-bold">2</span>
                        <h2 class="text-base sm:text-lg font-bold text-gray-950">Pemilihan Instrumen &amp; Fasilitas Lab</h2>
                    </div>
                    <span class="text-xs text-gray-500 font-medium">Berdasarkan Katalog Resmi</span>
                </div>

                <div class="space-y-4">
                    <div>
                        <label for="mesin_lab_id" class="tu-label">Instrumen Laboratorium yang Dituju <span class="text-racing-red">*</span></label>
                        <select id="mesin_lab_id" name="mesin_lab_id" dusk="select-mesin-lab" required
                                class="tu-input @error('mesin_lab_id') is-invalid @enderror" onchange="onMesinSelected()">
                            <option value="">-- Pilih Instrumen / Fasilitas Uji --</option>
                            @foreach ($mesinList as $mesin)
                                <option value="{{ $mesin->id }}"
                                        data-status="{{ $mesin->status_mesin }}"
                                        data-kuota="{{ $mesin->kuota_harian }}"
                                        data-sisa="{{ $mesin->sisaKuotaHarian() }}"
                                        data-sla="{{ $mesin->estimasi_sla_hari }}"
                                        data-lab="{{ $mesin->laboratorium }}"
                                        data-kategori="{{ $mesin->kategori_layanan }}"
                                        data-sop="{{ e($mesin->sop_penyiapan) }}"
                                        {{ old('mesin_lab_id', $loop->first ? $mesin->id : '') == $mesin->id ? 'selected' : '' }}>
                                    {{ $mesin->nama_mesin }} &middot; {{ $mesin->laboratorium }} ({{ $mesin->status_mesin }})
                                </option>
                            @endforeach
                        </select>
                        @error('mesin_lab_id')
                            <p class="tu-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Live Parameter Panel (SLA, Kuota, SOP) -->
                    <div id="panelParameterMesin" class="bg-gray-50 border border-gray-200 rounded-md p-5 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="bg-white border border-gray-200 p-3.5 rounded-md">
                                <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Status Instrumen</p>
                                <p id="dispStatusMesin" class="mt-1 font-bold text-sm text-emerald-700 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span>
                                    <span>Aktif Siap Pakai</span>
                                </p>
                            </div>
                            <div class="bg-white border border-gray-200 p-3.5 rounded-md">
                                <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Estimasi SLA Pengerjaan</p>
                                <p id="dispSlaMesin" class="mt-1 font-bold text-sm text-gray-900 tabular-nums">
                                    2 Hari Kerja
                                </p>
                            </div>
                            <div class="bg-white border border-gray-200 p-3.5 rounded-md">
                                <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide">Sisa Kuota Hari Ini</p>
                                <p id="dispKuotaMesin" class="mt-1 font-bold text-sm text-tiger-flame tabular-nums">
                                    5 / 6 Slot Tersedia
                                </p>
                            </div>
                        </div>

                        <!-- Panel SOP Penyiapan Sampel (PBI-06 / SC-06 Reader) -->
                        <div class="bg-amber-50/70 border border-amber-200 rounded-md p-4">
                            <div class="flex items-center gap-2 text-amber-900 font-bold text-xs uppercase tracking-wide mb-1.5">
                                <svg class="w-4 h-4 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Panduan Standar Operasional Prosedur (SOP) Penyiapan Sampel</span>
                            </div>
                            <div id="dispSopPenyiapan" class="text-xs text-amber-900/90 whitespace-pre-line leading-relaxed font-sans">
                                Silakan pilih mesin laboratorium untuk membaca petunjuk penyiapan sampel.
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Section 3: Spesifikasi Sampel & Rincian Pengujian -->
            <section class="bg-white border border-gray-200 rounded-lg p-6 sm:p-8 space-y-6 shadow-sm tu-reveal [--d:160ms]">
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <div class="flex items-center gap-3">
                        <span class="inline-grid place-items-center w-7 h-7 rounded-full bg-racing-red text-white text-xs font-bold">3</span>
                        <h2 class="text-base sm:text-lg font-bold text-gray-950">Spesifikasi Sampel &amp; Tujuan Uji</h2>
                    </div>
                    <span class="text-xs text-gray-500 font-medium">Fisik atau Digital</span>
                </div>

                <div class="space-y-6">
                    <!-- Pilihan Bentuk Sampel (Fisik / Digital) -->
                    <div>
                        <label class="tu-label">Bentuk Sampel Pengujian <span class="text-racing-red">*</span></label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-1.5">
                            <label class="relative flex items-start gap-3 p-4 border border-gray-200 rounded-lg cursor-pointer transition-all hover:border-racing-red has-[:checked]:border-racing-red has-[:checked]:bg-red-50/20">
                                <input type="radio" name="jenis_sampel" value="fisik" dusk="radio-sampel-fisik"
                                       class="mt-1 text-racing-red focus:ring-racing-red"
                                       {{ old('jenis_sampel', 'fisik') === 'fisik' ? 'checked' : '' }}
                                       onchange="toggleJenisSampel()">
                                <div>
                                    <p class="text-sm font-bold text-gray-900">Spesimen Fisik</p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        Sampel material, cairan, kristal, komponen elektronika, atau beton yang diserahkan langsung ke meja Laboran.
                                    </p>
                                    <span class="inline-block mt-2 text-[10px] font-semibold text-amber-800 bg-amber-100 px-2 py-0.5 rounded">
                                        Wajib Cetak &amp; Tempel Resi Barcode
                                    </span>
                                </div>
                            </label>

                            <label class="relative flex items-start gap-3 p-4 border border-gray-200 rounded-lg cursor-pointer transition-all hover:border-racing-red has-[:checked]:border-racing-red has-[:checked]:bg-red-50/20">
                                <input type="radio" name="jenis_sampel" value="digital" dusk="radio-sampel-digital"
                                       class="mt-1 text-racing-red focus:ring-racing-red"
                                       {{ old('jenis_sampel') === 'digital' ? 'checked' : '' }}
                                       onchange="toggleJenisSampel()">
                                <div>
                                    <p class="text-sm font-bold text-gray-900">Berkas / Dataset Digital</p>
                                    <p class="text-xs text-gray-500 mt-0.5">
                                        Data komputasi simulasi, file CAD/FEA, mesh 3D, atau dataset riset yang diunggah ke cloud storage.
                                    </p>
                                    <span class="inline-block mt-2 text-[10px] font-semibold text-emerald-800 bg-emerald-100 px-2 py-0.5 rounded">
                                        E-Ticket Digital (Tanpa Cetak Fisik)
                                    </span>
                                </div>
                            </label>
                        </div>
                        @error('jenis_sampel')
                            <p class="tu-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tautan Cloud (Hanya muncul jika Digital) -->
                    <div id="wrapTautanCloud" class="hidden transition-all duration-300">
                        <label for="tautan_file_cloud" class="tu-label">Tautan Berkas Cloud (Google Drive / OneDrive Kampus) <span class="text-racing-red">*</span></label>
                        <input type="url" id="tautan_file_cloud" name="tautan_file_cloud" dusk="input-tautan-cloud"
                               value="{{ old('tautan_file_cloud') }}"
                               class="tu-input @error('tautan_file_cloud') is-invalid @enderror"
                               placeholder="https://drive.google.com/drive/folders/...">
                        <p class="text-[11px] text-gray-500 mt-1">
                            Pastikan akses izin tautan telah diset terbuka (Viewer / Editor) agar tim Laboran dapat mengunduh berkas pengujian.
                        </p>
                        @error('tautan_file_cloud')
                            <p class="tu-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="nama_sampel" class="tu-label">Nama / Label Spesimen <span class="text-racing-red">*</span></label>
                            <input type="text" id="nama_sampel" name="nama_sampel" dusk="input-nama-sampel"
                                   value="{{ old('nama_sampel') }}" required
                                   class="tu-input @error('nama_sampel') is-invalid @enderror"
                                   placeholder="Contoh: Nanopartikel TiO2 Doping Fe 5%">
                            @error('nama_sampel')
                                <p class="tu-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="tanggal_uji" class="tu-label">Tanggal Rencana Uji <span class="text-racing-red">*</span></label>
                            <input type="date" id="tanggal_uji" name="tanggal_uji" dusk="input-tanggal-uji"
                                   value="{{ old('tanggal_uji', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required
                                   class="tu-input @error('tanggal_uji') is-invalid @enderror">
                            @error('tanggal_uji')
                                <p class="tu-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="deskripsi_sampel" class="tu-label">Deskripsi Karakteristik Sampel <span class="text-racing-red">*</span></label>
                        <textarea id="deskripsi_sampel" name="deskripsi_sampel" rows="3" dusk="input-deskripsi-sampel" required
                                  class="tu-input @error('deskripsi_sampel') is-invalid @enderror"
                                  placeholder="Jelaskan kondisi fisik sampel, perkiraan konsentrasi, pelarut yang digunakan, atau penanganan khusus bila ada.">{{ old('deskripsi_sampel') }}</textarea>
                        @error('deskripsi_sampel')
                            <p class="tu-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tujuan_pengujian" class="tu-label">Tujuan Pengujian Riset <span class="text-racing-red">*</span></label>
                        <textarea id="tujuan_pengujian" name="tujuan_pengujian" rows="2" dusk="input-tujuan-pengujian" required
                                  class="tu-input @error('tujuan_pengujian') is-invalid @enderror"
                                  placeholder="Contoh: Analisis spektrum absorbansi optik untuk riset Tugas Akhir / Publikasi Jurnal.">{{ old('tujuan_pengujian') }}</textarea>
                        @error('tujuan_pengujian')
                            <p class="tu-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            <!-- Section 4: Konfirmasi Ketentuan & Submit -->
            <section class="bg-gray-100/80 border border-gray-200 rounded-lg p-6 space-y-4 tu-reveal [--d:240ms]">
                <div class="flex items-start gap-3">
                    <input type="checkbox" id="persetujuan_sop" name="persetujuan_sop" required
                           class="tu-check mt-1" checked>
                    <label for="persetujuan_sop" class="text-xs text-gray-700 leading-relaxed cursor-pointer select-none">
                        Saya menyatakan bahwa data spesimen yang diajukan sudah sesuai dengan Standar Operasional Prosedur (SOP) Laboratorium Telkom University, bersedia menyerahkan sampel fisik maksimal dalam 3x24 jam sejak tiket diterbitkan, dan memahami bahwa layanan laboratorium kampus ini <strong>bebas biaya (gratis)</strong>.
                    </label>
                </div>

                <div class="pt-4 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="text-xs text-gray-500">
                        <span class="font-bold text-gray-900">Output:</span> Nomor Tiket + Resi E-Ticket PDF Ber-barcode resmi Telyuji.
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('home') }}" class="tu-btn tu-btn--ghost tu-btn--sm">
                            <span>Batal</span>
                        </a>
                        <button type="submit" id="btnSubmitPengajuan" dusk="btn-submit-pengajuan" class="tu-btn">
                            <span>Konfirmasi &amp; Terbitkan Resi Barcode</span>
                            <span class="arr" aria-hidden="true">&rarr;</span>
                        </button>
                    </div>
                </div>
            </section>
        </form>
    </div>
</div>

<script>
    const prodiByFakultas = @json($fakultasProdiMap);
    const oldProdi = @json(old('prodi', 'S1 Sistem Informasi'));

    function updateProdiOptions() {
        const fakSelect = document.getElementById('fakultas');
        const prodiSelect = document.getElementById('prodi');
        const selectedFak = fakSelect.value;

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

    function onMesinSelected() {
        const select = document.getElementById('mesin_lab_id');
        const opt = select.options[select.selectedIndex];
        if (!opt || !opt.value) return;

        const status = opt.getAttribute('data-status') || 'Aktif';
        const sla = opt.getAttribute('data-sla') || '2';
        const kuota = opt.getAttribute('data-kuota') || '5';
        const sisa = opt.getAttribute('data-sisa') || '5';
        const sop = opt.getAttribute('data-sop') || 'Tidak ada panduan spesifik.';

        // Render status
        const statusEl = document.getElementById('dispStatusMesin');
        if (status === 'Aktif') {
            statusEl.innerHTML = '<span class="w-2 h-2 rounded-full bg-emerald-500 inline-block"></span> <span>Aktif Siap Pakai</span>';
            statusEl.className = 'mt-1 font-bold text-sm text-emerald-700 flex items-center gap-1.5';
        } else {
            statusEl.innerHTML = '<span class="w-2 h-2 rounded-full bg-red-500 inline-block"></span> <span>Dalam Pemeliharaan</span>';
            statusEl.className = 'mt-1 font-bold text-sm text-red-600 flex items-center gap-1.5';
        }

        // Render SLA
        document.getElementById('dispSlaMesin').textContent = sla + ' Hari Kerja';

        // Render Kuota
        const kuotaEl = document.getElementById('dispKuotaMesin');
        kuotaEl.textContent = sisa + ' / ' + kuota + ' Slot Tersedia';
        if (parseInt(sisa, 10) <= 0) {
            kuotaEl.classList.remove('text-tiger-flame');
            kuotaEl.classList.add('text-red-600');
        } else {
            kuotaEl.classList.remove('text-red-600');
            kuotaEl.classList.add('text-tiger-flame');
        }

        // Render SOP
        document.getElementById('dispSopPenyiapan').textContent = sop;
    }

    function toggleJenisSampel() {
        const isDigital = document.querySelector('input[name="jenis_sampel"]:checked').value === 'digital';
        const wrap = document.getElementById('wrapTautanCloud');
        const inputCloud = document.getElementById('tautan_file_cloud');

        if (isDigital) {
            wrap.classList.remove('hidden');
            inputCloud.setAttribute('required', 'required');
        } else {
            wrap.classList.add('hidden');
            inputCloud.removeAttribute('required');
        }
    }

    // Inisialisasi awal saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function () {
        updateProdiOptions();
        onMesinSelected();
        toggleJenisSampel();
    });
</script>
@endsection
