<?php

namespace App\Http\Requests;

use App\Models\MesinLab;
use App\Models\TiketPengujian;
use Illuminate\Foundation\Http\FormRequest;

class PengajuanUjiRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna memiliki otorisasi untuk membuat pengajuan.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Aturan validasi untuk formulir pengajuan uji lab dinamis (PBI-07).
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nama_pemohon' => ['required', 'string', 'max:150'],
            'nim_nip' => ['required', 'string', 'max:50'],
            'fakultas' => ['required', 'string', 'max:100'],
            'prodi' => ['required', 'string', 'max:100'],
            'mesin_lab_id' => ['required', 'exists:mesin_lab,id'],
            'nama_sampel' => ['required', 'string', 'max:150'],
            'deskripsi_sampel' => ['required', 'string', 'max:2000'],
            'tujuan_pengujian' => ['required', 'string', 'max:2000'],
            'jenis_sampel' => ['required', 'in:fisik,digital'],
            'tautan_file_cloud' => [
                'nullable',
                'required_if:jenis_sampel,digital',
                'url',
                'max:500',
            ],
            'tanggal_uji' => ['required', 'date', 'after_or_equal:today'],
        ];
    }

    /**
     * Kustomisasi pesan validasi bahasa Indonesia yang ramah pengguna.
     */
    public function messages(): array
    {
        return [
            'nama_pemohon.required' => 'Nama lengkap pemohon wajib diisi.',
            'nim_nip.required' => 'Nomor Induk (NIM / NIP / NIDN) wajib diisi.',
            'fakultas.required' => 'Fakultas asal wajib dipilih.',
            'prodi.required' => 'Program studi wajib dipilih atau diisi.',
            'mesin_lab_id.required' => 'Silakan pilih instrumen laboratorium yang akan digunakan.',
            'mesin_lab_id.exists' => 'Instrumen laboratorium yang dipilih tidak valid dalam katalog.',
            'nama_sampel.required' => 'Nama atau label sampel spesimen wajib diisi.',
            'deskripsi_sampel.required' => 'Deskripsi karakteristik sampel wajib diisi agar laboran memahami spesifikasi bahan.',
            'tujuan_pengujian.required' => 'Tujuan pengujian riset / praktikum wajib dijelaskan.',
            'jenis_sampel.required' => 'Pilih bentuk sampel uji (Fisik atau Berkas Digital).',
            'tautan_file_cloud.required_if' => 'Tautan penyimpanan cloud (Google Drive, OneDrive, atau server kampus) wajib diisi untuk sampel digital.',
            'tautan_file_cloud.url' => 'Format tautan cloud harus berupa URL yang valid (diawali http:// atau https://).',
            'tanggal_uji.required' => 'Pilih tanggal rencana pengujian.',
            'tanggal_uji.after_or_equal' => 'Tanggal rencana pengujian tidak boleh tanggal yang telah berlalu.',
        ];
    }

    /**
     * Validasi lanjutan: pastikan mesin berstatus Aktif dan kuota harian belum penuh (UC-07 alur alternatif).
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $mesinId = $this->input('mesin_lab_id');
            $tanggal = $this->input('tanggal_uji');

            if ($mesinId) {
                $mesin = MesinLab::find($mesinId);
                if ($mesin) {
                    if (!$mesin->isAktif()) {
                        $validator->errors()->add('mesin_lab_id', "Instrumen {$mesin->nama_mesin} saat ini sedang dalam status Pemeliharaan (Maintenance). Silakan pilih instrumen lain.");
                    } else {
                        $sisa = $mesin->sisaKuotaHarian($tanggal);
                        if ($sisa <= 0) {
                            $validator->errors()->add('tanggal_uji', "Kuota pengujian harian untuk {$mesin->nama_mesin} pada tanggal tersebut telah penuh (0/{$mesin->kuota_harian} slot tersisa). Silakan pilih tanggal lain.");
                        }
                    }
                }
            }
        });
    }
}
