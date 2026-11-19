@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">SOP Penyiapan Sampel</h1>
        <p class="text-sm text-gray-500">Pilih mesin untuk menulis atau memperbarui SOP penyiapan sampel.</p>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto rounded-lg border border-gray-200 bg-white">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                <tr>
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Nama Mesin</th>
                    <th class="px-4 py-3">Kategori</th>
                    <th class="px-4 py-3">SOP</th>
                    <th class="px-4 py-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($mesins as $mesin)
                    <tr>
                        <td class="px-4 py-3">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 font-medium">{{ $mesin->nama_mesin }}</td>
                        <td class="px-4 py-3">{{ $mesin->kategori_layanan }}</td>
                        <td class="px-4 py-3">
                            @if ($mesin->sop_penyiapan)
                                <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-800">Sudah ada</span>
                            @else
                                <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600">Belum ada</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('laboran.sop.edit', $mesin) }}"
                                   class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-medium hover:bg-gray-50">
                                    {{ $mesin->sop_penyiapan ? 'Edit SOP' : 'Tulis SOP' }}
                                </a>
                                @if ($mesin->sop_penyiapan)
                                    <form method="POST" action="{{ route('laboran.sop.destroy', $mesin) }}"
                                          onsubmit="return confirm('Hapus SOP mesin {{ $mesin->nama_mesin }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="rounded-md border border-red-200 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-50">Hapus SOP</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada data mesin.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection