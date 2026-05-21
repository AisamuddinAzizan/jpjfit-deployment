<?php

namespace App\Http\Requests;

use App\Services\LandingPageContentService;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLandingPageContentRequest extends FormRequest
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
        $service = app(LandingPageContentService::class);
        $rules = [
            'content' => ['required', 'array'],
            'content.en' => ['required', 'array'],
            'content.ms' => ['required', 'array'],
            'active_locale' => ['nullable', 'in:en,ms'],
        ];

        foreach (array_keys($service->defaults()) as $key) {
            $rules['content.en.'.$key] = ['nullable', 'string', 'max:5000'];
            $rules['content.ms.'.$key] = ['nullable', 'string', 'max:5000'];
        }

        return $rules;
    }
}
