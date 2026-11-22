<?php

namespace App\Services;

use App\Models\TiketPengujian;

/**
 * ResiPdfService
 *
 * Generator dokumen Resi E-Ticket PDF ber-barcode resmi Telyuji (PBI-08 / UC-08).
 * Menghasilkan file PDF murni (ISO 32000-1 / PDF-1.4) dengan tata letak minimalis,
 * logogram resmi Telyuji tersemat (RGB FlateDecode XObject), dan barcode vektor Code128
 * tanpa ketergantungan ekstensi/library eksternal (waktu render < 30ms).
 */
class ResiPdfService
{
    /** @var BarcodeGeneratorService */
    private $barcodeService;

    public function __construct(BarcodeGeneratorService $barcodeService)
    {
        $this->barcodeService = $barcodeService;
    }

    /**
     * Hasilkan stream biner PDF dokumen resi pengujian untuk tiket yang diberikan.
     */
    public function generate(TiketPengujian $tiket): string
    {
        $pdf = new PurePdfBuilder();
        $pdf->setPageSize(595.28, 841.89); // A4 Portrait (pt, 72 dpi)

        // Palet Warna Resmi Telyuji
        $red = [237 / 255, 30 / 255, 40 / 255];      // Racing Red #ED1E28
        $flame = [242 / 255, 106 / 255, 45 / 255];   // Tiger Flame #F26A2D
        $jasmine = [247 / 255, 212 / 255, 136 / 255];// Jasmine #F7D488
        $mint = [233 / 255, 247 / 255, 202 / 255];   // Frosted Mint #E9F7CA
        $ink = [17 / 255, 24 / 255, 39 / 255];       // Gray 900 #111827
        $body = [55 / 255, 65 / 255, 81 / 255];      // Gray 700 #374151
        $muted = [107 / 255, 114 / 255, 128 / 255];  // Gray 500 #6B7280
        $subtle = [156 / 255, 163 / 255, 175 / 255]; // Gray 400 #9CA3AF
        $line = [229 / 255, 231 / 255, 235 / 255];   // Gray 200 #E5E7EB
        $surface = [249 / 255, 250 / 255, 251 / 255];// Gray 50 #F9FAFB
        $white = [1.0, 1.0, 1.0];

        $margin = 44.0;
        $contentWidth = 595.28 - ($margin * 2); // 507.28 pt
        $rightEdge = $margin + $contentWidth;   // 551.28 pt

        // 1. Garis Aksen Tipis di Puncak Halaman (Dual-tone Telyuji)
        $pdf->drawRect(0, 837.89, 465.0, 4.0, $red, true);
        $pdf->drawRect(465.0, 837.89, 130.28, 4.0, $flame, true);

        // 2. Header Institusi: Logo Resmi Telyuji + Identitas Minimalis
        $logoPath = public_path('images/logo-telyuji.png');
        $logoDrawn = $pdf->drawPngImage($logoPath, $margin, 774.0, 38.0, 38.0);
        $brandX = $logoDrawn ? $margin + 48.0 : $margin;

        $pdf->addText($brandX, 796.0, 'Telyuji', 'Helvetica-Bold', 18, $ink);
        $pdf->addText($brandX, 783.0, 'Teliti dan Teruji  |  Laboratorium Riset Terpadu', 'Helvetica', 8.5, $muted);
        $pdf->addText($brandX, 772.0, 'Telkom University', 'Helvetica-Bold', 8, $body);

        // Kanan Header: Klasifikasi Dokumen & Pill Alur Spesimen
        $pdf->addTextRight($rightEdge, 801.0, 'RESI E-TICKET & BUKTI PENDAFTARAN', 'Helvetica-Bold', 8.5, $ink);
        $pdf->addTextRight($rightEdge, 790.0, 'SPESIMEN PENGUJIAN LABORATORIUM', 'Helvetica', 7.5, $muted);

        $isDigital = $tiket->jenis_sampel === 'digital';
        $pillText = $isDigital ? 'BERKAS DIGITAL  |  E-TICKET' : 'SPESIMEN FISIK  |  TEMPEL RESI';
        $pillBg = $isDigital ? $mint : $jasmine;
        $pillW = $isDigital ? 132.0 : 144.0;
        $pillX = $rightEdge - $pillW;
        $pdf->drawRect($pillX, 769.0, $pillW, 15.5, $pillBg, true);
        $pdf->addTextCentered($pillX, $pillW, 774.2, $pillText, 'Helvetica-Bold', 7, $ink);

        // Garis Pembatas Header
        $pdf->drawHLine($margin, 756.0, $contentWidth, $line, 0.75);

        // 3. Blok Fokus Utama: Nomor Tiket & Barcode Code128
        $heroY = 668.0;
        $heroH = 74.0;
        $pdf->drawRect($margin, $heroY, $contentWidth, $heroH, $white, true);
        $pdf->drawRect($margin, $heroY, $contentWidth, $heroH, $line, false, 0.75);
        $pdf->drawRect($margin, $heroY, 3.0, $heroH, $red, true);

        // Kiri Hero: Nomor Identifikasi Tiket + Tanggal
        $pdf->addText($margin + 16.0, $heroY + 54.0, 'NOMOR IDENTIFIKASI TIKET', 'Helvetica-Bold', 7, $muted);
        $pdf->addText($margin + 16.0, $heroY + 30.0, $tiket->nomor_tiket, 'Courier-Bold', 18, $red);

        $tglDaftar = ($tiket->submitted_at ?: $tiket->created_at ?: now())->translatedFormat('d M Y, H:i') . ' WIB';
        $tglUji = $tiket->tanggal_uji ? $tiket->tanggal_uji->translatedFormat('d M Y') : '-';
        $pdf->addText($margin + 16.0, $heroY + 13.0, "Terdaftar: {$tglDaftar}   |   Rencana Uji: {$tglUji}", 'Helvetica', 7.5, $muted);

        // Pembatas Vertikal Halus antara Nomor Tiket dan Barcode
        $barcodeDividerX = $rightEdge - 210.0;
        $pdf->drawVLine($barcodeDividerX, $heroY + 10.0, $heroH - 20.0, $line, 0.6);

        // Kanan Hero: Vektor Barcode Code128 dengan Quiet-Zone Putih Bersih
        $barcodeBoxX = $barcodeDividerX + 14.0;
        $barcodeW = 182.0;
        $this->drawBarcodeVector($pdf, $tiket->nomor_tiket, $barcodeBoxX, $heroY + 24.0, $barcodeW, 38.0, $ink);
        $pdf->addTextCentered($barcodeBoxX, $barcodeW, $heroY + 11.0, "* {$tiket->nomor_tiket} *", 'Courier-Bold', 8, $ink);

        // 4. Grid Dua Kolom: Identitas Pemohon & Spesifikasi Layanan
        $colGap = 24.0;
        $colW = ($contentWidth - $colGap) / 2; // ~241.64 pt
        $leftColX = $margin;
        $rightColX = $margin + $colW + $colGap;

        $sectionTopY = 640.0;

        // Judul Kolom Kiri
        $pdf->addText($leftColX, $sectionTopY, '01  IDENTITAS PEMOHON', 'Helvetica-Bold', 8, $ink);
        $pdf->drawHLine($leftColX, $sectionTopY - 6.0, $colW, $line, 0.75);

        // Judul Kolom Kanan
        $pdf->addText($rightColX, $sectionTopY, '02  SPESIFIKASI LAYANAN LAB', 'Helvetica-Bold', 8, $ink);
        $pdf->drawHLine($rightColX, $sectionTopY - 6.0, $colW, $line, 0.75);

        // Isi Kolom Kiri
        $yLeft = $sectionTopY - 22.0;
        $yLeft = $this->drawFieldBlock($pdf, $leftColX, $yLeft, $colW, 'Nama Lengkap', $tiket->nama_pemohon ?: '-', $ink, $muted, 9.5);
        $yLeft = $this->drawFieldBlock($pdf, $leftColX, $yLeft, $colW, 'Nomor Induk (NIM / NIP)', $tiket->nim_nip ?: '-', $ink, $muted, 9.0, 'Courier-Bold');
        $yLeft = $this->drawFieldBlock($pdf, $leftColX, $yLeft, $colW, 'Fakultas / Unit', $tiket->fakultas ?: '-', $ink, $muted, 8.5);
        $yLeft = $this->drawFieldBlock($pdf, $leftColX, $yLeft, $colW, 'Program Studi', $tiket->prodi ?: '-', $ink, $muted, 8.5);

        // Isi Kolom Kanan
        $yRight = $sectionTopY - 22.0;
        $yRight = $this->drawFieldBlock($pdf, $rightColX, $yRight, $colW, 'Nama / Label Sampel', $tiket->nama_sampel ?: '-', $ink, $muted, 9.5);
        $yRight = $this->drawFieldBlock($pdf, $rightColX, $yRight, $colW, 'Instrumen Pengujian', $tiket->instrumen ?: '-', $ink, $muted, 8.5);
        $yRight = $this->drawFieldBlock($pdf, $rightColX, $yRight, $colW, 'Lokasi Laboratorium', $tiket->laboratorium ?: '-', $ink, $muted, 8.5);
        $yRight = $this->drawFieldBlock($pdf, $rightColX, $yRight, $colW, 'Rencana Tanggal Pengujian', $tiket->tanggal_uji ? $tiket->tanggal_uji->translatedFormat('d F Y') : '-', $ink, $muted, 8.5);

        // 5. Ringkasan Karakteristik & Tujuan Pengujian (Minimalist Left-Accent Block)
        $notesTopY = min($yLeft, $yRight) - 10.0;
        $descLines = $this->wrapLines($tiket->deskripsi_sampel ?: '-', 96, 2);
        $tujuanLines = $this->wrapLines($tiket->tujuan_pengujian ?: '-', 96, 2);
        $cloudLines = ($isDigital && $tiket->tautan_file_cloud)
            ? $this->wrapLines($tiket->tautan_file_cloud, 96, 2)
            : [];

        $notesHeight = 14.0
            + 12.0 + (count($descLines) * 11.5)
            + 6.0 + 12.0 + (count($tujuanLines) * 11.5)
            + (!empty($cloudLines) ? (6.0 + 12.0 + (count($cloudLines) * 11.0)) : 0.0)
            + 6.0;

        $notesBottomY = $notesTopY - $notesHeight;
        $pdf->drawRect($margin, $notesBottomY, $contentWidth, $notesHeight, $surface, true);
        $pdf->drawRect($margin, $notesBottomY, 2.5, $notesHeight, $flame, true);

        $yCursor = $notesTopY - 14.0;
        $pdf->addText($margin + 14.0, $yCursor, 'KARAKTERISTIK & DESKRIPSI SAMPEL', 'Helvetica-Bold', 7, $muted);
        $yCursor -= 12.0;
        foreach ($descLines as $textLine) {
            $pdf->addText($margin + 14.0, $yCursor, $textLine, 'Helvetica', 8.5, $body);
            $yCursor -= 11.5;
        }

        $yCursor -= 6.0;
        $pdf->addText($margin + 14.0, $yCursor, 'TUJUAN PENGUJIAN RISET', 'Helvetica-Bold', 7, $muted);
        $yCursor -= 12.0;
        foreach ($tujuanLines as $textLine) {
            $pdf->addText($margin + 14.0, $yCursor, $textLine, 'Helvetica', 8.5, $body);
            $yCursor -= 11.5;
        }

        if (!empty($cloudLines)) {
            $yCursor -= 6.0;
            $pdf->addText($margin + 14.0, $yCursor, 'TAUTAN BERKAS CLOUD', 'Helvetica-Bold', 7, $muted);
            $yCursor -= 12.0;
            foreach ($cloudLines as $textLine) {
                $pdf->addText($margin + 14.0, $yCursor, $textLine, 'Courier', 8, $red);
                $yCursor -= 11.0;
            }
        }

        // 6. Ketentuan Layanan & Logistik Ringkas
        $sopTopY = $notesBottomY - 24.0;
        $pdf->addText($margin, $sopTopY, '03  KETENTUAN LAYANAN & LOGISTIK', 'Helvetica-Bold', 8, $ink);
        $pdf->drawHLine($margin, $sopTopY - 6.0, $contentWidth, $line, 0.75);

        $ketentuan = [
            '1. Layanan pengujian laboratorium ini bebas biaya bagi seluruh sivitas akademika Telkom University.',
            '2. Khusus spesimen fisik, cetak dan tempelkan resi ini pada wadah sampel sebelum diserahkan ke Laboran (maks. 3x24 jam).',
            '3. Sisa spesimen fisik wajib diambil maksimal 7 (tujuh) hari kerja setelah Laporan Hasil Uji (LHU) diterbitkan.',
            '4. Progres pengujian dapat dipantau secara real-time melalui Visual Timeline Tracking pada portal Telyuji.',
        ];

        $ySop = $sopTopY - 21.0;
        foreach ($ketentuan as $poin) {
            $pdf->addText($margin, $ySop, $poin, 'Helvetica', 8, $body);
            $ySop -= 14.0;
        }

        // 7. Area Pengesahan Minimalis (Garis Tanda Tangan Terbuka, Tanpa Kotak Berat)
        $sigTopY = min($ySop - 18.0, 214.0);
        $pdf->drawDashedHLine($margin, $sigTopY + 12.0, $contentWidth, $line, 0.75);

        $sigLineW = 190.0;
        $leftSigX = $margin + 20.0;
        $rightSigX = $rightEdge - $sigLineW - 20.0;

        // Kiri: Pemohon
        $pdf->addTextCentered($leftSigX, $sigLineW, $sigTopY - 8.0, 'Pemohon Pengujian', 'Helvetica-Bold', 8.5, $ink);
        $pdf->addTextCentered($leftSigX, $sigLineW, $sigTopY - 20.0, $isDigital ? 'Tanda tangan & tanggal pengajuan' : 'Tanda tangan & tanggal penyerahan', 'Helvetica', 7.5, $muted);
        $pdf->drawHLine($leftSigX, $sigTopY - 72.0, $sigLineW, $subtle, 0.75);
        $pdf->addTextCentered($leftSigX, $sigLineW, $sigTopY - 86.0, $tiket->nama_pemohon ?: '-', 'Helvetica-Bold', 8.5, $ink);
        $pdf->addTextCentered($leftSigX, $sigLineW, $sigTopY - 97.0, 'NIM / NIP: ' . ($tiket->nim_nip ?: '-'), 'Courier', 7.5, $muted);

        // Kanan: Laboran
        $pdf->addTextCentered($rightSigX, $sigLineW, $sigTopY - 8.0, $isDigital ? 'Petugas Laboran Verifikator' : 'Petugas Laboran Penerima', 'Helvetica-Bold', 8.5, $ink);
        $pdf->addTextCentered($rightSigX, $sigLineW, $sigTopY - 20.0, $isDigital ? 'Verifikasi berkas & persetujuan lab' : 'Paraf & stempel penerimaan fisik', 'Helvetica', 7.5, $muted);
        $pdf->drawHLine($rightSigX, $sigTopY - 72.0, $sigLineW, $subtle, 0.75);
        $pdf->addTextCentered($rightSigX, $sigLineW, $sigTopY - 86.0, 'Tim Laboran Telkom University', 'Helvetica-Bold', 8.5, $ink);
        $pdf->addTextCentered($rightSigX, $sigLineW, $sigTopY - 97.0, $this->truncate($tiket->laboratorium ?: 'Laboratorium Riset Terpadu', 46), 'Helvetica', 7.5, $muted);

        // 8. Footer Minimalis & Token Verifikasi Terintegrasi
        $token = substr($tiket->barcode_token ?: strtoupper(hash('crc32b', $tiket->nomor_tiket . 'TELYUJI')), 0, 16);
        $timestamp = now()->translatedFormat('d M Y, H:i') . ' WIB';

        $pdf->drawHLine($margin, 68.0, $contentWidth, $line, 0.6);
        $pdf->addText($margin, 55.0, 'Telyuji  |  Laboratorium Riset Terpadu Telkom University, Bandung', 'Helvetica-Bold', 7, $muted);
        $pdf->addText($margin, 44.0, "Diterbitkan otomatis oleh sistem pada {$timestamp}. Dokumen sah tanpa cap basah untuk keperluan pelacakan.", 'Helvetica', 6.8, $subtle);
        $pdf->addTextRight($rightEdge, 55.0, "TOKEN: {$token}", 'Courier-Bold', 7.5, $body);
        $pdf->addTextRight($rightEdge, 44.0, 'TERVERIFIKASI DIGITAL', 'Helvetica', 6.8, $subtle);

        return $pdf->output();
    }

