@extends('layouts.app')

@section('content')
<div class="relative overflow-hidden" id="master-fp-root">
    <x-bg-pattern uid="master-fp-pat" />
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 space-y-10">
        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
            <div class="tu-reveal max-w-2xl">
                <div class="flex flex-wrap items-center gap-2 mb-3">
                    <span class="tu-chip tu-chip--jasmine">Portal Admin</span>
                    <span class="tu-chip tu-chip--cream">Referensi Akademik</span>
                </div>
                <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-gray-950 leading-[1.05]">
                    Kelola Master Data Fakultas &amp; Program Studi
                </h1>
                <p class="mt-4 text-sm sm:text-base leading-relaxed text-gray-600">
                    Atur daftar unit fakultas dan program studi sebagai referensi dinamis pada formulir profil akademik dan pengajuan uji laboratorium.
                </p>
            </div>

            <div class="tu-reveal [--d:100ms] flex flex-wrap items-center gap-2.5">
                <span dusk="pill-total-fakultas" class="tu-chip tu-chip--cream text-xs">
                    Total Fakultas: {{ $totalFakultas }}
                </span>
                <span dusk="pill-total-prodi" class="tu-chip tu-chip--mint text-xs">
                    Total Prodi: {{ $totalProdi }}
                </span>
            </div>
        </div>

        @if (session('status'))
            <div class="tu-alert" dusk="master-status-alert" role="alert">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="tu-alert tu-alert--danger space-y-1" role="alert">
                @foreach ($errors->all() as $err)
                    <p>&bull; {{ $err }}</p>
                @endforeach
            </div>
        @endif

        <!-- Metric Strip -->
        <section aria-label="Ringkasan master data akademik">
            <div class="grid grid-cols-2 border-y border-gray-200 bg-white/70">
                <div class="tu-metric tu-reveal px-5 sm:px-7 py-7 border-r border-gray-200 [--c:#ED1E28]">
                    <span class="tu-tick"></span>
                    <p class="mt-5 text-xs sm:text-sm font-semibold text-racing-red">Unit Fakultas</p>
                    <p class="mt-1 text-4xl sm:text-5xl font-extrabold tracking-tight text-gray-950 tabular-nums">{{ $totalFakultas }}</p>
                    <p class="mt-2 text-xs text-gray-500">Terdaftar aktif di sistem Telyuji</p>
                </div>
                <div class="tu-metric tu-reveal px-5 sm:px-7 py-7 [--c:#F26A2D] [--d:80ms]">
                    <span class="tu-tick"></span>
                    <p class="mt-5 text-xs sm:text-sm font-semibold text-tiger-flame">Program Studi</p>
                    <p class="mt-1 text-4xl sm:text-5xl font-extrabold tracking-tight text-gray-950 tabular-nums">{{ $totalProdi }}</p>
                    <p class="mt-2 text-xs text-gray-500">Tersinkron otomatis ke formulir pengajuan</p>
                </div>
            </div>
        </section>

        <!-- CARD 1: MASTER DATA FAKULTAS -->
        <section class="bg-white border border-gray-200" aria-labelledby="heading-fakultas">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-5 sm:px-7 py-5 border-b border-gray-200">
                <div>
                    <h2 id="heading-fakultas" class="text-base font-bold tracking-tight text-gray-950">Master Data Fakultas</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Unit fakultas induk di lingkungan kampus Telkom University.</p>
                </div>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <form method="GET" action="{{ route('admin.master.fakultas-prodi.index') }}" class="relative sm:w-64">
                        <input type="hidden" name="search_prodi" value="{{ $searchProdi }}">
                        <input type="hidden" name="filter_fakultas_id" value="{{ $filterFakultas }}">
                        <svg class="pointer-events-none absolute left-3 top-[11px] h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z"/>
                        </svg>
                        <input type="search" name="search_fakultas" dusk="input-search-fakultas" value="{{ $searchFakultas }}"
                               placeholder="Cari nama atau kode fakultas..."
                               class="tu-input !py-2 !pl-9 !text-xs">
                    </form>

                    <button type="button" dusk="btn-tambah-fakultas" onclick="openModal('modalTambahFakultas')" class="tu-btn tu-btn--sm">
                        <span>+ Tambah Fakultas</span>
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm" dusk="table-fakultas">
                    <thead>
                        <tr class="border-b border-gray-100 tu-eyebrow !text-[10px] text-gray-400">
                            <th scope="col" class="px-5 sm:px-7 py-3 font-medium w-14 text-center">No</th>
                            <th scope="col" class="px-3 py-3 font-medium w-32">Kode Fakultas</th>
                            <th scope="col" class="px-3 py-3 font-medium">Nama Fakultas</th>
                            <th scope="col" class="px-3 py-3 font-medium w-36 text-center">Jumlah Prodi</th>
                            <th scope="col" class="px-5 sm:px-7 py-3 font-medium w-40 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($fakultas as $idx => $f)
                            <tr dusk="row-fakultas-{{ $f->id }}" class="lu-row no-anim">
                                <td class="px-5 sm:px-7 py-3.5 text-center tu-mono text-xs text-gray-500">{{ $fakultas->firstItem() + $idx }}</td>
                                <td class="px-3 py-3.5">
                                    <span class="tu-chip tu-chip--jasmine tu-mono">{{ $f->kode_fakultas }}</span>
                                </td>
                                <td class="px-3 py-3.5 font-semibold text-gray-950">{{ $f->nama_fakultas }}</td>
                                <td class="px-3 py-3.5 text-center">
                                    <span class="tu-chip tu-chip--cream tu-mono">{{ $f->prodi_count }} Prodi</span>
                                </td>
                                <td class="px-5 sm:px-7 py-3.5 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2 justify-end">
                                        <button type="button"
                                                dusk="btn-edit-fakultas-{{ $f->id }}"
                                                onclick="editFakultas('{{ $f->id }}', '{{ addslashes($f->kode_fakultas) }}', '{{ addslashes($f->nama_fakultas) }}')"
                                                class="tu-btn tu-btn--sm tu-btn--ghost !py-1.5 !px-3 text-xs">
                                            Edit
                                        </button>
                                        <form action="{{ route('admin.fakultas.destroy', $f->id) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus Fakultas {{ addslashes($f->nama_fakultas) }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" dusk="btn-hapus-fakultas-{{ $f->id }}"
                                                    class="tu-btn tu-btn--sm tu-btn--ghost !py-1.5 !px-3 text-xs text-racing-red hover:border-racing-red">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">
                                    Tidak ada data fakultas ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($fakultas->hasPages())
                <div class="px-5 sm:px-7 py-4 border-t border-gray-100">
                    {{ $fakultas->links() }}
                </div>
            @endif
        </section>

        <!-- CARD 2: MASTER DATA PROGRAM STUDI -->
        <section class="bg-white border border-gray-200" aria-labelledby="heading-prodi">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 px-5 sm:px-7 py-5 border-b border-gray-200">
                <div>
                    <h2 id="heading-prodi" class="text-base font-bold tracking-tight text-gray-950">Master Data Program Studi</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Program studi yang bernaung di bawah masing-masing fakultas.</p>
                </div>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <form method="GET" action="{{ route('admin.master.fakultas-prodi.index') }}" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                        <input type="hidden" name="search_fakultas" value="{{ $searchFakultas }}">
                        <select name="filter_fakultas_id" dusk="select-filter-fakultas" onchange="this.form.submit()"
                                class="tu-input !py-2 !text-xs sm:w-56">
                            <option value="">Semua Fakultas</option>
                            @foreach ($allFakultas as $af)
                                <option value="{{ $af->id }}" {{ (string) $filterFakultas === (string) $af->id ? 'selected' : '' }}>
                                    {{ $af->kode_fakultas }} &middot; {{ $af->nama_fakultas }}
                                </option>
                            @endforeach
                        </select>

                        <div class="relative sm:w-56">
                            <svg class="pointer-events-none absolute left-3 top-[11px] h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z"/>
                            </svg>
                            <input type="search" name="search_prodi" dusk="input-search-prodi" value="{{ $searchProdi }}"
                                   placeholder="Cari kode atau nama prodi..."
                                   class="tu-input !py-2 !pl-9 !text-xs">
                        </div>
                    </form>

                    <button type="button" dusk="btn-tambah-prodi" onclick="openModal('modalTambahProdi')" class="tu-btn tu-btn--sm shrink-0">
                        <span>+ Tambah Prodi</span>
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm" dusk="table-prodi">
                    <thead>
                        <tr class="border-b border-gray-100 tu-eyebrow !text-[10px] text-gray-400">
                            <th scope="col" class="px-5 sm:px-7 py-3 font-medium w-14 text-center">No</th>
                            <th scope="col" class="px-3 py-3 font-medium w-28">Kode Prodi</th>
                            <th scope="col" class="px-3 py-3 font-medium">Nama Program Studi</th>
                            <th scope="col" class="px-3 py-3 font-medium">Fakultas Induk</th>
                            <th scope="col" class="px-5 sm:px-7 py-3 font-medium w-40 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($prodi as $idx => $p)
                            <tr dusk="row-prodi-{{ $p->id }}" class="lu-row no-anim">
                                <td class="px-5 sm:px-7 py-3.5 text-center tu-mono text-xs text-gray-500">{{ $prodi->firstItem() + $idx }}</td>
                                <td class="px-3 py-3.5">
                                    <span class="tu-chip tu-chip--cream tu-mono">{{ $p->kode_prodi ?: '-' }}</span>
                                </td>
                                <td class="px-3 py-3.5 font-semibold text-gray-950">{{ $p->nama_prodi }}</td>
                                <td class="px-3 py-3.5 text-xs text-gray-600">
                                    <span class="inline-flex items-center gap-2">
                                        <span class="tu-dot" aria-hidden="true"></span>
                                        <span>{{ $p->fakultas->nama_fakultas ?? '-' }}</span>
                                    </span>
                                </td>
                                <td class="px-5 sm:px-7 py-3.5 text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2 justify-end">
                                        <button type="button"
                                                dusk="btn-edit-prodi-{{ $p->id }}"
                                                onclick="editProdi('{{ $p->id }}', '{{ $p->fakultas_id }}', '{{ addslashes($p->kode_prodi) }}', '{{ addslashes($p->nama_prodi) }}')"
                                                class="tu-btn tu-btn--sm tu-btn--ghost !py-1.5 !px-3 text-xs">
                                            Edit
                                        </button>
                                        <form action="{{ route('admin.prodi.destroy', $p->id) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus Program Studi {{ addslashes($p->nama_prodi) }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" dusk="btn-hapus-prodi-{{ $p->id }}"
                                                    class="tu-btn tu-btn--sm tu-btn--ghost !py-1.5 !px-3 text-xs text-racing-red hover:border-racing-red">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">
                                    Tidak ada data program studi ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($prodi->hasPages())
                <div class="px-5 sm:px-7 py-4 border-t border-gray-100">
                    {{ $prodi->links() }}
                </div>
            @endif
        </section>
    </div>
