<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendNewsletterBroadcastRequest extends FormRequest
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
            'recipient_mode' => ['required', Rule::in(['all', 'selected'])],
            'subscriber_ids' => ['required_if:recipient_mode,selected', 'array'],
            'subscriber_ids.*' => ['integer', 'exists:newsletter_subscribers,id'],
            'subject' => ['required', 'string', 'max:180'],
            'message' => ['required', 'string', 'max:5000'],

            'test_session_id' => ['nullable', 'exists:test_sessions,id'],
        ];
    }
}