    /**
     * Gambar baris barcode vektor langsung ke dalam canvas PDF.
     */
    private function drawBarcodeVector(PurePdfBuilder $pdf, string $text, float $x, float $y, float $width, float $height, array $color): void
    {
        $binary = $this->barcodeService->toBinaryString($text);
        $len = strlen($binary);
        if ($len === 0) {
            return;
        }

        $barWidth = $width / $len;

        $i = 0;
        while ($i < $len) {
            if ($binary[$i] === '1') {
                $count = 1;
                while ($i + 1 < $len && $binary[$i + 1] === '1') {
                    $count++;
                    $i++;
                }
                $pdf->drawRect($x, $y, $count * $barWidth, $height, $color, true);
                $x += $count * $barWidth;
            } else {
                $x += $barWidth;
            }
            $i++;
        }
    }

    /**
     * Tulis pasangan label mikro + nilai terbungkus rapi dalam blok kolom.
     */
    private function drawFieldBlock(
        PurePdfBuilder $pdf,
        float $x,
        float $y,
        float $colWidth,
        string $label,
        string $value,
        array $inkColor,
        array $mutedColor,
        float $valueSize = 8.5,
        string $valueFont = 'Helvetica-Bold'
    ): float {
        $pdf->addText($x, $y, $label, 'Helvetica', 7.2, $mutedColor);
        $maxChars = max(24, (int) floor($colWidth / ($valueSize * 0.56)));
        $lines = $this->wrapLines($value, $maxChars, 2);

        $lineY = $y - 12.0;
        foreach ($lines as $lineText) {
            $pdf->addText($x, $lineY, $lineText, $valueFont, $valueSize, $inkColor);
            $lineY -= ($valueSize + 3.0);
        }

        return $lineY - 6.0;
    }

