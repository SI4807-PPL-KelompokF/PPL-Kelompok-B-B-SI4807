<?php

namespace App\Http\Controllers;

use App\Models\MesinLab;
use Illuminate\Http\Request;

class LaboranSopController extends Controller
{
    // Template awal saat SOP masih kosong (memuat klausul baku 7 hari kerja, UC-06)
    private const TEMPLATE_SOP = '<p><strong>SOP Penyiapan Sampel</strong></p>'
        . '<ol><li>Pastikan sampel dalam kondisi baik dan sesuai spesifikasi mesin.</li>'
        . '<li>Tempelkan resi barcode pada wadah sampel.</li></ol>'
        . '<p>Sampel wajib diambil maksimal 7 hari kerja setelah pengujian selesai.</p>';

    public function __construct()
    {
        $this->middleware(['auth', 'laboran']);
    }

    public function index()
    {
        MesinLab::seedDefaultData();

        $mesins = MesinLab::orderBy('laboratorium')
            ->orderBy('nama_mesin')
            ->get();

        $metrics = [
            'total' => $mesins->count(),
            'sudah_ada' => $mesins->filter(function (MesinLab $m) {
                return !empty(trim(strip_tags((string) $m->sop_penyiapan)));
            })->count(),
            'belum_ada' => $mesins->filter(function (MesinLab $m) {
                return empty(trim(strip_tags((string) $m->sop_penyiapan)));
            })->count(),
        ];

        return view('laboran.sop.index', compact('mesins', 'metrics'));
    }

    public function edit(MesinLab $mesin)
    {
        $sop = $this->formatForEditor($mesin->sop_penyiapan);

        return view('laboran.sop.edit', compact('mesin', 'sop'));
    }

    public function update(Request $request, MesinLab $mesin)
    {
        $request->validate([
            'sop_penyiapan' => ['nullable', 'string', 'max:20000'],
        ], [
            'sop_penyiapan.max' => 'Teks SOP maksimal 20.000 karakter.',
        ]);

        $mesin->update([
            'sop_penyiapan' => $this->bersihkan($request->input('sop_penyiapan')),
        ]);

        return redirect()->route('laboran.sop.index')
            ->with('status', 'SOP mesin ' . $mesin->nama_mesin . ' berhasil disimpan.');
    }

    // "Hapus SOP" = mengosongkan teks SOP, mesin tetap ada
    public function destroy(MesinLab $mesin)
    {
        $mesin->update(['sop_penyiapan' => null]);

        return redirect()->route('laboran.sop.index')
            ->with('status', 'SOP mesin ' . $mesin->nama_mesin . ' berhasil dihapus.');
    }

    /**
     * Siapkan teks SOP agar tampil rapi di editor Quill (mengonversi teks polos berbaris ke paragraf HTML).
     */
    private function formatForEditor(?string $raw): string
    {
        if ($raw === null || trim(strip_tags($raw)) === '') {
            return self::TEMPLATE_SOP;
        }

        if (preg_match('/<(p|ol|ul|li|br|strong|b|em|i|u)\b/i', $raw)) {
            return (string) $this->bersihkan($raw);
        }

        $lines = preg_split('/\r\n|\r|\n/', trim($raw)) ?: [];
        $paragraphs = array_map(function (string $line) {
            return '<p>' . e(trim($line)) . '</p>';
        }, array_filter($lines, function (string $line) {
            return trim($line) !== '';
        }));

        return implode('', $paragraphs);
    }

    // Hanya izinkan tag format dasar dan buang semua atribut (cegah XSS), tanpa dependency baru
    private function bersihkan(?string $html): ?string
    {
        if ($html === null || trim(strip_tags($html)) === '') {
            return null;
        }

        $html = strip_tags($html, '<p><br><strong><b><em><i><u><ul><ol><li>');

        return preg_replace('/<(p|br|strong|b|em|i|u|ul|ol|li)\b[^>]*>/i', '<$1>', $html);
    }
}
