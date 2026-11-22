@extends('layouts.app')

@section('content')
<div class="relative overflow-hidden" id="vl-root">
    <x-bg-pattern uid="laboran-ktm-pat" />
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 space-y-10">
        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
            <div class="tu-reveal max-w-2xl">
                <div class="flex items-center gap-2 mb-3">
                    <a href="{{ route('laboran.pengujian.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 hover:text-racing-red transition-colors">
                        <span>&larr; Panel Pengujian</span>
                    </a>
                    <span class="text-gray-300">&middot;</span>
                    <span class="tu-chip tu-chip--jasmine text-[11px]">PBI-01 Validasi Identitas</span>
                </div>
                <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-gray-950 leading-[1.05]">Validasi Identitas &amp; KTM</h1>
                <p class="mt-4 text-sm sm:text-base leading-relaxed text-gray-600">
                    Periksa kesesuaian data akademik dan berkas KTM/ID pemohon sebelum mereka diizinkan mengajukan uji laboratorium.
                </p>
            </div>
            <div class="tu-reveal [--d:100ms] flex flex-wrap items-center gap-3">
                <a href="{{ route('laboran.pengujian.index') }}" class="tu-btn tu-btn--sm tu-btn--ghost">
                    <span>Panel Pengujian Lab</span>
                </a>
            </div>
        </div>

        @if (session('status'))
            <div class="tu-alert" dusk="status-alert" role="alert">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="border border-racing-red bg-red-50/60 px-4 py-3 text-xs font-medium text-racing-red rounded-[3px]" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Metric strip -->
        <section aria-label="Ringkasan validasi KTM">
            <div class="grid grid-cols-2 lg:grid-cols-4 border-y border-gray-200 bg-white/70">
                <div class="tu-metric tu-reveal px-4 sm:px-7 py-7 border-r border-b lg:border-b-0 border-gray-200 [--c:#F26A2D]">
                    <span class="tu-tick"></span>
                    <p class="mt-5 text-xs sm:text-sm font-semibold text-tiger-flame">Perlu Verifikasi</p>
                    <p class="mt-1 text-4xl sm:text-5xl font-extrabold tracking-tight text-gray-950 tabular-nums">{{ $metrics['menunggu'] }}</p>
                    <p class="mt-2 text-xs text-gray-500">Berkas baru menunggu validasi</p>
                </div>
                <div class="tu-metric tu-reveal px-4 sm:px-7 py-7 border-b lg:border-b-0 lg:border-r border-gray-200 [--c:#34d399] [--d:80ms]">
                    <span class="tu-tick"></span>
                    <p class="mt-5 text-xs sm:text-sm font-semibold text-emerald-800">Tervalidasi</p>
                    <p class="mt-1 text-4xl sm:text-5xl font-extrabold tracking-tight text-gray-950 tabular-nums">{{ $metrics['tervalidasi'] }}</p>
                    <p class="mt-2 text-xs text-emerald-700">Akun terverifikasi aktif</p>
                </div>
                <div class="tu-metric tu-reveal px-4 sm:px-7 py-7 border-r border-gray-200 [--c:#EF4444] [--d:160ms]">
                    <span class="tu-tick"></span>
                    <p class="mt-5 text-xs sm:text-sm font-semibold text-red-700">Ditolak</p>
                    <p class="mt-1 text-4xl sm:text-5xl font-extrabold tracking-tight text-gray-950 tabular-nums">{{ $metrics['ditolak'] }}</p>
                    <p class="mt-2 text-xs text-red-600">Perlu unggah ulang</p>
                </div>
                <div class="tu-metric tu-reveal px-4 sm:px-7 py-7 [--c:#6B7280] [--d:240ms]">
                    <span class="tu-tick"></span>
                    <p class="mt-5 text-xs sm:text-sm font-semibold text-gray-700">Total Pemohon</p>
                    <p class="mt-1 text-4xl sm:text-5xl font-extrabold tracking-tight text-gray-950 tabular-nums">{{ $metrics['total'] }}</p>
                    <p class="mt-2 text-xs text-gray-500">{{ $metrics['belum_unggah'] }} belum unggah KTM</p>
                </div>
            </div>
        </section>

        <!-- Daftar Pemohon -->
        <section class="bg-white border border-gray-200 tu-reveal" aria-labelledby="queue-heading">
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4 border-b border-gray-200 px-5 sm:px-7 pt-5">
                <div>
                    <h2 id="queue-heading" class="text-base font-bold tracking-tight text-gray-950">Daftar Pemohon &amp; Berkas Identitas</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Kelola status validasi kartu identitas akademik mahasiswa dan dosen.</p>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between lg:justify-end gap-4 lg:gap-6 pb-4">
                    <div class="flex overflow-x-auto no-scrollbar gap-1 border-b sm:border-b-0 border-gray-100 pb-2 sm:pb-0" role="tablist">
                        <a href="{{ route('laboran.validasi.ktm.index', ['status' => 'all', 'q' => $search]) }}"
                           role="tab"
                           aria-selected="{{ $statusFilter === 'all' ? 'true' : 'false' }}"
                           dusk="tab-semua"
                           class="tu-tab">
                           Semua <span class="tu-mono text-xs text-gray-400">({{ $metrics['total'] }})</span>
                        </a>
                        <a href="{{ route('laboran.validasi.ktm.index', ['status' => 'menunggu', 'q' => $search]) }}"
                           role="tab"
                           aria-selected="{{ $statusFilter === 'menunggu' ? 'true' : 'false' }}"
                           dusk="tab-menunggu"
                           class="tu-tab">
                           Perlu Verifikasi <span class="tu-mono text-xs text-gray-400">({{ $metrics['menunggu'] }})</span>
                        </a>
                        <a href="{{ route('laboran.validasi.ktm.index', ['status' => 'tervalidasi', 'q' => $search]) }}"
                           role="tab"
                           aria-selected="{{ $statusFilter === 'tervalidasi' ? 'true' : 'false' }}"
                           dusk="tab-tervalidasi"
                           class="tu-tab">
                           Tervalidasi <span class="tu-mono text-xs text-gray-400">({{ $metrics['tervalidasi'] }})</span>
                        </a>
                        <a href="{{ route('laboran.validasi.ktm.index', ['status' => 'ditolak', 'q' => $search]) }}"
                           role="tab"
                           aria-selected="{{ $statusFilter === 'ditolak' ? 'true' : 'false' }}"
                           dusk="tab-ditolak"
                           class="tu-tab">
                           Ditolak <span class="tu-mono text-xs text-gray-400">({{ $metrics['ditolak'] }})</span>
                        </a>
                    </div>
                    <form method="GET" action="{{ route('laboran.validasi.ktm.index') }}" class="relative sm:w-64">
                        <input type="hidden" name="status" value="{{ $statusFilter }}">
                        <svg class="pointer-events-none absolute left-3 top-[13px] h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 21l-4.35-4.35M10.5 18a7.5 7.5 0 100-15 7.5 7.5 0 000 15z"/>
                        </svg>
                        <input name="q" value="{{ $search }}" dusk="input-search" type="search" class="tu-input !py-2 !pl-9 !text-sm" placeholder="Cari nama, NIM, prodi" autocomplete="off">
                    </form>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm" dusk="table-validasi-ktm">
                    <thead>
                        <tr class="border-b border-gray-100 tu-eyebrow !text-[10px] text-gray-400">
                            <th scope="col" class="px-5 sm:px-7 py-3 font-medium">Pemohon</th>
                            <th scope="col" class="px-3 py-3 font-medium">Nomor Induk</th>
                            <th scope="col" class="px-3 py-3 font-medium">Fakultas &amp; Prodi</th>
                            <th scope="col" class="px-3 py-3 font-medium">Berkas KTM</th>
                            <th scope="col" class="px-3 py-3 font-medium">Status Validasi</th>
                            <th scope="col" class="px-5 sm:px-7 py-3 text-right font-medium">Aksi Laboran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($pemohonList as $p)
                            <tr class="hover:bg-gray-50/60 transition-colors" dusk="row-pemohon-{{ $p->id }}">
                                {{-- Nama & Email --}}
                                <td class="px-5 sm:px-7 py-4 align-top">
                                    <p class="font-semibold text-gray-950">{{ $p->nama_lengkap ?? $p->name }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $p->email }}</p>
                                </td>

                                {{-- Nomor Induk (NIM) --}}
                                <td class="px-3 py-4 align-top whitespace-nowrap">
                                    @if ($p->nomor_induk)
                                        <span class="tu-mono text-sm font-semibold text-gray-900">{{ $p->nomor_induk }}</span>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Belum diisi</span>
                                    @endif
                                </td>

                                {{-- Fakultas & Prodi --}}
                                <td class="px-3 py-4 align-top">
                                    <p class="font-medium text-gray-900 text-xs">{{ $p->fakultas_id ?: '-' }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $p->prodi_id ?: '-' }}</p>
                                </td>

                                {{-- Berkas KTM --}}
                                <td class="px-3 py-4 align-top whitespace-nowrap">
                                    @if (!empty($p->foto_identitas))
                                        @php
                                            $ext = strtolower(pathinfo($p->foto_identitas, PATHINFO_EXTENSION));
                                            $previewUrl = route('laboran.validasi.ktm.preview', $p->id);
                                        @endphp
                                        <div class="flex items-center gap-2">
                                            <button type="button"
                                                    dusk="btn-preview-ktm-{{ $p->id }}"
                                                    onclick="openPreviewModal('{{ $previewUrl }}', '{{ strtoupper($ext) }}', '{{ addslashes($p->nama_lengkap ?? $p->name) }}')"
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded border border-gray-300 bg-white text-gray-700 hover:border-racing-red hover:text-racing-red transition-colors">
                                                @if(in_array($ext, ['jpg', 'jpeg', 'png']))
                                                    <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                @else
                                                    <svg class="w-3.5 h-3.5 text-racing-red" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                    </svg>
                                                @endif
                                                <span>Lihat Berkas</span>
                                            </button>
                                            <a href="{{ $previewUrl }}" target="_blank" title="Buka di tab baru" class="text-gray-400 hover:text-gray-700 text-xs">↗</a>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-xs text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                            Belum Unggah
                                        </span>
                                    @endif
                                </td>

                                {{-- Status Validasi --}}
                                <td class="px-3 py-4 align-top whitespace-nowrap" dusk="status-badge-{{ $p->id }}">
                                    @if ($p->status_validasi_ktm === 'Tervalidasi')
                                        <span class="tu-chip tu-chip--mint">Tervalidasi</span>
                                    @elseif ($p->status_validasi_ktm === 'Ditolak')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-800 border border-red-200">
                                            Ditolak
                                        </span>
                                    @elseif (!empty($p->foto_identitas))
                                        <span class="tu-chip tu-chip--jasmine">Perlu Verifikasi</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium text-gray-500 bg-gray-100">
                                            Belum Lengkap
                                        </span>
                                    @endif
                                </td>

                                {{-- Aksi Laboran --}}
                                <td class="px-5 sm:px-7 py-4 align-top text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5 justify-end">
                                        {{-- Tombol Setujui --}}
                                        @if ($p->status_validasi_ktm !== 'Tervalidasi' && !empty($p->foto_identitas))
                                            <form action="{{ route('laboran.validasi.ktm.approve', $p->id) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Validasi dan setujui KTM pemohon {{ addslashes($p->nama_lengkap ?? $p->name) }}?')">
                                                @csrf
                                                <button type="submit"
                                                        dusk="btn-setujui-{{ $p->id }}"
                                                        class="px-2.5 py-1 bg-emerald-700 hover:bg-emerald-800 text-white rounded text-xs font-semibold transition-colors flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    <span>Setujui</span>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Tombol Tolak --}}
                                        @if ($p->status_validasi_ktm !== 'Ditolak' && !empty($p->foto_identitas))
                                            <form action="{{ route('laboran.validasi.ktm.reject', $p->id) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Tolak berkas KTM pemohon {{ addslashes($p->nama_lengkap ?? $p->name) }}?')">
                                                @csrf
                                                <button type="submit"
                                                        dusk="btn-tolak-{{ $p->id }}"
                                                        class="px-2 py-1 bg-white hover:bg-red-50 text-red-700 border border-red-300 rounded text-xs font-semibold transition-colors">
                                                    Tolak
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Status jika sudah tervalidasi --}}
                                        @if ($p->status_validasi_ktm === 'Tervalidasi')
                                            <span class="text-xs text-emerald-700 font-medium">✓ Selesai</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 text-sm">
                                    Tidak ada data pemohon yang sesuai dengan filter atau kata kunci pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($pemohonList->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $pemohonList->links() }}
                </div>
            @endif
        </section>
    </div>
