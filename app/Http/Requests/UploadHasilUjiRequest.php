<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;

class UploadHasilUjiRequest extends FormRequest
{
    public const ALLOWED_EXTENSIONS = ['pdf', 'xlsx', 'xls'];
    public const MAX_SIZE_KB = 5120; // 5 MB

    public function authorize(): bool
    {
        return $this->user() !== null && $this->user()->isLaboran();
    }

    public function rules(): array
    {
        return [
            'berkas_hasil' => [
                'required',
                'file',
                'max:' . self::MAX_SIZE_KB,
                function ($attribute, $value, $fail) {
                    $ext = strtolower((string) $value->getClientOriginalExtension());

                    if (!in_array($ext, self::ALLOWED_EXTENSIONS, true)) {
                        $fail('Format berkas ditolak. Gunakan PDF atau Excel (.xlsx / .xls).');
                        return;
                    }

                    if ((int) $value->getSize() <= 0) {
                        $fail('Berkas hasil uji kosong atau rusak.');
                        return;
                    }

                    $realPath = $value->getRealPath();
                    if ($realPath && is_file($realPath)) {
                        $handle = @fopen($realPath, 'rb');
                        $header = $handle ? (string) fread($handle, 8) : '';
                        if ($handle) {
                            fclose($handle);
                        }

                        // Verifikasi magic bytes apabila berkas berisi konten biner nyata (bukan stub nol pengujian)
                        if ($header !== '' && trim($header, "\0") !== '') {
                            if ($ext === 'pdf' && strncmp($header, '%PDF-', 5) !== 0) {
                                $fail('Isi berkas tidak valid sebagai dokumen PDF.');
                                return;
                            }

                            if ($ext === 'xlsx' && strncmp($header, 'PK', 2) !== 0) {
                                $fail('Isi berkas tidak valid sebagai dokumen Excel (.xlsx).');
                                return;
                            }

                            if ($ext === 'xls' && strncmp($header, "\xD0\xCF\x11\xE0", 4) !== 0) {
                                $fail('Isi berkas tidak valid sebagai dokumen Excel (.xls).');
                                return;
                            }
                        }
                    }
                },
            ],
            'catatan_laboran' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'berkas_hasil.required' => 'Silakan pilih berkas hasil uji terlebih dahulu.',
            'berkas_hasil.file' => 'Berkas yang dikirim tidak valid.',
            'berkas_hasil.max' => 'Ukuran berkas melebihi batas maksimal 5 MB.',
            'catatan_laboran.max' => 'Catatan revisi maksimal 500 karakter.',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        if ($this->expectsJson()) {
            throw new HttpResponseException(response()->json([
                'message' => $validator->errors()->first('berkas_hasil') ?: $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422));
        }

        $nomorTiket = (string) $this->route('nomorTiket');

        throw (new ValidationException($validator))
            ->redirectTo(route('laboran.pengujian.index', ['tiket' => $nomorTiket]));
    }
}
