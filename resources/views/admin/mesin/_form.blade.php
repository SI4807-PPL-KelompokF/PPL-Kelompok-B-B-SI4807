<div class="space-y-5">
    <div>
        <label for="nama_mesin" class="block text-sm font-medium mb-1">Nama Mesin</label>
        <input type="text" id="nama_mesin" name="nama_mesin"
               value="{{ old('nama_mesin', $mesin->nama_mesin ?? '') }}"
               class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
        @error('nama_mesin')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="kategori_layanan" class="block text-sm font-medium mb-1">Kategori Layanan</label>
        <input type="text" id="kategori_layanan" name="kategori_layanan"
               value="{{ old('kategori_layanan', $mesin->kategori_layanan ?? '') }}"
               class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gray-400">
        @error('kategori_layanan')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>