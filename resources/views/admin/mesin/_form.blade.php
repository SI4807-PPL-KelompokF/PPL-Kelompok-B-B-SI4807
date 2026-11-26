<div class="space-y-5">
    <div>
        <label for="nama_mesin" class="tu-label">Nama Mesin / Instrumen <span class="text-racing-red">*</span></label>
        <input type="text" id="nama_mesin" name="nama_mesin" dusk="input-nama-mesin" required
               value="{{ old('nama_mesin', $mesin->nama_mesin ?? '') }}"
               placeholder="Contoh: Spektrofotometer UV-Vis Terintegrasi"
               class="tu-input @error('nama_mesin') is-invalid @enderror">
        @error('nama_mesin')
            <p class="tu-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        <div>
            <label for="kategori_layanan" class="tu-label">Kategori Layanan <span class="text-racing-red">*</span></label>
            <input type="text" id="kategori_layanan" name="kategori_layanan" dusk="input-kategori-layanan" list="kategori-options" required
                   value="{{ old('kategori_layanan', $mesin->kategori_layanan ?? '') }}"
                   placeholder="Contoh: Material & Kimia Terapan"
                   class="tu-input @error('kategori_layanan') is-invalid @enderror">
            <datalist id="kategori-options">
                @foreach ($kategoriOptions ?? \App\Models\MesinLab::DEFAULT_KATEGORI_LAYANAN as $kat)
                    <option value="{{ $kat }}"></option>
                @endforeach
            </datalist>
            @error('kategori_layanan')
                <p class="tu-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="laboratorium" class="tu-label">Lokasi Laboratorium <span class="text-racing-red">*</span></label>
            <input type="text" id="laboratorium" name="laboratorium" dusk="input-laboratorium" required
                   value="{{ old('laboratorium', $mesin->laboratorium ?? '') }}"
                   placeholder="Contoh: Lab Kimia Terpadu Telkom University"
                   class="tu-input @error('laboratorium') is-invalid @enderror">
            @error('laboratorium')
                <p class="tu-error">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div>
            <label for="kuota_harian" class="tu-label">Kuota Harian (Slot) <span class="text-racing-red">*</span></label>
            <input type="number" id="kuota_harian" name="kuota_harian" dusk="input-kuota-harian" min="1" max="500" required
                   value="{{ old('kuota_harian', $mesin->kuota_harian ?? 5) }}"
                   class="tu-input tu-mono @error('kuota_harian') is-invalid @enderror">
            @error('kuota_harian')
                <p class="tu-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="estimasi_sla_hari" class="tu-label">Estimasi SLA (Hari Kerja) <span class="text-racing-red">*</span></label>
            <input type="number" id="estimasi_sla_hari" name="estimasi_sla_hari" dusk="input-estimasi-sla" min="1" max="60" required
                   value="{{ old('estimasi_sla_hari', $mesin->estimasi_sla_hari ?? 2) }}"
                   class="tu-input tu-mono @error('estimasi_sla_hari') is-invalid @enderror">
            @error('estimasi_sla_hari')
                <p class="tu-error">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="status_mesin" class="tu-label">Status Operasional <span class="text-racing-red">*</span></label>
            @php
                $selectedStatus = old('status_mesin', $mesin->status_mesin ?? \App\Models\MesinLab::STATUS_AKTIF);
            @endphp
            <select id="status_mesin" name="status_mesin" dusk="select-status-mesin" required
                    class="tu-input @error('status_mesin') is-invalid @enderror">
                <option value="{{ \App\Models\MesinLab::STATUS_AKTIF }}" {{ $selectedStatus === \App\Models\MesinLab::STATUS_AKTIF ? 'selected' : '' }}>
                    Aktif
                </option>
                <option value="{{ \App\Models\MesinLab::STATUS_MAINTENANCE }}" {{ $selectedStatus === \App\Models\MesinLab::STATUS_MAINTENANCE ? 'selected' : '' }}>
                    Maintenance
                </option>
            </select>
            @error('status_mesin')
                <p class="tu-error">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>
