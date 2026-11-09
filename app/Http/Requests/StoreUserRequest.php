<?php

namespace App\Http\Requests;

use App\Http\Controllers\Auth\Concerns\ValidatesTelkomCampusAuth;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    use ValidatesTelkomCampusAuth;

    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => $this->telkomEmailRules(['max:255', 'unique:users,email']),
            'role' => ['required', 'string', Rule::in(User::ALLOWED_ROLES)],
            'password' => $this->strongPasswordRules(),
        ];
    }

    public function messages(): array
    {
        return array_merge(
            $this->telkomAuthValidationMessages('email', true),
            [
                'email.unique' => 'Alamat email ini sudah terdaftar.',
                'role.in' => 'Peran pengguna tidak valid.',
            ]
        );
    }
}
