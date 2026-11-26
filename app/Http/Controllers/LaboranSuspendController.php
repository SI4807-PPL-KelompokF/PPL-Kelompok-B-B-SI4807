<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class LaboranSuspendController extends Controller
{
    /**
     * Tampilkan daftar akun pemohon dan status akses (PBI-02 / SPK-2).
     */
    public function index(Request $request)
    {
        $statusFilter = $request->query('status', 'semua');
        $search = trim($request->query('search', ''));

        // Dinamis count pemohon aktif & ditangguhkan
        $totalAktif = User::where('role', User::ROLE_PEMOHON)
            ->where(function ($q) {
                $q->whereNull('is_suspended')->orWhere('is_suspended', false);
            })
            ->count();

        $totalDitangguhkan = User::where('role', User::ROLE_PEMOHON)
            ->where('is_suspended', true)
            ->count();

        // Query pemohon
        $query = User::with(['fakultas', 'prodi'])
            ->where('role', User::ROLE_PEMOHON);

        if ($statusFilter === 'aktif') {
            $query->where(function ($q) {
                $q->whereNull('is_suspended')->orWhere('is_suspended', false);
            });
        } elseif ($statusFilter === 'ditangguhkan') {
            $query->where('is_suspended', true);
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nomor_induk', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $pemohonList = $query->orderBy('is_suspended', 'desc')
            ->orderBy('nama_lengkap', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('laboran.suspend.index', [
            'pemohonList' => $pemohonList,
            'statusFilter' => $statusFilter,
            'search' => $search,
            'totalAktif' => $totalAktif,
            'totalDitangguhkan' => $totalDitangguhkan,
        ]);
    }

    /**
     * Tangguhkan (suspend) sementara akun pemohon.
     */
    public function suspend(Request $request, User $user)
    {
        $request->validate([
            'kategori_pelanggaran' => 'nullable|string',
            'alasan_suspend' => 'required|string|min:5',
        ]);

        $kategori = trim($request->input('kategori_pelanggaran', ''));
        $detail = trim($request->input('alasan_suspend'));

        if (!empty($kategori)) {
            $alasanLengkap = "[{$kategori}] {$detail}";
        } else {
            $alasanLengkap = $detail;
        }

        $user->suspendAkun($alasanLengkap);

        return redirect()->back()->with('success', "Akun Pemohon {$user->name} berhasil ditangguhkan.");
    }

    /**
     * Cabut sanksi suspend pemohon.
     */
    public function unsuspend(User $user)
    {
        $user->cabutSuspend();

        return redirect()->back()->with('success', "Penangguhan akun Pemohon {$user->name} berhasil dicabut.");
    }
}
