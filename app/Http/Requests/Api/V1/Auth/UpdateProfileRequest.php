<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:255',
            'avatar' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'gender' => 'sometimes|in:male,female',
            'date_of_birth' => 'sometimes|date',
            'language_preference' => 'sometimes|string|max:2',
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'User\'s full name.',
                'example' => 'John Updated',
            ],
            'phone' => [
                'description' => 'User\'s contact phone number.',
                'example' => '+1234567890',
            ],
            'avatar' => [
                'description' => 'Profile image file.',
                'example' => null,
            ],
            'gender' => [
                'description' => 'User\'s gender.',
                'example' => 'female',
            ],
            'date_of_birth' => [
                'description' => 'User\'s date of birth.',
                'example' => '1992-08-20',
            ],
            'language_preference' => [
                'description' => 'Preferred language code (e.g., en, ar).',
                'example' => 'ar',
            ],
        ];
    }
}
