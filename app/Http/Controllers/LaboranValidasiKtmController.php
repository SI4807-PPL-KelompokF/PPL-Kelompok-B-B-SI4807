<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LaboranValidasiKtmController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'laboran']);
    }

    /**
     * Tampilkan antarmuka Laboran untuk memvalidasi KTM & Profil Akademik Pemohon (PBI-01).
     */
    public function index(Request $request)
    {
        $statusFilter = $request->query('status', 'all');
        $search = trim($request->query('q', ''));

        $query = User::where('role', User::ROLE_PEMOHON);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nomor_induk', 'like', "%{$search}%")
                  ->orWhere('fakultas_id', 'like', "%{$search}%")
                  ->orWhere('prodi_id', 'like', "%{$search}%");
            });
        }

        // Metrik ringkasan
        $allPemohon = User::where('role', User::ROLE_PEMOHON)->get();
        $metrics = [
            'total'        => $allPemohon->count(),
            'menunggu'     => $allPemohon->filter(function ($u) {
                return ($u->status_validasi_ktm ?? 'Belum Tervalidasi') !== 'Tervalidasi' && !empty($u->foto_identitas);
            })->count(),
            'tervalidasi'  => $allPemohon->where('status_validasi_ktm', 'Tervalidasi')->count(),
            'ditolak'      => $allPemohon->where('status_validasi_ktm', 'Ditolak')->count(),
            'belum_unggah' => $allPemohon->filter(function ($u) {
                return empty($u->foto_identitas);
            })->count(),
        ];

        // Terapkan filter tab
        if ($statusFilter === 'menunggu') {
            $query->where(function ($q) {
                $q->where('status_validasi_ktm', '!=', 'Tervalidasi')
                  ->orWhereNull('status_validasi_ktm');
            })->whereNotNull('foto_identitas')->where('foto_identitas', '!=', '');
        } elseif ($statusFilter === 'tervalidasi') {
            $query->where('status_validasi_ktm', 'Tervalidasi');
        } elseif ($statusFilter === 'ditolak') {
            $query->where('status_validasi_ktm', 'Ditolak');
        } elseif ($statusFilter === 'belum_unggah') {
            $query->where(function ($q) {
                $q->whereNull('foto_identitas')->orWhere('foto_identitas', '');
            });
        }

        // Urutan: yang sudah unggah berkas tapi belum tervalidasi ditaruh paling atas
        $pemohonList = $query->orderByRaw("
            CASE 
                WHEN (status_validasi_ktm = 'Belum Tervalidasi' OR status_validasi_ktm IS NULL) AND (foto_identitas IS NOT NULL AND foto_identitas != '') THEN 0
                WHEN status_validasi_ktm = 'Ditolak' THEN 1
                WHEN status_validasi_ktm = 'Tervalidasi' THEN 2
                ELSE 3
            END ASC
        ")->orderByDesc('updated_at')->paginate(12)->withQueryString();

        return view('laboran.validasi-ktm', [
            'pemohonList'  => $pemohonList,
            'metrics'      => $metrics,
            'statusFilter' => $statusFilter,
            'search'       => $search,
        ]);
    }

    /**
     * Validasi dan setujui KTM pemohon.
     */
    public function approve(User $user)
    {
        if (!$user->isPemohon()) {
            return back()->withErrors(['error' => 'Hanya akun pemohon yang dapat divalidasi identitasnya.']);
        }

        if (empty($user->foto_identitas)) {
            return back()->withErrors(['error' => 'Pemohon ini belum mengunggah berkas foto identitas / KTM.']);
        }

        $user->status_validasi_ktm = 'Tervalidasi';
        $user->save();

        $nama = $user->nama_lengkap ?? $user->name;
        $nim = $user->nomor_induk ? " ({$user->nomor_induk})" : '';

        return back()->with('status', "Identitas dan KTM pemohon {$nama}{$nim} berhasil divalidasi.");
    }

    /**
     * Tolak berkas KTM pemohon dengan catatan penolakan.
     */
    public function reject(Request $request, User $user)
    {
        if (!$user->isPemohon()) {
            return back()->withErrors(['error' => 'Hanya akun pemohon yang dapat diproses.']);
        }

        $user->status_validasi_ktm = 'Ditolak';
        $user->save();

        $nama = $user->nama_lengkap ?? $user->name;
        $nim = $user->nomor_induk ? " ({$user->nomor_induk})" : '';

        return back()->with('status', "Validasi KTM untuk pemohon {$nama}{$nim} telah ditolak.");
    }

    /**
     * Pratinjau / stream berkas KTM secara aman untuk Laboran.
     */
    public function previewKtm(User $user)
    {
        if (empty($user->foto_identitas) || !Storage::disk('public')->exists($user->foto_identitas)) {
            abort(404, 'Berkas foto KTM/ID tidak ditemukan di penyimpanan server.');
        }

        return Storage::disk('public')->response($user->foto_identitas);
    }
}
