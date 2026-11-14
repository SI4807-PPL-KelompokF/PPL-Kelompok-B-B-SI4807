<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * PBI-01: Master data Fakultas → Prodi Telkom University.
     * Sumber sama dengan PengajuanUjiController::getFakultasProdiMap().
     */
    private function getFakultasProdiMap(): array
    {
        return [
            'Fakultas Informatika (FIF)' => [
                'S1 Informatika',
                'S1 Sistem Informasi',
                'S1 Teknologi Informasi',
                'S1 Rekayasa Perangkat Lunak',
                'S1 Sains Data',
                'S2 Informatika',
            ],
            'Fakultas Teknik Elektro (FTE)' => [
                'S1 Teknik Telekomunikasi',
                'S1 Teknik Elektro',
                'S1 Teknik Biomedis',
                'S1 Teknik Komputer',
                'S2 Teknik Elektro',
            ],
            'Fakultas Rekayasa Industri (FRI)' => [
                'S1 Teknik Industri',
                'S1 Sistem Informasi',
                'S1 Teknik Logistik',
                'S2 Teknik Industri',
            ],
            'Fakultas Ekonomi dan Bisnis (FEB)' => [
                'S1 Manajemen',
                'S1 Akuntansi',
                'S2 Manajemen',
            ],
            'Fakultas Komunikasi dan Ilmu Sosial (FKS)' => [
                'S1 Ilmu Komunikasi',
                'S1 Hubungan Masyarakat',
                'S1 Administrasi Bisnis',
            ],
            'Fakultas Industri Kreatif (FIK)' => [
                'S1 Desain Komunikasi Visual',
                'S1 Desain Interior',
                'S1 Desain Produk',
                'S1 Seni Rupa',
            ],
            'Ilmu Terapan (FIT)' => [
                'D3 Teknik Telekomunikasi',
                'D3 Rekayasa Perangkat Lunak',
                'D3 Sistem Informasi',
            ],
            'Staf / Dosen' => [
                'Dosen Tetap',
                'Dosen Tidak Tetap',
                'Tenaga Kependidikan',
            ],
        ];
    }

    /**
     * PBI-01 (GET): Tampilkan halaman Profil Akademik & Validasi KTM.
     */
    public function show()
    {
        $user              = Auth::user();
        $fakultasProdiMap  = $this->getFakultasProdiMap();

        return view('profil.index', compact('user', 'fakultasProdiMap'));
    }

    /**
     * PBI-01 (PUT): Simpan pembaruan profil pemohon.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nama_lengkap'   => ['required', 'string', 'max:150'],
            'nomor_induk'    => [
                'required', 'string', 'max:50',
                Rule::unique('users', 'nomor_induk')->ignore($user->id),
            ],
            'fakultas_id'    => ['required', 'string', 'max:150'],
            'prodi_id'       => ['required', 'string', 'max:150'],
            'foto_identitas' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ], [
            'nama_lengkap.required'    => 'Nama lengkap wajib diisi.',
            'nama_lengkap.max'         => 'Nama lengkap maksimal 150 karakter.',
            'nomor_induk.required'     => 'Nomor induk (NIM/NIDN/NIP) wajib diisi.',
            'nomor_induk.max'          => 'Nomor induk maksimal 50 karakter.',
            'nomor_induk.unique'       => 'Nomor induk ini sudah digunakan oleh akun lain.',
            'fakultas_id.required'     => 'Pilih fakultas asal terlebih dahulu.',
            'prodi_id.required'        => 'Pilih program studi terlebih dahulu.',
            'foto_identitas.mimes'     => 'Format berkas KTM harus JPG, PNG, atau PDF.',
            'foto_identitas.max'       => 'Ukuran berkas KTM maksimal 2 MB.',
        ]);

        $updatePayload = [
            'nama_lengkap' => $validated['nama_lengkap'],
            'nomor_induk'  => $validated['nomor_induk'],
            'fakultas_id'  => $validated['fakultas_id'],
            'prodi_id'     => $validated['prodi_id'],
        ];

        // Validasi bahwa prodi_id sesuai dengan fakultas_id yang dipilih
        $map = $this->getFakultasProdiMap();
        if (
            !isset($map[$validated['fakultas_id']]) ||
            !in_array($validated['prodi_id'], $map[$validated['fakultas_id']])
        ) {
            return back()
                ->withInput()
                ->withErrors(['prodi_id' => 'Program studi tidak sesuai dengan fakultas yang dipilih.']);
        }

        // Handle unggah foto identitas
        if ($request->hasFile('foto_identitas')) {
            $file = $request->file('foto_identitas');

            // Hapus berkas lama jika ada
            if ($user->foto_identitas && Storage::disk('public')->exists($user->foto_identitas)) {
                Storage::disk('public')->delete($user->foto_identitas);
            }

            // Simpan berkas baru: storage/app/public/identitas/{user_id}/
            $path = $file->store('identitas/' . $user->id, 'public');
            $updatePayload['foto_identitas'] = $path;
        }

        $user->update($updatePayload);

        return redirect()
            ->route('profil.show')
            ->with('status', 'Profil akademik berhasil diperbarui.');
    }

    /**
     * PBI-01 (GET JSON): Endpoint AJAX — daftar prodi berdasarkan fakultas_id.
     */
    public function prodiByFakultas(Request $request)
    {
        $map      = $this->getFakultasProdiMap();
        $fakultas = $request->input('fakultas_id', '');
        $prodis   = $map[$fakultas] ?? [];

        return response()->json(['prodis' => $prodis]);
    }
}
