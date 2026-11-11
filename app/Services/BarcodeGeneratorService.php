<?php

namespace App\Services;

/**
 * BarcodeGeneratorService
 *
 * Menghasilkan barcode standar Code128 (Subset B) dalam format SVG vektor murni
 * tanpa memerlukan pustaka eksternal atau ekstensi GD/Imagick khusus.
 * Sangat cepat, tajam pada resolusi cetak apapun, dan kompatibel 100% dengan standar ISO/IEC 15417.
 */
class BarcodeGeneratorService
{
    /**
     * Pola pola Code 128 (lebar bar dan spasi) untuk subset B.
     * Format: string 6 angka yang merepresentasikan lebar baris gelap dan terang bergantian.
     */
    private const CODE128_PATTERNS = [
        '212222', '222122', '222221', '121223', '121322', '131222', '122213', '122312', '132212', '221213', // 0-9
        '221312', '231212', '112232', '122132', '122231', '113222', '123122', '123221', '223211', '221132', // 10-19
        '221231', '213212', '223112', '312131', '311222', '321122', '321221', '312212', '322112', '322211', // 20-29
        '212123', '212321', '232121', '111323', '131123', '131321', '112313', '132113', '132311', '211313', // 30-39
        '231113', '231311', '112133', '112331', '132131', '113123', '113321', '133121', '313121', '211331', // 40-49
        '231131', '213113', '213311', '213131', '311123', '311321', '331121', '312113', '312311', '332111', // 50-59
        '314111', '221411', '431111', '111224', '111422', '121124', '121421', '141122', '141221', '112214', // 60-69
        '112412', '122114', '122411', '142112', '142211', '241211', '221114', '413111', '241112', '134111', // 70-79
        '111242', '121142', '121241', '114212', '124112', '124211', '411212', '421112', '421211', '212141', // 80-89
        '214121', '412121', '111143', '111341', '131141', '114113', '114311', '411113', '411311', '113141', // 90-99
        '114131', '311141', '411131', '211412', '211214', '211232', '233111', '211133', // 100-107 (Start B = 104, Stop = 106)
    ];

    private const START_B = 104;
    private const STOP_CHAR = 106;

    /**
     * Encode string teks ke dalam format baris Code128 Subset B.
     */
    public function encode(string $text): array
    {
        $chars = str_split($text);
        $values = [];
        $checksum = self::START_B;

        // Start Code B
        $values[] = self::START_B;

        foreach ($chars as $idx => $char) {
            $val = ord($char) - 32;
            if ($val < 0 || $val > 95) {
                $val = 0; // Fallback untuk karakter yang tidak tercakup
            }
            $values[] = $val;
            $checksum += $val * ($idx + 1);
        }

        // Hitung Check Digit (Modulus 103)
        $checkDigit = $checksum % 103;
        $values[] = $checkDigit;

        // Stop Character
        $values[] = self::STOP_CHAR;

        return $values;
    }

    /**
     * Konversi nilai ter-encode menjadi urutan bar hitam/putih.
     * Mengembalikan string binary ('1' = bar hitam, '0' = spasi putih).
     */
    public function toBinaryString(string $text): string
    {
        $encoded = $this->encode($text);
        $binary = '';

        foreach ($encoded as $val) {
            $pattern = self::CODE128_PATTERNS[$val] ?? '212222';
            $bar = true;
            for ($i = 0; $i < strlen($pattern); $i++) {
                $width = (int) $pattern[$i];
                $binary .= str_repeat($bar ? '1' : '0', $width);
                $bar = !$bar;
            }
        }

        // Code 128 Stop pattern memiliki garis tebal 2 modul di akhir
        $binary .= '11';

        return $binary;
    }

    /**
     * Hasilkan SVG Barcode murni yang dapat disisipkan langsung ke HTML atau PDF.
     *
     * @param string $text Teks yang akan dijadikan barcode
     * @param int $width Lebar dasar (piksel)
     * @param int $height Tinggi bar (piksel)
     * @param bool $showLabel Apakah menampilkan teks di bawah barcode
     * @return string Kode SVG
     */
    public function getSvg(string $text, int $width = 300, int $height = 70, bool $showLabel = true): string
    {
        $binary = $this->toBinaryString($text);
        $moduleCount = strlen($binary);
        $quietZone = 10; // Margin hening kiri-kanan
        $totalModules = $moduleCount + ($quietZone * 2);

        $moduleWidth = $width / $totalModules;
        $svgHeight = $showLabel ? $height + 20 : $height;

        $rects = [];
        $currentX = $quietZone * $moduleWidth;

        $i = 0;
        while ($i < $moduleCount) {
            if ($binary[$i] === '1') {
                $barLength = 1;
                while ($i + 1 < $moduleCount && $binary[$i + 1] === '1') {
                    $barLength++;
                    $i++;
                }
                $barW = round($barLength * $moduleWidth, 3);
                $barX = round($currentX, 3);
                $rects[] = "<rect x=\"{$barX}\" y=\"0\" width=\"{$barW}\" height=\"{$height}\" fill=\"#111827\" />";
                $currentX += $barLength * $moduleWidth;
            } else {
                $currentX += $moduleWidth;
            }
            $i++;
        }

        $rectsString = implode("\n    ", $rects);

        $labelSvg = '';
        if ($showLabel) {
            $textEscaped = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
            $labelY = $height + 15;
            $centerX = $width / 2;
            $labelSvg = "<text x=\"{$centerX}\" y=\"{$labelY}\" font-family=\"'General Sans', monospace, sans-serif\" font-size=\"11\" font-weight=\"600\" fill=\"#111827\" text-anchor=\"middle\" letter-spacing=\"1.5\">* {$textEscaped} *</text>";
        }

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {$width} {$svgHeight}" width="{$width}" height="{$svgHeight}" style="background-color: transparent;">
    <rect width="100%" height="100%" fill="#ffffff" />
    {$rectsString}
    {$labelSvg}
</svg>
SVG;
    }

    /**
     * Hasilkan data URL base64 SVG untuk rendering instan pada tag <img>.
     */
    public function getDataUri(string $text, int $width = 300, int $height = 70, bool $showLabel = true): string
    {
        $svg = $this->getSvg($text, $width, $height, $showLabel);
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}
