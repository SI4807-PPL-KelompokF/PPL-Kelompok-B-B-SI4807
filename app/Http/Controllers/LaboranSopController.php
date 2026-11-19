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

    public function index()
    {
        $mesins = MesinLab::orderBy('nama_mesin')->get();

        return view('laboran.sop.index', compact('mesins'));
    }

    public function edit(MesinLab $mesin)
    {
        $sop = $mesin->sop_penyiapan ?: self::TEMPLATE_SOP;

        return view('laboran.sop.edit', compact('mesin', 'sop'));
    }

    public function update(Request $request, MesinLab $mesin)
    {
        $request->validate([
            'sop_penyiapan' => 'nullable|string|max:20000',
        ]);

        $mesin->update([
            'sop_penyiapan' => $this->bersihkan($request->input('sop_penyiapan')),
        ]);

        return redirect()->route('laboran.sop.index')
            ->with('success', 'SOP mesin ' . $mesin->nama_mesin . ' berhasil disimpan.');
    }

    // "Hapus SOP" = mengosongkan teks SOP, mesin tetap ada
    public function destroy(MesinLab $mesin)
    {
        $mesin->update(['sop_penyiapan' => null]);

        return redirect()->route('laboran.sop.index')
            ->with('success', 'SOP mesin ' . $mesin->nama_mesin . ' berhasil dihapus.');
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