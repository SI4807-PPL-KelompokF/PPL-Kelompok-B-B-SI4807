<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\Concerns\ValidatesTelkomCampusAuth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails, ValidatesTelkomCampusAuth;

    /**
     * Validate the email for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateEmail(Request $request)
    {
        $request->validate([
            'email' => $this->telkomEmailRules(),
        ], $this->telkomAuthValidationMessages('email'));
    }
}
