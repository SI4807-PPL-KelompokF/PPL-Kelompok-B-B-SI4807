@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Ubah Mesin</h1>

    <form method="POST" action="{{ route('admin.mesin.update', $mesin) }}"
          class="rounded-lg border border-gray-200 bg-white p-6">
        @csrf
        @method('PUT')
        @include('admin.mesin._form')

        <div class="mt-6 flex justify-end gap-2">
            <a href="{{ route('admin.mesin.index') }}"
               class="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium hover:bg-gray-50">Batal</a>
            <button type="submit"
                    class="rounded-md bg-racing-red px-4 py-2 text-sm font-semibold text-white hover:opacity-90">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection