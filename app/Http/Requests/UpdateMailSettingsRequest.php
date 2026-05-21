<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMailSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return (bool) $this->user()?->hasRole('admin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'settings' => ['required', 'array'],
            'settings.active_mailer' => ['required', Rule::in(['log', 'smtp', 'gmail', 'outlook', 'mailtrap'])],

            'settings.smtp_scheme' => ['nullable', 'string', 'max:20'],
            'settings.smtp_host' => ['nullable', 'string', 'max:255'],
            'settings.smtp_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'settings.smtp_username' => ['nullable', 'string', 'max:255'],
            'settings.smtp_password' => ['nullable', 'string', 'max:255'],
            'settings.smtp_from_address' => ['nullable', 'email', 'max:255'],
            'settings.smtp_from_name' => ['nullable', 'string', 'max:255'],

            'settings.gmail_scheme' => ['nullable', 'string', 'max:20'],
            'settings.gmail_host' => ['nullable', 'string', 'max:255'],
            'settings.gmail_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'settings.gmail_username' => ['nullable', 'email', 'max:255'],
            'settings.gmail_app_password' => ['nullable', 'string', 'max:255'],
            'settings.gmail_from_address' => ['nullable', 'email', 'max:255'],
            'settings.gmail_from_name' => ['nullable', 'string', 'max:255'],

            'settings.outlook_scheme' => ['nullable', 'string', 'max:20'],
            'settings.outlook_host' => ['nullable', 'string', 'max:255'],
            'settings.outlook_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'settings.outlook_username' => ['nullable', 'string', 'max:255'],
            'settings.outlook_password' => ['nullable', 'string', 'max:255'],
            'settings.outlook_from_address' => ['nullable', 'email', 'max:255'],
            'settings.outlook_from_name' => ['nullable', 'string', 'max:255'],

            'settings.mailtrap_scheme' => ['nullable', 'string', 'max:20'],
            'settings.mailtrap_host' => ['nullable', 'string', 'max:255'],
            'settings.mailtrap_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'settings.mailtrap_username' => ['nullable', 'string', 'max:255'],
            'settings.mailtrap_password' => ['nullable', 'string', 'max:255'],
            'settings.mailtrap_from_address' => ['nullable', 'email', 'max:255'],
            'settings.mailtrap_from_name' => ['nullable', 'string', 'max:255'],
        ];
    }
}