</div>

<!-- 1. Modal Tambah Fakultas -->
<div id="modalTambahFakultas" dusk="modal-tambah-fakultas" class="fixed inset-0 z-50 hidden bg-black/60 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="title-tambah-fakultas">
    <div class="bg-white border border-gray-200 rounded-lg max-w-md w-full overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 id="title-tambah-fakultas" class="text-base font-bold text-gray-950">Tambah Fakultas</h3>
            <button type="button" onclick="closeModal('modalTambahFakultas')" class="tu-icon-btn !w-8 !h-8" aria-label="Tutup modal">&times;</button>
        </div>
        <form action="{{ route('admin.fakultas.store') }}" method="POST">
            @csrf
            <div class="p-6 space-y-4">
                <div>
                    <label for="tambah_kode_fakultas" class="tu-label">Kode Fakultas <span class="text-racing-red">*</span></label>
                    <input type="text" id="tambah_kode_fakultas" name="kode_fakultas" dusk="input-tambah-kode-fakultas" required placeholder="Contoh: FRI / FIF / FTE" class="tu-input uppercase">
                </div>
                <div>
                    <label for="tambah_nama_fakultas" class="tu-label">Nama Fakultas <span class="text-racing-red">*</span></label>
                    <input type="text" id="tambah_nama_fakultas" name="nama_fakultas" dusk="input-tambah-nama-fakultas" required placeholder="Contoh: Fakultas Rekayasa Industri (FRI)" class="tu-input">
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-2.5">
                <button type="button" onclick="closeModal('modalTambahFakultas')" class="tu-btn tu-btn--ghost tu-btn--sm">Batal</button>
                <button type="submit" dusk="btn-submit-tambah-fakultas" class="tu-btn tu-btn--sm">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<!-- 2. Modal Edit Fakultas -->
