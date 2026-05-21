<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['nullable', 'string', 'max:30'],
            'department' => ['nullable', 'string', 'max:120'],
            'role' => ['required', Rule::in(['admin', 'jpj_officer', 'health_officer'])],
            'is_active' => ['nullable', 'boolean'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }
}
