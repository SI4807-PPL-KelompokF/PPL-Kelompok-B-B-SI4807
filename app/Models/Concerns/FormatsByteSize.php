<?php

namespace App\Models\Concerns;

trait FormatsByteSize
{
    /**
     * Format ukuran byte menjadi representasi yang mudah dibaca (KB atau MB).
     */
    public function getUkuranFormattedAttribute(): string
    {
        $bytes = (int) ($this->ukuran_bytes ?? 0);

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2, ',', '.') . ' MB';
        }

        return max(1, (int) round($bytes / 1024)) . ' KB';
    }
}
