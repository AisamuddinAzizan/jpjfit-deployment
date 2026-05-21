<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFitnessResultRequest extends FormRequest
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
            'participant_id' => ['required', 'integer', 'exists:participants,id'],
            'test_session_id' => ['required', 'integer', 'exists:test_sessions,id'],
            'push_ups' => ['required', 'integer', 'min:0', 'max:200'],
            'sit_ups' => ['required', 'integer', 'min:0', 'max:200'],
            'sit_and_reach_cm' => ['required', 'numeric', 'min:-20', 'max:100'],
            'shuttle_run_level' => ['required', 'numeric', 'min:0', 'max:30'],
            'run_2_4km_seconds' => ['required', 'integer', 'min:300', 'max:3600'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
