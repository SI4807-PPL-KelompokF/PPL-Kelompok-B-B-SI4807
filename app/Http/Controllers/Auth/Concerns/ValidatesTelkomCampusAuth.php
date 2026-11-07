<?php

namespace App\Http\Controllers\Auth\Concerns;

trait ValidatesTelkomCampusAuth
{
    public const TELKOM_STUDENT_EMAIL_REGEX = '/^[a-zA-Z0-9._%+-]+@student\.telkomuniversity\.ac\.id$/i';

    /**
     * Aturan validasi email kampus Telkom University (@student.telkomuniversity.ac.id).
     */
    protected function telkomEmailRules(array $extraRules = []): array
    {
        return array_merge([
            'required',
            'string',
            'email',
            'regex:' . self::TELKOM_STUDENT_EMAIL_REGEX,
        ], $extraRules);
    }

    /**
     * Aturan validasi kata sandi kuat (minimal 8 karakter, huruf kecil, huruf besar, simbol).
     */
    protected function strongPasswordRules(): array
    {
        return [
            'required',
            'string',
            'min:8',
            'regex:/[a-z]/',
            'regex:/[A-Z]/',
            'regex:/[^a-zA-Z0-9]/',
            'confirmed',
        ];
    }

    /**
     * Pesan kesalahan validasi standar berbahasa Indonesia untuk autentikasi Telyuji.
     */
    protected function telkomAuthValidationMessages(string $emailField = 'email', bool $includePassword = false): array
    {
        $messages = [
            $emailField . '.regex' => 'Alamat email wajib menggunakan domain @student.telkomuniversity.ac.id.',
        ];

        if ($includePassword) {
            $messages['password.min'] = 'Kata sandi minimal harus 8 karakter.';
            $messages['password.regex'] = 'Kata sandi wajib mengandung kombinasi huruf kecil, huruf besar, dan simbol.';
        }

        return $messages;
    }
}