</div>

{{-- Modal Pratinjau Berkas KTM --}}
<div id="previewModal" class="fixed inset-0 z-50 hidden bg-black/60 flex items-center justify-center p-4" onclick="closePreviewModal(event)">
    <div class="bg-white rounded-xl shadow-2xl max-w-3xl w-full max-h-[90vh] flex flex-col overflow-hidden" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <div>
                <p class="text-base font-bold text-gray-900" id="modalTitle">Pratinjau Berkas KTM</p>
                <p class="text-xs text-gray-500" id="modalSubtitle">Dokumen identitas pemohon</p>
            </div>
            <div class="flex items-center gap-2">
                <a id="modalNewTabLink" href="about:blank" target="_blank" rel="noopener noreferrer" class="text-xs font-semibold text-racing-red hover:underline px-2 py-1">
                    Buka Tab Baru &rarr;
                </a>
                <button type="button" onclick="closePreviewModal()" class="w-8 h-8 rounded-full hover:bg-gray-100 grid place-items-center text-gray-500 hover:text-gray-900">
                    ✕
                </button>
            </div>
        </div>
        <div class="flex-1 overflow-auto p-6 bg-gray-50 min-h-[350px] flex items-center justify-center" id="modalBody">
            {{-- Content diinjeksi via JS --}}
        </div>
    </div>
</div>

<script>
    function openPreviewModal(url, ext, name) {
        document.getElementById('modalTitle').textContent = 'KTM: ' + name;
        document.getElementById('modalSubtitle').textContent = 'Format berkas: ' + ext;
        document.getElementById('modalNewTabLink').href = url;

        const body = document.getElementById('modalBody');
        body.innerHTML = '';

        if (['JPG', 'JPEG', 'PNG'].includes(ext)) {
            const img = document.createElement('img');
            img.src = url;
            img.alt = 'KTM ' + name;
            img.className = 'max-w-full max-h-[70vh] object-contain rounded border border-gray-200 shadow-sm';
            body.appendChild(img);
        } else {
            // PDF viewer iframe
            const iframe = document.createElement('iframe');
            iframe.src = url;
            iframe.className = 'w-full h-[70vh] rounded border border-gray-200';
            body.appendChild(iframe);
        }

        document.getElementById('previewModal').classList.remove('hidden');
    }

    function closePreviewModal(e) {
        if (!e || e.target.id === 'previewModal' || !e.target.closest('#modalBody')) {
            document.getElementById('previewModal').classList.add('hidden');
            document.getElementById('modalBody').innerHTML = '';
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePreviewModal();
        }
    });
</script>
@endsection
