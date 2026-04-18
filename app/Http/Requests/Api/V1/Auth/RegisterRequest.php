<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|string',
            'phone' => 'required|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'required|date',
            'language_preference' => 'required|string|max:255',
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'The user\'s full name.',
                'example' => 'Test User',
            ],
            'email' => [
                'description' => 'Unique email address.',
                'example' => 'test@example.com',
            ],
            'password' => [
                'description' => 'Secure password (min 6 chars).',
                'example' => 'secret123',
            ],
            'password_confirmation' => [
                'description' => 'Must match the password field.',
                'example' => 'secret123',
            ],
            'phone' => [
                'description' => 'Contact phone number.',
                'example' => '+123456789',
            ],
            'avatar' => [
                'description' => 'Profile image file (jpeg, png, jpg, gif).',
                'example' => null,
            ],
            'gender' => [
                'description' => 'User gender.',
                'example' => 'male',
            ],
            'date_of_birth' => [
                'description' => 'User date of birth.',
                'example' => '1995-05-15',
            ],
            'language_preference' => [
                'description' => 'Preferred language code.',
                'example' => 'en',
            ],
        ];
    }
}
