<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHealthRecordRequest extends FormRequest
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
            'height_cm' => ['required', 'numeric', 'min:50', 'max:260'],
            'weight_kg' => ['required', 'numeric', 'min:20', 'max:250'],
            'blood_pressure_systolic' => ['required', 'integer', 'min:80', 'max:250'],
            'blood_pressure_diastolic' => ['required', 'integer', 'min:40', 'max:200'],
            'glucose_mmol' => ['nullable', 'numeric', 'min:0'],
            'cholesterol_mmol' => ['nullable', 'numeric', 'min:0'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
