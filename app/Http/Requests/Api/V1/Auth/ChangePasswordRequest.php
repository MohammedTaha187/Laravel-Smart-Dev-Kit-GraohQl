<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed',
            'new_password_confirmation' => 'required|string',
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'current_password' => [
                'description' => 'The user\'s current password.',
                'example' => 'oldpassword123',
            ],
            'new_password' => [
                'description' => 'The new password to set (min 6 chars).',
                'example' => 'newpassword123',
            ],
            'new_password_confirmation' => [
                'description' => 'Must match the new password.',
                'example' => 'newpassword123',
            ],
        ];
    }
}
