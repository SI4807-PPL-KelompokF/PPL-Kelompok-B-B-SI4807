@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css">
@endpush

@section('content')
<div class="relative overflow-hidden">
    <x-bg-pattern uid="laboran-sop-edit-pat" />
    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 space-y-8">
        <div class="tu-reveal space-y-2">
            <nav class="flex items-center gap-2 text-xs text-gray-500" aria-label="Breadcrumb">
                <a href="{{ route('laboran.sop.index') }}" class="transition-colors hover:text-racing-red">Kelola SOP</a>
                <span aria-hidden="true">&rarr;</span>
                <span class="font-semibold text-gray-900">Edit SOP</span>
            </nav>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-gray-950">SOP Penyiapan Sampel</h1>
            <p class="text-sm text-gray-600">
                Instrumen: <span class="font-semibold text-gray-950">{{ $mesin->nama_mesin }}</span> &middot; {{ $mesin->laboratorium }}
            </p>
        </div>

        @if ($errors->any())
            <div class="tu-alert tu-alert--danger" role="alert">
                {{ $errors->first() }}
            </div>
        @endif

        <form id="form-sop" method="POST" action="{{ route('laboran.sop.update', $mesin) }}"
              class="bg-white border border-gray-200 rounded-lg p-6 sm:p-8 space-y-6 tu-reveal">
            @csrf
            @method('PUT')

            <div>
                <p class="tu-label">Isi Panduan SOP Penyiapan Sampel</p>
                <div id="editor" class="bg-white min-h-[260px] text-sm">{!! $sop !!}</div>
                <input type="hidden" name="sop_penyiapan" id="sop_penyiapan" value="{{ old('sop_penyiapan', $sop) }}">
                <p class="text-xs text-gray-500 mt-2">
                    Gunakan daftar bernomor atau poin agar Pemohon mudah mengikuti persyaratan wadah, kondisi sampel, serta batas pengambilan residu 7 hari kerja.
                </p>
            </div>

            <div class="pt-5 border-t border-gray-200 flex items-center justify-end gap-3">
                <a href="{{ route('laboran.sop.index') }}" class="tu-btn tu-btn--ghost tu-btn--sm">Batal</a>
                <button type="submit" dusk="btn-simpan-sop" class="tu-btn tu-btn--sm">
                    <span>Simpan SOP</span>
                    <span class="arr" aria-hidden="true">&rarr;</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
<script>
    (function () {
        var editorEl = document.getElementById('editor');
        var hiddenInput = document.getElementById('sop_penyiapan');
        var form = document.getElementById('form-sop');

        if (!editorEl || !hiddenInput || !form || typeof Quill === 'undefined') return;

        var quill = new Quill('#editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{ list: 'ordered' }, { list: 'bullet' }]
                ]
            }
        });

        form.addEventListener('submit', function () {
            hiddenInput.value = quill.root.innerHTML;
        });
    })();
</script>
@endpush
