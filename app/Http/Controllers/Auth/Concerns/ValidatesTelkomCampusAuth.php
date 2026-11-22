<?php

namespace App\Http\Controllers\Auth\Concerns;

trait ValidatesTelkomCampusAuth
{
    public const TELKOM_CAMPUS_EMAIL_REGEX = '/^[a-zA-Z0-9._%+-]+@(?:[a-zA-Z0-9-]+\.)*telkomuniversity\.ac\.id$/i';
    public const TELKOM_STUDENT_EMAIL_REGEX = self::TELKOM_CAMPUS_EMAIL_REGEX;

    /**
     * Aturan validasi email civitas akademika Telkom University (@telkomuniversity.ac.id beserta subdomainnya).
     */
    protected function telkomEmailRules(array $extraRules = []): array
    {
        return array_merge([
            'required',
            'string',
            'email',
            'regex:' . self::TELKOM_CAMPUS_EMAIL_REGEX,
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
            $emailField . '.regex' => 'Alamat email wajib menggunakan domain @telkomuniversity.ac.id.',
        ];

        if ($includePassword) {
            $messages['password.min'] = 'Kata sandi minimal harus 8 karakter.';
            $messages['password.regex'] = 'Kata sandi wajib mengandung kombinasi huruf kecil, huruf besar, dan simbol.';
        }

        return $messages;
    }
}
