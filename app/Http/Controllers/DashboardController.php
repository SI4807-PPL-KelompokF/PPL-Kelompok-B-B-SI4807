<?php

namespace App\Http\Controllers;

use App\Models\Fakultas;
use App\Models\TiketPengujian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

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
        $metrics = [
            'total' => 0,
            'menunggu' => 0,
            'dalam_antrean' => 0,
            'sedang_diuji' => 0,
            'selesai' => 0,
        ];
        $usageByInstrumen = collect();
        $usageByFakultas = collect();

        try {
            if (!Schema::hasTable('tiket_pengujian')) {
                return view('admin.dashboard', compact(
                    'metrics',
                    'usageByInstrumen',
                    'usageByFakultas'
                ));
            }

            // Satu kueri COUNT per status agar bebas N+1 dan portabel di SQLite, MySQL, & PostgreSQL
            $statusCounts = TiketPengujian::query()
                ->select('status', DB::raw('COUNT(*) as jumlah'))
                ->groupBy('status')
                ->pluck('jumlah', 'status');

            $metrics = [
                'total' => (int) $statusCounts->sum(),
                'menunggu' => (int) ($statusCounts[TiketPengujian::STATUS_MENUNGGU] ?? 0),
                'dalam_antrean' => (int) ($statusCounts[TiketPengujian::STATUS_DALAM_ANTREAN] ?? 0),
                'sedang_diuji' => (int) ($statusCounts[TiketPengujian::STATUS_SEDANG_DIUJI] ?? 0),
                'selesai' => (int) ($statusCounts[TiketPengujian::STATUS_SELESAI] ?? 0),
            ];

            // Agregasi penggunaan instrumen (portabel lintas dialek SQL)
            $rawInstrumen = TiketPengujian::query()
                ->select('instrumen', DB::raw('COUNT(*) as jumlah_pengajuan'))
                ->groupBy('instrumen')
                ->get();

            $usageByInstrumen = $rawInstrumen
                ->groupBy(function ($row) {
                    $label = trim((string) ($row->instrumen ?? ''));

                    return $label === '' ? 'Tidak Diketahui' : $label;
                })
                ->map(function ($group, $label) {
                    return (object) [
                        'instrumen' => $label,
                        'jumlah_pengajuan' => (int) $group->sum('jumlah_pengajuan'),
                    ];
                })
                ->sortByDesc('jumlah_pengajuan')
                ->values();

            // Agregasi distribusi fakultas (portabel di SQLite, MySQL, & PostgreSQL — tanpa INSTR())
            $rawFakultas = TiketPengujian::query()
                ->select('fakultas', DB::raw('COUNT(*) as jumlah_pengajuan'))
                ->groupBy('fakultas')
                ->get();

            $usageByFakultas = $rawFakultas
                ->groupBy(function ($row) {
                    return $this->normalizeFakultasLabel($row->fakultas);
                })
                ->map(function ($group, $label) {
                    $identity = Fakultas::resolveIdentity($label);

                    return (object) [
                        'fakultas' => $label,
                        'kode_fakultas' => $identity['code'],
                        'warna' => $identity['hex'],
                        'jumlah_pengajuan' => (int) $group->sum('jumlah_pengajuan'),
                    ];
                })
                ->sortByDesc('jumlah_pengajuan')
                ->values();
        } catch (\Throwable $e) {
            Log::warning('PBI-16: gagal mengambil statistik dashboard laboratorium.', [
                'exception' => $e,
            ]);
        }

        return view('admin.dashboard', compact(
            'metrics',
            'usageByInstrumen',
            'usageByFakultas'
        ));
    }

    /**
     * Normalisasikan variasi penulisan nama/kode fakultas dari seluruh modul (PBI-03, PBI-07, PBI-13)
     * agar tidak terpecah menjadi kategori duplikat pada grafik distribusi.
     */
    private function normalizeFakultasLabel(?string $raw): string
    {
        $value = trim((string) ($raw ?? ''));
        if ($value === '') {
            return 'Tidak Diketahui';
        }

        // Ambil segmen setelah pemisah '·' jika formatnya "S1 Teknik Elektro · FTE"
        if (mb_strpos($value, '·') !== false) {
            $parts = explode('·', $value);
            $value = trim((string) end($parts));
        }

        if ($value === '') {
            return 'Tidak Diketahui';
        }

        // Normalisasi kode dalam tanda kurung seperti "Fakultas Rekayasa Industri (FRI)"
        $withoutCode = trim((string) preg_replace('/\s*\([A-Za-z]{2,5}\)\s*$/', '', $value));
        $upper = strtoupper($withoutCode);

        $canonicalMap = [
            'FTE' => 'Fakultas Teknik Elektro',
            'FAK. TEKNIK ELEKTRO' => 'Fakultas Teknik Elektro',
            'FAKULTAS TEKNIK ELEKTRO' => 'Fakultas Teknik Elektro',
            'FIF' => 'Fakultas Informatika',
            'FAK. INFORMATIKA' => 'Fakultas Informatika',
            'FAKULTAS INFORMATIKA' => 'Fakultas Informatika',
            'FRI' => 'Fakultas Rekayasa Industri',
            'FAK. REKAYASA INDUSTRI' => 'Fakultas Rekayasa Industri',
            'FAKULTAS REKAYASA INDUSTRI' => 'Fakultas Rekayasa Industri',
            'FIT' => 'Fakultas Ilmu Terapan',
            'ILMU TERAPAN' => 'Fakultas Ilmu Terapan',
            'FAK. ILMU TERAPAN' => 'Fakultas Ilmu Terapan',
            'FAKULTAS ILMU TERAPAN' => 'Fakultas Ilmu Terapan',
            'FEB' => 'Fakultas Ekonomi dan Bisnis',
            'FAK. EKONOMI DAN BISNIS' => 'Fakultas Ekonomi dan Bisnis',
            'FAKULTAS EKONOMI DAN BISNIS' => 'Fakultas Ekonomi dan Bisnis',
            'FKS' => 'Fakultas Komunikasi dan Ilmu Sosial',
            'FKB' => 'Fakultas Komunikasi dan Ilmu Sosial',
            'FAKULTAS KOMUNIKASI DAN ILMU SOSIAL' => 'Fakultas Komunikasi dan Ilmu Sosial',
            'FIK' => 'Fakultas Industri Kreatif',
            'FAKULTAS INDUSTRI KREATIF' => 'Fakultas Industri Kreatif',
        ];

        return $canonicalMap[$upper] ?? $withoutCode;
    }
}
