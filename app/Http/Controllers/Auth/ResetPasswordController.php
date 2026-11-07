<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Concerns\ValidatesTelkomCampusAuth;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    use ResetsPasswords, ValidatesTelkomCampusAuth;

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
            'email' => $this->telkomEmailRules(),
            'password' => $this->strongPasswordRules(),
        ];
    }

    /**
     * Get the password reset validation error messages.
     *
     * @return array
     */
    protected function validationErrorMessages()
    {
        return $this->telkomAuthValidationMessages('email', true);
    }
}
