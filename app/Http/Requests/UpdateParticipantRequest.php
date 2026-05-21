<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateParticipantRequest extends FormRequest
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
        $participantId = $this->route('participant')?->id;

        return [
            'participant_no' => ['required', 'string', 'max:50', Rule::unique('participants', 'participant_no')->ignore($participantId)],
            'full_name' => ['required', 'string', 'max:255'],
            'ic_no' => ['required', 'string', 'max:30', Rule::unique('participants', 'ic_no')->ignore($participantId)],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'agency' => ['nullable', 'string', 'max:120'],
            'rank' => ['nullable', 'string', 'max:120'],
            'address' => ['nullable', 'string', 'max:1000'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:30'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