    /**
     * Pecah teks panjang menjadi maksimal $maxLines baris tanpa melampaui margin.
     *
     * @return array<int, string>
     */
    private function wrapLines(string $text, int $maxChars, int $maxLines = 2): array
    {
        $clean = trim(preg_replace('/\s+/', ' ', $text) ?? $text);
        if ($clean === '') {
            return ['-'];
        }

        $wrapped = explode("\n", wordwrap($clean, $maxChars, "\n", true));
        if (count($wrapped) <= $maxLines) {
            return $wrapped;
        }

        $lines = array_slice($wrapped, 0, $maxLines);
        $lastIdx = $maxLines - 1;
        $lines[$lastIdx] = rtrim(substr($lines[$lastIdx], 0, max(1, $maxChars - 3))) . '...';

        return $lines;
    }

    private function truncate(string $text, int $maxChars): string
    {
        $clean = trim($text);
        if (strlen($clean) <= $maxChars) {
            return $clean;
        }

        return rtrim(substr($clean, 0, max(1, $maxChars - 3))) . '...';
    }
}

/**
 * PurePdfBuilder
 *
 * Engine pembangun PDF 1.4 murni yang memproduksi berkas PDF biner valid
 * dengan dukungan tipografi, garis vektor, serta penyematan gambar PNG 8-bit
 * (FlateDecode RGB XObject) tanpa ketergantungan library/ekstensi khusus.
 */
