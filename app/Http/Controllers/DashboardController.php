<?php

namespace App\Http\Controllers;

use App\Models\TiketPengujian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Tampilkan halaman Dashboard Analitik Penggunaan Laboratorium (PBI-16).
     */
    public function index(Request $request)
    {
        // Nilai fallback apabila tabel belum dimigrasi
        $metrics = [
            'total'       => 0,
            'menunggu'    => 0,
            'sedang_diuji' => 0,
            'selesai'     => 0,
        ];
        $usageByInstrumen = collect();
        $usageByFakultas  = collect();

        try {
            if (!Schema::hasTable('tiket_pengujian')) {
                return view('admin.dashboard', compact(
                    'metrics', 'usageByInstrumen', 'usageByFakultas'
                ));
            }

            // ── Kartu ringkasan metrik ────────────────────────────────────────────
            // Satu kueri COUNT per status untuk menghindari N+1 dan pengambilan
            // seluruh record ke PHP.
            $statusCounts = TiketPengujian::query()
                ->select('status', DB::raw('COUNT(*) as jumlah'))
                ->groupBy('status')
                ->pluck('jumlah', 'status');

            $total = $statusCounts->sum();

            $metrics = [
                'total'        => $total,
                'menunggu'     => (int) ($statusCounts[TiketPengujian::STATUS_MENUNGGU] ?? 0),
                'sedang_diuji' => (int) ($statusCounts[TiketPengujian::STATUS_SEDANG_DIUJI] ?? 0),
                'selesai'      => (int) ($statusCounts[TiketPengujian::STATUS_SELESAI] ?? 0),
            ];

            // ── Grafik penggunaan mesin (instrumen) ──────────────────────────────
            // Satu baris tiket dihitung satu kali untuk instrumen yang tercatat.
            // Nilai kosong tetap ditampilkan sebagai "Tidak Diketahui" agar tidak
            // hilang diam-diam dari ringkasan jumlah pengajuan.
            $subInstrumen = TiketPengujian::query()
                ->selectRaw("CASE WHEN instrumen IS NULL OR TRIM(instrumen) = '' THEN 'Tidak Diketahui' ELSE TRIM(instrumen) END AS instrumen");

            $usageByInstrumen = DB::table(DB::raw("({$subInstrumen->toSql()}) as t_instrumen"))
                ->mergeBindings($subInstrumen->getQuery())
                ->select('instrumen', DB::raw('COUNT(*) as jumlah_pengajuan'))
                ->groupBy('instrumen')
                ->orderByDesc('jumlah_pengajuan')
                ->get();


            // Grafik distribusi pengajuan per fakultas
            // Normalisasi kode dan variasi nama fakultas agar tidak
            // dihitung sebagai kategori yang berbeda.

            $facultyValueSql = "TRIM(CASE
                WHEN INSTR(fakultas, '·') > 0
                THEN SUBSTR(fakultas, INSTR(fakultas, '·') + 1)
                ELSE fakultas
            END)";

            $subFakultas = TiketPengujian::query()->selectRaw("
                CASE
                    WHEN fakultas IS NULL OR TRIM(fakultas) = ''
                        THEN 'Tidak Diketahui'

                    WHEN {$facultyValueSql} = ''
                        THEN 'Tidak Diketahui'

                    WHEN UPPER({$facultyValueSql})
                        IN ('FTE', 'FAKULTAS TEKNIK ELEKTRO')
                        THEN 'Fakultas Teknik Elektro'

                    WHEN UPPER({$facultyValueSql})
                        IN ('FIF', 'FAKULTAS INFORMATIKA')
                        THEN 'Fakultas Informatika'

                    WHEN UPPER({$facultyValueSql})
                        IN ('FAK. REKAYASA INDUSTRI',
                            'FAKULTAS REKAYASA INDUSTRI')
                        THEN 'Fakultas Rekayasa Industri'

                    WHEN UPPER({$facultyValueSql})
                        IN ('FAK. ILMU TERAPAN',
                            'FAKULTAS ILMU TERAPAN')
                        THEN 'Fakultas Ilmu Terapan'

                    ELSE {$facultyValueSql}
                END AS fakultas
            ");

            $usageByFakultas = DB::table(
                    DB::raw("({$subFakultas->toSql()}) as t_fakultas")
                )
                ->mergeBindings($subFakultas->getQuery())
                ->select('fakultas', DB::raw('COUNT(*) as jumlah_pengajuan'))
                ->groupBy('fakultas')
                ->orderByDesc('jumlah_pengajuan')
                ->get();

        } catch (\Throwable $e) {
            // Tetap tampilkan fallback yang aman, tetapi catat penyebabnya agar
            // kegagalan query tidak tersembunyi saat dashboard kosong.
            Log::warning('PBI-16: gagal mengambil statistik dashboard laboratorium.', [
                'exception' => $e,
            ]);
        }

        return view('admin.dashboard', compact(
            'metrics', 'usageByInstrumen', 'usageByFakultas'
        ));
    }
}