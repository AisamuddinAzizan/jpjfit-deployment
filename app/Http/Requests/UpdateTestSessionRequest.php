<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTestSessionRequest extends FormRequest
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
        $sessionId = $this->route('test_session')?->id;

        return [
            'session_code' => ['required', 'string', 'max:50', Rule::unique('test_sessions', 'session_code')->ignore($sessionId)],
            'title' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'session_date' => ['required', 'date'],
            'start_time' => ['nullable', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'end_time' => ['nullable', 'regex:/^\d{2}:\d{2}(:\d{2})?$/'],
            'status' => ['required', Rule::in(['scheduled', 'ongoing', 'completed', 'cancelled'])],
            'description' => ['nullable', 'string', 'max:2000'],
            'participant_ids' => ['nullable', 'array'],
            'participant_ids.*' => ['integer', 'exists:participants,id'],
        ];
    }
}
