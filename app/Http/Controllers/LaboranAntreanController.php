<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use App\Models\TiketPengujian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LaboranAntreanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'laboran']);
    }

    // Menampilkan antrean aktif PBI-10
    public function index()
    {
        $tiketList = TiketPengujian::whereIn('status', [
            TiketPengujian::STATUS_MENUNGGU,
            TiketPengujian::STATUS_DALAM_ANTREAN,
        ])
            ->orderBy('id', 'asc')
            ->get();

        return view('laboran.antrean', compact('tiketList'));
    }

    // Menerima sampel fisik
    public function terimaFisik(Request $request, string $nomorTiket)
    {
        DB::transaction(function () use ($request, $nomorTiket) {
            $tiket = TiketPengujian::where('nomor_tiket', $nomorTiket)
                ->lockForUpdate()
                ->firstOrFail();

            if ($tiket->jenis_sampel !== 'fisik') {
                abort(422, 'Tiket ini bukan pengajuan sampel fisik.');
            }

            if ($tiket->status !== TiketPengujian::STATUS_MENUNGGU) {
                abort(422, 'Status tiket bukan Menunggu Verifikasi.');
            }

            $statusLama = $tiket->status;

            $tiket->status = TiketPengujian::STATUS_DALAM_ANTREAN;
            $tiket->received_at = now();
            $tiket->save();

            $this->catatAudit(
                $tiket,
                $statusLama,
                $tiket->status,
                'TERIMA_FISIK',
                'Laboran menerima sampel fisik.',
                $request
            );
        });

        return redirect()
            ->route('laboran.antrean.index')
            ->with('success', 'Sampel berhasil diterima dan masuk Dalam Antrean.');
    }

    // Memulai proses pengujian dengan mesin
    public function prosesMesin(Request $request, string $nomorTiket)
    {
        DB::transaction(function () use ($request, $nomorTiket) {
            $tiket = TiketPengujian::where('nomor_tiket', $nomorTiket)
                ->lockForUpdate()
                ->firstOrFail();

            if ($tiket->status !== TiketPengujian::STATUS_DALAM_ANTREAN) {
                abort(422, 'Tiket harus berstatus Dalam Antrean sebelum diproses.');
            }

            $statusLama = $tiket->status;

            $tiket->status = TiketPengujian::STATUS_SEDANG_DIUJI;
            $tiket->testing_at = now();
            $tiket->save();

            $this->catatAudit(
                $tiket,
                $statusLama,
                $tiket->status,
                'PROSES_MESIN',
                'Laboran memulai proses pengujian menggunakan mesin.',
                $request
            );
        });

        return redirect()
            ->route('laboran.antrean.index')
            ->with('success', 'Status tiket berhasil diubah menjadi Sedang Diuji.');
    }

    // Mencatat perubahan status ke audit_trails
    private function catatAudit(
        TiketPengujian $tiket,
        string $statusLama,
        string $statusBaru,
        string $aksi,
        string $keterangan,
        Request $request
    ) {
        AuditTrail::create([
            'tiket_pengujian_id' => $tiket->id,
            'user_id' => Auth::id(),
            'nama_aktor' => Auth::user()->name,
            'aksi' => $aksi,
            'berkas_lama' => $statusLama,
            'berkas_baru' => $statusBaru,
            'ukuran_bytes' => 0,
            'versi' => 1,
            'keterangan' => $keterangan,
            'ip_address' => $request->ip(),
        ]);
    }
}
