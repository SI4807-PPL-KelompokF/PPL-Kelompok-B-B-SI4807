<?php

namespace App\Http\Requests;

use App\Http\Controllers\Auth\Concerns\ValidatesTelkomCampusAuth;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    use ValidatesTelkomCampusAuth;

    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $targetUser = $this->route('user');
        $targetId = $targetUser instanceof User ? $targetUser->id : $targetUser;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => $this->telkomEmailRules([
                'max:255',
                Rule::unique('users', 'email')->ignore($targetId),
            ]),
            'role' => ['required', 'string', Rule::in(User::ALLOWED_ROLES)],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[^a-zA-Z0-9]/',
                'confirmed',
            ],
        ];
    }

    public function messages(): array
    {
        return array_merge(
            $this->telkomAuthValidationMessages('email', true),
            [
                'email.unique' => 'Alamat email ini sudah digunakan oleh pengguna lain.',
                'role.in' => 'Peran pengguna tidak valid.',
            ]
        );
    }
}