<div id="modalEditFakultas" dusk="modal-edit-fakultas" class="fixed inset-0 z-50 hidden bg-black/60 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="title-edit-fakultas">
    <div class="bg-white border border-gray-200 rounded-lg max-w-md w-full overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 id="title-edit-fakultas" class="text-base font-bold text-gray-950">Ubah Fakultas</h3>
            <button type="button" onclick="closeModal('modalEditFakultas')" class="tu-icon-btn !w-8 !h-8" aria-label="Tutup modal">&times;</button>
        </div>
        <form id="formEditFakultas" action="" method="POST">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-4">
                <div>
                    <label for="edit_kode_fakultas" class="tu-label">Kode Fakultas <span class="text-racing-red">*</span></label>
                    <input type="text" id="edit_kode_fakultas" name="kode_fakultas" dusk="input-edit-kode-fakultas" required class="tu-input uppercase">
                </div>
                <div>
                    <label for="edit_nama_fakultas" class="tu-label">Nama Fakultas <span class="text-racing-red">*</span></label>
                    <input type="text" id="edit_nama_fakultas" name="nama_fakultas" dusk="input-edit-nama-fakultas" required class="tu-input">
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-2.5">
                <button type="button" onclick="closeModal('modalEditFakultas')" class="tu-btn tu-btn--ghost tu-btn--sm">Batal</button>
                <button type="submit" dusk="btn-submit-edit-fakultas" class="tu-btn tu-btn--sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- 3. Modal Tambah Prodi -->
