<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Concerns\ValidatesTelkomCampusAuth;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use AuthenticatesUsers, ValidatesTelkomCampusAuth;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Arahkan pengguna setelah login ke halaman sesuai perannya (Admin / Laboran / Pemohon).
     */
    public function redirectTo(): string
    {
        $user = Auth::user();

        return $user ? $user->homeRoute() : $this->redirectTo;
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $field = $this->username();

        $request->validate([
            $field => $this->telkomEmailRules(),
            'password' => ['required', 'string'],
        ], $this->telkomAuthValidationMessages($field));
    }
}
