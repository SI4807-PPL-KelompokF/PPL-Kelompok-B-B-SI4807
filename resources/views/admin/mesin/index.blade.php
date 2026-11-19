@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">Katalog Mesin</h1>
            <p class="text-sm text-gray-500">Kelola data mesin laboratorium dan status operasionalnya.</p>
        </div>
        <a href="{{ route('admin.mesin.create') }}"
           class="inline-flex items-center rounded-md bg-racing-red px-4 py-2 text-sm font-semibold text-white hover:opacity-90">
            + Tambah Mesin
        </a>
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
                    <th class="px-4 py-3">Kategori Layanan</th>
                    <th class="px-4 py-3">Status</th>
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
                            @if ($mesin->status_aktif)
                                <span class="rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-800">Aktif</span>
                            @else
                                <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800">Maintenance</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <form method="POST" action="{{ route('admin.mesin.toggle', $mesin) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-medium hover:bg-gray-50">
                                        {{ $mesin->status_aktif ? 'Set Maintenance' : 'Set Aktif' }}
                                    </button>
                                </form>
                                <a href="{{ route('admin.mesin.edit', $mesin) }}"
                                   class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-medium hover:bg-gray-50">Edit</a>
                                <form method="POST" action="{{ route('admin.mesin.destroy', $mesin) }}"
                                      onsubmit="return confirm('Hapus mesin {{ $mesin->nama_mesin }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="rounded-md border border-red-200 px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-50">Hapus</button>
                                </form>
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