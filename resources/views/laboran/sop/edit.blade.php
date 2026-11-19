@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css">

<div class="max-w-3xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold">SOP Penyiapan Sampel</h1>
    <p class="text-sm text-gray-500 mb-6">Mesin: <span class="font-semibold">{{ $mesin->nama_mesin }}</span></p>

    @if ($errors->any())
        <div class="mb-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <form id="form-sop" method="POST" action="{{ route('laboran.sop.update', $mesin) }}"
          class="rounded-lg border border-gray-200 bg-white p-6">
        @csrf
        @method('PUT')

        <div id="editor" class="bg-white" style="min-height: 260px;">{!! $sop !!}</div>
        <input type="hidden" name="sop_penyiapan" id="sop_penyiapan">

        <div class="mt-6 flex justify-end gap-2">
            <a href="{{ route('laboran.sop.index') }}"
               class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium hover:bg-gray-50">Batal</a>
            <button type="submit"
                    class="rounded-md bg-racing-red px-4 py-2 text-sm font-semibold text-white hover:opacity-90">Simpan SOP</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
<script>
    var quill = new Quill('#editor', {
        theme: 'snow',
        modules: {
            toolbar: [['bold', 'italic', 'underline'], [{ list: 'ordered' }, { list: 'bullet' }]]
        }
    });

    document.getElementById('form-sop').addEventListener('submit', function () {
        document.getElementById('sop_penyiapan').value = quill.root.innerHTML;
    });
</script>
@endsection