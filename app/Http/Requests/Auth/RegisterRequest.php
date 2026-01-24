<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('auth.name.required'),
            'email.required' => __('auth.email.required'),
            'email.unique' => __('auth.email.unique'),
            'password.required' => __('auth.password.required'),
            'password.confirmed' => __('auth.password.confirmed'),
            'position_id.required' => __('auth.position_id.required'),
            'position_id.exists' => __('auth.position_id.exists'),
        ];
    }
}
