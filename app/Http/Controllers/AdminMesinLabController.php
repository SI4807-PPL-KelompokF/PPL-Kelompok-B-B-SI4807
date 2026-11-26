<?php

namespace App\Http\Controllers;

use App\Models\MesinLab;
use App\Models\TiketPengujian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class AdminMesinLabController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Tampilkan daftar seluruh mesin laboratorium dan status operasionalnya (PBI-04).
     */
    public function index()
    {
        MesinLab::seedDefaultData();

        $mesins = MesinLab::withCount('tiketPengujian')
            ->orderBy('laboratorium')
            ->orderBy('nama_mesin')
            ->get();

        $metrics = [
            'total' => $mesins->count(),
            'aktif' => $mesins->where('status_mesin', MesinLab::STATUS_AKTIF)->count(),
            'maintenance' => $mesins->where('status_mesin', MesinLab::STATUS_MAINTENANCE)->count(),
        ];

        return view('admin.mesin.index', compact('mesins', 'metrics'));
    }

    /**
     * Tampilkan formulir penambahan mesin baru.
     */
    public function create()
    {
        $kategoriOptions = MesinLab::DEFAULT_KATEGORI_LAYANAN;

        return view('admin.mesin.create', compact('kategoriOptions'));
    }

    /**
     * Simpan mesin laboratorium baru ke katalog.
     */
    public function store(Request $request)
    {
        $data = $this->validateMesin($request);

        MesinLab::create($data);

        return redirect()->route('admin.mesin.index')
            ->with('status', 'Mesin laboratorium berhasil ditambahkan.');
    }

    /**
     * Tampilkan formulir perubahan data mesin.
     */
    public function edit(MesinLab $mesin)
    {
        $kategoriOptions = MesinLab::DEFAULT_KATEGORI_LAYANAN;

        return view('admin.mesin.edit', compact('mesin', 'kategoriOptions'));
    }

    /**
     * Perbarui data mesin laboratorium.
     */
    public function update(Request $request, MesinLab $mesin)
    {
        $data = $this->validateMesin($request);

        $mesin->update($data);

        return redirect()->route('admin.mesin.index')
            ->with('status', 'Data mesin laboratorium berhasil diperbarui.');
    }

    /**
     * Ubah status operasional mesin antara Aktif dan Maintenance.
     */
    public function toggleStatus(MesinLab $mesin)
    {
        $newStatus = $mesin->isAktif()
            ? MesinLab::STATUS_MAINTENANCE
            : MesinLab::STATUS_AKTIF;

        $mesin->update(['status_mesin' => $newStatus]);

        return back()->with('status', "Status mesin {$mesin->nama_mesin} berhasil diubah menjadi {$newStatus}.");
    }

    /**
     * Hapus mesin dari katalog dengan pemeriksaan integritas referensial.
     */
    public function destroy(MesinLab $mesin)
    {
        if (Schema::hasTable('tiket_pengujian')) {
            $terpakai = TiketPengujian::where('mesin_lab_id', $mesin->id)
                ->orWhere('instrumen', $mesin->nama_mesin)
                ->exists();

            if ($terpakai) {
                return back()->withErrors([
                    'mesin' => "Mesin {$mesin->nama_mesin} tidak dapat dihapus karena masih tercatat pada riwayat tiket pengujian.",
                ]);
            }
        }

        $nama = $mesin->nama_mesin;
        $mesin->delete();

        return redirect()->route('admin.mesin.index')
            ->with('status', "Mesin {$nama} berhasil dihapus.");
    }

    /**
     * Validasi parameter utama katalog mesin laboratorium.
     */
    private function validateMesin(Request $request): array
    {
        $validated = $request->validate([
            'nama_mesin' => ['required', 'string', 'max:150'],
            'kategori_layanan' => ['required', 'string', 'max:100'],
            'laboratorium' => ['required', 'string', 'max:150'],
            'status_mesin' => ['nullable', Rule::in([MesinLab::STATUS_AKTIF, MesinLab::STATUS_MAINTENANCE])],
            'kuota_harian' => ['required', 'integer', 'min:1', 'max:500'],
            'estimasi_sla_hari' => ['required', 'integer', 'min:1', 'max:60'],
        ], [
            'nama_mesin.required' => 'Nama mesin laboratorium wajib diisi.',
            'kategori_layanan.required' => 'Kategori layanan wajib diisi.',
            'laboratorium.required' => 'Nama laboratorium penempatan wajib diisi.',
            'status_mesin.in' => 'Status mesin harus bernilai Aktif atau Maintenance.',
            'kuota_harian.required' => 'Kuota harian pengujian wajib diisi.',
            'kuota_harian.min' => 'Kuota harian minimal 1 slot.',
            'estimasi_sla_hari.required' => 'Estimasi SLA pengerjaan wajib diisi.',
            'estimasi_sla_hari.min' => 'Estimasi SLA minimal 1 hari kerja.',
        ]);

        return [
            'nama_mesin' => trim($validated['nama_mesin']),
            'kategori_layanan' => trim($validated['kategori_layanan']),
            'laboratorium' => trim($validated['laboratorium']),
            'status_mesin' => $validated['status_mesin'] ?? MesinLab::STATUS_AKTIF,
            'kuota_harian' => (int) $validated['kuota_harian'],
            'estimasi_sla_hari' => (int) $validated['estimasi_sla_hari'],
        ];
    }
}