<div id="modalTambahProdi" dusk="modal-tambah-prodi" class="fixed inset-0 z-50 hidden bg-black/60 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="title-tambah-prodi">
    <div class="bg-white border border-gray-200 rounded-lg max-w-md w-full overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 id="title-tambah-prodi" class="text-base font-bold text-gray-950">Tambah Program Studi</h3>
            <button type="button" onclick="closeModal('modalTambahProdi')" class="tu-icon-btn !w-8 !h-8" aria-label="Tutup modal">&times;</button>
        </div>
        <form action="{{ route('admin.prodi.store') }}" method="POST">
            @csrf
            <div class="p-6 space-y-4">
                <div>
                    <label for="tambah_prodi_fakultas" class="tu-label">Fakultas Induk <span class="text-racing-red">*</span></label>
                    <select id="tambah_prodi_fakultas" name="fakultas_id" dusk="select-tambah-prodi-fakultas" required class="tu-input">
                        <option value="">-- Pilih Fakultas Induk --</option>
                        @foreach ($allFakultas as $af)
                            <option value="{{ $af->id }}">{{ $af->kode_fakultas }} &middot; {{ $af->nama_fakultas }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="tambah_kode_prodi" class="tu-label">Kode Prodi</label>
                    <input type="text" id="tambah_kode_prodi" name="kode_prodi" dusk="input-tambah-kode-prodi" placeholder="Contoh: IF / SI / TI" class="tu-input uppercase">
                </div>
                <div>
                    <label for="tambah_nama_prodi" class="tu-label">Nama Program Studi <span class="text-racing-red">*</span></label>
                    <input type="text" id="tambah_nama_prodi" name="nama_prodi" dusk="input-tambah-nama-prodi" required placeholder="Contoh: S1 Sistem Informasi" class="tu-input">
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-2.5">
                <button type="button" onclick="closeModal('modalTambahProdi')" class="tu-btn tu-btn--ghost tu-btn--sm">Batal</button>
                <button type="submit" dusk="btn-submit-tambah-prodi" class="tu-btn tu-btn--sm">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<!-- 4. Modal Edit Prodi -->
<div id="modalEditProdi" dusk="modal-edit-prodi" class="fixed inset-0 z-50 hidden bg-black/60 flex items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="title-edit-prodi">
    <div class="bg-white border border-gray-200 rounded-lg max-w-md w-full overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h3 id="title-edit-prodi" class="text-base font-bold text-gray-950">Ubah Program Studi</h3>
            <button type="button" onclick="closeModal('modalEditProdi')" class="tu-icon-btn !w-8 !h-8" aria-label="Tutup modal">&times;</button>
        </div>
        <form id="formEditProdi" action="" method="POST">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-4">
                <div>
                    <label for="edit_prodi_fakultas_id" class="tu-label">Fakultas Induk <span class="text-racing-red">*</span></label>
                    <select id="edit_prodi_fakultas_id" name="fakultas_id" dusk="select-edit-prodi-fakultas" required class="tu-input">
                        @foreach ($allFakultas as $af)
                            <option value="{{ $af->id }}">{{ $af->kode_fakultas }} &middot; {{ $af->nama_fakultas }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="edit_kode_prodi" class="tu-label">Kode Prodi</label>
                    <input type="text" id="edit_kode_prodi" name="kode_prodi" dusk="input-edit-kode-prodi" class="tu-input uppercase">
                </div>
                <div>
                    <label for="edit_nama_prodi" class="tu-label">Nama Program Studi <span class="text-racing-red">*</span></label>
                    <input type="text" id="edit_nama_prodi" name="nama_prodi" dusk="input-edit-nama-prodi" required class="tu-input">
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-2.5">
                <button type="button" onclick="closeModal('modalEditProdi')" class="tu-btn tu-btn--ghost tu-btn--sm">Batal</button>
                <button type="submit" dusk="btn-submit-edit-prodi" class="tu-btn tu-btn--sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openModal(id) {
        var el = document.getElementById(id);
        if (el) el.classList.remove('hidden');
    }

    function closeModal(id) {
        var el = document.getElementById(id);
        if (el) el.classList.add('hidden');
    }

    function editFakultas(id, kode, nama) {
        var form = document.getElementById('formEditFakultas');
        form.action = '/admin/fakultas/' + id;
        document.getElementById('edit_kode_fakultas').value = kode;
        document.getElementById('edit_nama_fakultas').value = nama;
        openModal('modalEditFakultas');
    }

    function editProdi(id, fakultasId, kode, nama) {
        var form = document.getElementById('formEditProdi');
        form.action = '/admin/prodi/' + id;
        document.getElementById('edit_prodi_fakultas_id').value = fakultasId;
        document.getElementById('edit_kode_prodi').value = kode;
        document.getElementById('edit_nama_prodi').value = nama;
        openModal('modalEditProdi');
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            document.querySelectorAll('[id^="modal"]').forEach(function (el) {
                el.classList.add('hidden');
            });
        }
    });
</script>
@endpush
