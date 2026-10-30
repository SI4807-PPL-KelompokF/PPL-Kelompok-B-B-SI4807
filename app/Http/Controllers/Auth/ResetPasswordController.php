<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => [
                'required',
                'email',
                'regex:/^[a-zA-Z0-9._%+-]+@student\.telkomuniversity\.ac\.id$/i',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[^a-zA-Z0-9]/',
                'confirmed',
            ],
        ];
    }

    /**
     * Get the password reset validation error messages.
     *
     * @return array
     */
    protected function validationErrorMessages()
    {
        return [
            'email.regex' => 'Alamat email wajib menggunakan domain @student.telkomuniversity.ac.id.',
            'password.min' => 'Kata sandi minimal harus 8 karakter.',
            'password.regex' => 'Kata sandi wajib mengandung kombinasi huruf kecil, huruf besar, dan simbol.',
        ];
    }
}
