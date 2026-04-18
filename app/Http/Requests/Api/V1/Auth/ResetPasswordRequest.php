<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string',
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'email' => [
                'description' => 'User email address associated with the account.',
                'example' => 'test@example.com',
            ],
            'token' => [
                'description' => 'The 6-digit password reset token received via email.',
                'example' => '123456',
            ],
            'password' => [
                'description' => 'New password (min 6 chars).',
                'example' => 'newpassword123',
            ],
            'password_confirmation' => [
                'description' => 'Must match the new password.',
                'example' => 'newpassword123',
            ],
        ];
    }
}
