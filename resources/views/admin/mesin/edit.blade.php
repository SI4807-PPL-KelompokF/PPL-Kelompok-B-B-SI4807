@extends('layouts.app')

@section('content')
<div class="relative overflow-hidden">
    <x-bg-pattern uid="admin-mesin-edit-pat" />
    <div class="relative max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 space-y-8">
        <div class="tu-reveal space-y-2">
            <nav class="flex items-center gap-2 text-xs text-gray-500" aria-label="Breadcrumb">
                <a href="{{ route('admin.mesin.index') }}" class="transition-colors hover:text-racing-red">Katalog Mesin</a>
                <span aria-hidden="true">&rarr;</span>
                <span class="font-semibold text-gray-900">Ubah Mesin</span>
            </nav>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-gray-950">Ubah Data Mesin</h1>
            <p class="text-sm text-gray-600">Perbarui parameter operasional untuk <span class="font-semibold text-gray-950">{{ $mesin->nama_mesin }}</span>.</p>
        </div>

        <form method="POST" action="{{ route('admin.mesin.update', $mesin) }}" class="bg-white border border-gray-200 rounded-lg p-6 sm:p-8 space-y-6 tu-reveal">
            @csrf
            @method('PUT')
            @include('admin.mesin._form')

            <div class="pt-5 border-t border-gray-200 flex items-center justify-end gap-3">
                <a href="{{ route('admin.mesin.index') }}" class="tu-btn tu-btn--ghost tu-btn--sm">Batal</a>
                <button type="submit" dusk="btn-submit-mesin" class="tu-btn tu-btn--sm">
                    <span>Simpan Perubahan</span>
                    <span class="arr" aria-hidden="true">&rarr;</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