class PurePdfBuilder
{
    private $width = 595.28;
    private $height = 841.89;
    private $streamContent = '';

    /** @var array<string, array{width:int,height:int,data:string}> */
    private $images = [];

    public function setPageSize(float $width, float $height): void
    {
        $this->width = $width;
        $this->height = $height;
    }

    public function addText(float $x, float $y, string $text, string $font = 'Helvetica', float $size = 10, array $color = [0, 0, 0]): void
    {
        $r = number_format($color[0], 3, '.', '');
        $g = number_format($color[1], 3, '.', '');
        $b = number_format($color[2], 3, '.', '');

        $encoded = $this->encodePdfText($text);
        $escaped = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $encoded);

        $this->streamContent .= "BT\n";
        $this->streamContent .= "/{$font} {$size} Tf\n";
        $this->streamContent .= "{$r} {$g} {$b} rg\n";
        $this->streamContent .= sprintf("%.2f %.2f Td\n", $x, $y);
        $this->streamContent .= "({$escaped}) Tj\n";
        $this->streamContent .= "ET\n";
    }

    public function addTextRight(float $rightX, float $y, string $text, string $font = 'Helvetica', float $size = 10, array $color = [0, 0, 0]): void
    {
        $textWidth = $this->estimateTextWidth($text, $font, $size);
        $this->addText($rightX - $textWidth, $y, $text, $font, $size, $color);
    }

    public function addTextCentered(float $boxX, float $boxWidth, float $y, string $text, string $font = 'Helvetica', float $size = 10, array $color = [0, 0, 0]): void
    {
        $textWidth = $this->estimateTextWidth($text, $font, $size);
        $startX = $boxX + max(0.0, ($boxWidth - $textWidth) / 2);
        $this->addText($startX, $y, $text, $font, $size, $color);
    }

    public function drawRect(float $x, float $y, float $w, float $h, array $color, bool $fill = true, float $lineWidth = 1): void
    {
        $r = number_format($color[0], 3, '.', '');
        $g = number_format($color[1], 3, '.', '');
        $b = number_format($color[2], 3, '.', '');

        if ($fill) {
            $this->streamContent .= "{$r} {$g} {$b} rg\n";
            $this->streamContent .= sprintf("%.2f %.2f %.2f %.2f re f\n", $x, $y, $w, $h);
        } else {
            $this->streamContent .= sprintf("%.2f w\n", $lineWidth);
            $this->streamContent .= "{$r} {$g} {$b} RG\n";
            $this->streamContent .= sprintf("%.2f %.2f %.2f %.2f re S\n", $x, $y, $w, $h);
        }
    }

    public function drawHLine(float $x, float $y, float $w, array $color, float $lineWidth = 1): void
    {
        $r = number_format($color[0], 3, '.', '');
        $g = number_format($color[1], 3, '.', '');
        $b = number_format($color[2], 3, '.', '');
        $x2 = $x + $w;

        $this->streamContent .= sprintf("%.2f w\n", $lineWidth);
        $this->streamContent .= "{$r} {$g} {$b} RG\n";
        $this->streamContent .= sprintf("%.2f %.2f m %.2f %.2f l S\n", $x, $y, $x2, $y);
    }

    public function drawDashedHLine(float $x, float $y, float $w, array $color, float $lineWidth = 0.75): void
    {
        $r = number_format($color[0], 3, '.', '');
        $g = number_format($color[1], 3, '.', '');
        $b = number_format($color[2], 3, '.', '');
        $x2 = $x + $w;

        $this->streamContent .= "q\n";
        $this->streamContent .= "[4 3] 0 d\n";
        $this->streamContent .= sprintf("%.2f w\n", $lineWidth);
        $this->streamContent .= "{$r} {$g} {$b} RG\n";
        $this->streamContent .= sprintf("%.2f %.2f m %.2f %.2f l S\n", $x, $y, $x2, $y);
        $this->streamContent .= "Q\n";
    }

    public function drawVLine(float $x, float $y, float $h, array $color, float $lineWidth = 1): void
    {
        $r = number_format($color[0], 3, '.', '');
        $g = number_format($color[1], 3, '.', '');
        $b = number_format($color[2], 3, '.', '');
        $y2 = $y + $h;

        $this->streamContent .= sprintf("%.2f w\n", $lineWidth);
        $this->streamContent .= "{$r} {$g} {$b} RG\n";
        $this->streamContent .= sprintf("%.2f %.2f m %.2f %.2f l S\n", $x, $y, $x, $y2);
    }

    /**
     * Sematkan gambar PNG 8-bit (RGB/RGBA) langsung ke PDF dengan komposisi latar putih bersih.
     */
    public function drawPngImage(string $filePath, float $x, float $y, float $width, float $height): bool
    {
        if (!is_file($filePath) || !function_exists('gzuncompress') || !function_exists('gzcompress')) {
            return false;
        }

        $decoded = $this->decodePngToRgbOnWhite($filePath);
        if ($decoded === null) {
            return false;
        }

        $imgName = 'Im' . (count($this->images) + 1);
        $this->images[$imgName] = $decoded;

        $this->streamContent .= "q\n";
        $this->streamContent .= sprintf("%.2f 0 0 %.2f %.2f %.2f cm\n", $width, $height, $x, $y);
        $this->streamContent .= "/{$imgName} Do\n";
        $this->streamContent .= "Q\n";

        return true;
    }

    /**
     * Dekoder PNG murni (tanpa ekstensi GD) untuk 8-bit RGB (type 2) & RGBA (type 6) non-interlaced.
     *
     * @return array{width:int,height:int,data:string}|null
     */
    private function decodePngToRgbOnWhite(string $filePath): ?array
    {
        $raw = @file_get_contents($filePath);
        if ($raw === false || strlen($raw) < 33 || substr($raw, 0, 8) !== "\x89PNG\r\n\x1a\n") {
            return null;
        }

        $offset = 8;
        $totalLen = strlen($raw);
        $imgW = 0;
        $imgH = 0;
        $bitDepth = 0;
        $colorType = 0;
        $interlace = 0;
        $idat = '';

        while ($offset + 8 <= $totalLen) {
            $chunkLen = unpack('N', substr($raw, $offset, 4))[1];
            $chunkType = substr($raw, $offset + 4, 4);
            $chunkData = substr($raw, $offset + 8, $chunkLen);
            $offset += 12 + $chunkLen;

            if ($chunkType === 'IHDR' && strlen($chunkData) >= 13) {
                $hdr = unpack('Nw/Nh/Cdepth/Ccolor/Ccomp/Cfilter/Cinterlace', $chunkData);
                $imgW = (int) $hdr['w'];
                $imgH = (int) $hdr['h'];
                $bitDepth = (int) $hdr['depth'];
                $colorType = (int) $hdr['color'];
                $interlace = (int) $hdr['interlace'];
            } elseif ($chunkType === 'IDAT') {
                $idat .= $chunkData;
            } elseif ($chunkType === 'IEND') {
                break;
            }
        }

        if ($imgW <= 0 || $imgH <= 0 || $bitDepth !== 8 || $interlace !== 0 || !in_array($colorType, [2, 6], true) || $idat === '') {
            return null;
        }

        $inflated = @gzuncompress($idat);
        if ($inflated === false) {
            return null;
        }

        $bpp = $colorType === 6 ? 4 : 3;
        $stride = $imgW * $bpp;
        if (strlen($inflated) < ($stride + 1) * $imgH) {
            return null;
        }

        $rgbOut = '';
        $prevRow = str_repeat("\x00", $stride);
        $pos = 0;

        for ($row = 0; $row < $imgH; $row++) {
            $filter = ord($inflated[$pos]);
            $pos++;
            $rawRow = substr($inflated, $pos, $stride);
            $pos += $stride;

            $recon = $this->unfilterPngScanline($filter, $rawRow, $prevRow, $bpp);
            $prevRow = $recon;

            if ($colorType === 2) {
                $rgbOut .= $recon;
                continue;
            }

            // Composite RGBA ke latar putih murni (#FFFFFF)
            for ($p = 0; $p < $stride; $p += 4) {
                $r = ord($recon[$p]);
                $g = ord($recon[$p + 1]);
                $b = ord($recon[$p + 2]);
                $a = ord($recon[$p + 3]);

                if ($a === 255) {
                    $rgbOut .= chr($r) . chr($g) . chr($b);
                } elseif ($a === 0) {
                    $rgbOut .= "\xff\xff\xff";
                } else {
                    $inv = 255 - $a;
                    $rgbOut .= chr((int) (($r * $a + 255 * $inv) / 255))
                        . chr((int) (($g * $a + 255 * $inv) / 255))
                        . chr((int) (($b * $a + 255 * $inv) / 255));
                }
            }
        }

        $compressed = gzcompress($rgbOut, 6);
        if ($compressed === false) {
            return null;
        }

        return [
            'width' => $imgW,
            'height' => $imgH,
            'data' => $compressed,
        ];
    }

    private function unfilterPngScanline(int $filter, string $rawRow, string $prevRow, int $bpp): string
    {
        if ($filter === 0) {
            return $rawRow;
        }

        $len = strlen($rawRow);
        $out = str_repeat("\x00", $len);

        for ($i = 0; $i < $len; $i++) {
            $x = ord($rawRow[$i]);
            $a = $i >= $bpp ? ord($out[$i - $bpp]) : 0;
            $b = ord($prevRow[$i]);
            $c = $i >= $bpp ? ord($prevRow[$i - $bpp]) : 0;

            if ($filter === 1) {
                $val = ($x + $a) & 0xff;
            } elseif ($filter === 2) {
                $val = ($x + $b) & 0xff;
            } elseif ($filter === 3) {
                $val = ($x + (($a + $b) >> 1)) & 0xff;
            } elseif ($filter === 4) {
                $p = $a + $b - $c;
                $pa = abs($p - $a);
                $pb = abs($p - $b);
                $pc = abs($p - $c);
                $pr = ($pa <= $pb && $pa <= $pc) ? $a : ($pb <= $pc ? $b : $c);
                $val = ($x + $pr) & 0xff;
            } else {
                $val = $x;
            }

            $out[$i] = chr($val);
        }

        return $out;
    }

    /**
     * Konversi string UTF-8 ke WinAnsiEncoding agar aman di font Type1 standar PDF.
     */
    private function encodePdfText(string $text): string
    {
        if (function_exists('iconv')) {
            $converted = @iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $text);
            if ($converted !== false) {
                return $converted;
            }
        }

        return preg_replace('/[^\x20-\x7E]/', ' ', $text) ?? $text;
    }

    /**
     * Estimasi presisi lebar teks (dalam poin) untuk perataan kanan & tengah pada Helvetica/Courier.
     */
    private function estimateTextWidth(string $text, string $font, float $size): float
    {
        $encoded = $this->encodePdfText($text);
        if (strpos($font, 'Courier') === 0) {
            return strlen($encoded) * $size * 0.6;
        }

        $isBold = strpos($font, 'Bold') !== false;
        $totalUnits = 0.0;
        $len = strlen($encoded);

        for ($i = 0; $i < $len; $i++) {
            $ch = $encoded[$i];
            if (strpos("ilI.,:;!'| ", $ch) !== false) {
                $totalUnits += $isBold ? 0.30 : 0.28;
            } elseif (strpos('fjrt()-/[]', $ch) !== false) {
                $totalUnits += $isBold ? 0.37 : 0.35;
            } elseif (strpos('mwMW@%', $ch) !== false) {
                $totalUnits += $isBold ? 0.89 : 0.85;
            } elseif ($ch >= 'A' && $ch <= 'Z') {
                $totalUnits += $isBold ? 0.73 : 0.71;
            } elseif ($ch >= '0' && $ch <= '9') {
                $totalUnits += 0.56;
            } else {
                $totalUnits += $isBold ? 0.57 : 0.53;
            }
        }

        return $totalUnits * $size;
    }

    /**
     * Rakit seluruh struktur objek PDF 1.4 dan hasilkan dokumen PDF biner final.
     */
    public function output(): string
    {
        $offsets = [];
        $buf = "%PDF-1.4\n%\xe2\xe3\xcf\xd3\n";

        // Peta nomor objek untuk Image XObjects (mulai dari objek 9)
        $imageObjMap = [];
        $nextObj = 9;
        foreach (array_keys($this->images) as $imgName) {
            $imageObjMap[$imgName] = $nextObj++;
        }

        $xObjectResource = '';
        if (!empty($imageObjMap)) {
            $entries = [];
            foreach ($imageObjMap as $imgName => $objId) {
                $entries[] = "/{$imgName} {$objId} 0 R";
            }
            $xObjectResource = ' /XObject << ' . implode(' ', $entries) . ' >>';
        }

        // Objek 1: Catalog
        $offsets[1] = strlen($buf);
        $buf .= "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";

        // Objek 2: Pages
        $offsets[2] = strlen($buf);
        $buf .= "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";

        // Objek 3: Page tunggal
        $offsets[3] = strlen($buf);
        $buf .= sprintf(
            "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 %.2f %.2f] /Contents 4 0 R /Resources << /Font << /Helvetica 5 0 R /Helvetica-Bold 6 0 R /Courier 7 0 R /Courier-Bold 8 0 R >>%s >> >>\nendobj\n",
            $this->width,
            $this->height,
            $xObjectResource
        );

        // Objek 4: Stream Konten Grafis
        $offsets[4] = strlen($buf);
        $streamLen = strlen($this->streamContent);
        $buf .= "4 0 obj\n<< /Length {$streamLen} >>\nstream\n{$this->streamContent}\nendstream\nendobj\n";

        // Objek 5-8: Standard Type1 Fonts (WinAnsiEncoding)
        $fonts = [
            5 => 'Helvetica',
            6 => 'Helvetica-Bold',
            7 => 'Courier',
            8 => 'Courier-Bold',
        ];

        foreach ($fonts as $num => $fontName) {
            $offsets[$num] = strlen($buf);
            $buf .= "{$num} 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /{$fontName} /Encoding /WinAnsiEncoding >>\nendobj\n";
        }

        // Objek 9+: Embedded PNG Images (FlateDecode RGB XObject)
        foreach ($imageObjMap as $imgName => $objId) {
            $img = $this->images[$imgName];
            $imgLen = strlen($img['data']);
            $offsets[$objId] = strlen($buf);
            $buf .= "{$objId} 0 obj\n<< /Type /XObject /Subtype /Image /Width {$img['width']} /Height {$img['height']} /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /FlateDecode /Length {$imgLen} >>\nstream\n{$img['data']}\nendstream\nendobj\n";
        }

        $totalObjects = $nextObj; // Termasuk objek 0

        // XREF Table
        $xrefOffset = strlen($buf);
        $buf .= "xref\n0 {$totalObjects}\n";
        $buf .= "0000000000 65535 f \n";
        for ($i = 1; $i < $totalObjects; $i++) {
            $buf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }

        // Trailer
        $buf .= "trailer\n<< /Size {$totalObjects} /Root 1 0 R >>\n";
        $buf .= "startxref\n{$xrefOffset}\n%%EOF\n";

        return $buf;
    }
}
