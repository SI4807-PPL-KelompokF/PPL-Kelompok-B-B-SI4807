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

    /**
     * Menampilkan antrean penerimaan sampel & progres pengujian Laboran (PBI-10).
     */
    public function index(Request $request)
    {
        $statusFilter = $request->query('status', 'all');
        $search = trim((string) $request->query('q', ''));

        $baseActive = TiketPengujian::whereIn('status', [
            TiketPengujian::STATUS_MENUNGGU,
            TiketPengujian::STATUS_DALAM_ANTREAN,
        ]);

        $metrics = [
            'total' => (clone $baseActive)->count(),
            'menunggu' => TiketPengujian::where('status', TiketPengujian::STATUS_MENUNGGU)->count(),
            'antrean' => TiketPengujian::where('status', TiketPengujian::STATUS_DALAM_ANTREAN)->count(),
            'sedang_diuji' => TiketPengujian::where('status', TiketPengujian::STATUS_SEDANG_DIUJI)->count(),
        ];

        $query = TiketPengujian::with('mesinLab')
            ->whereIn('status', [
                TiketPengujian::STATUS_MENUNGGU,
                TiketPengujian::STATUS_DALAM_ANTREAN,
            ]);

        if ($statusFilter === 'menunggu') {
            $query->where('status', TiketPengujian::STATUS_MENUNGGU);
        } elseif ($statusFilter === 'antrean') {
            $query->where('status', TiketPengujian::STATUS_DALAM_ANTREAN);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_tiket', 'like', "%{$search}%")
                    ->orWhere('nama_sampel', 'like', "%{$search}%")
                    ->orWhere('nama_pemohon', 'like', "%{$search}%")
                    ->orWhere('instrumen', 'like', "%{$search}%");
            });
        }

        $tiketList = $query
            ->orderByRaw("CASE WHEN status = 'Menunggu Verifikasi' THEN 0 ELSE 1 END")
            ->orderBy('id', 'asc')
            ->get();

        return view('laboran.antrean', compact('tiketList', 'metrics', 'statusFilter', 'search'));
    }

    /**
     * Menerima sampel fisik di meja laboratorium (Menunggu Verifikasi -> Dalam Antrean).
     */
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
                AuditTrail::AKSI_TERIMA_FISIK,
                'Laboran menerima sampel fisik di meja penerimaan laboratorium.',
                $request
            );
        });

        return redirect()
            ->route('laboran.antrean.index')
            ->with('status', "Sampel berhasil diterima ({$nomorTiket}) dan masuk Dalam Antrean.");
    }

    /**
     * Memverifikasi berkas sampel digital / tautan cloud (Menunggu Verifikasi -> Dalam Antrean).
     */
    public function verifikasiDigital(Request $request, string $nomorTiket)
    {
        DB::transaction(function () use ($request, $nomorTiket) {
            $tiket = TiketPengujian::where('nomor_tiket', $nomorTiket)
                ->lockForUpdate()
                ->firstOrFail();

            if ($tiket->jenis_sampel !== 'digital') {
                abort(422, 'Tiket ini bukan pengajuan sampel digital.');
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
                AuditTrail::AKSI_VERIFIKASI_DIGITAL,
                'Laboran memverifikasi tautan berkas sampel digital pemohon.',
                $request
            );
        });

        return redirect()
            ->route('laboran.antrean.index')
            ->with('status', "Berkas digital tiket {$nomorTiket} berhasil diverifikasi dan masuk Dalam Antrean.");
    }

    /**
     * Memulai proses pengujian pada instrumen laboratorium (Dalam Antrean -> Sedang Diuji).
     */
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
                AuditTrail::AKSI_PROSES_MESIN,
                'Laboran memulai proses pengujian menggunakan mesin laboratorium.',
                $request
            );
        });

        return redirect()
            ->route('laboran.antrean.index')
            ->with('status', "Status tiket {$nomorTiket} berhasil diubah menjadi Sedang Diuji. Lanjutkan unggah LHU pada Panel Hasil Uji.");
    }

    /**
     * Mencatat perubahan status antrean ke audit_trails.
     */
    private function catatAudit(
        TiketPengujian $tiket,
        string $statusLama,
        string $statusBaru,
        string $aksi,
        string $keterangan,
        Request $request
    ): void {
        AuditTrail::create([
            'tiket_pengujian_id' => $tiket->id,
            'user_id' => Auth::id(),
            'nama_aktor' => Auth::user()->displayName(),
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
