<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyResidentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === 'resident' && $this->user()?->resident_id === null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'registration_code' => 'required|string|size:16|regex:/\A[0-9A-F]{16}\z/',
        ];
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('registration_code'))) {
            $this->merge([
                'registration_code' => strtoupper(str_replace(['-', ' '], '', trim($this->input('registration_code')))),
            ]);
        }
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'registration_code.size' => 'Enter the 16-character code provided by barangay staff.',
            'registration_code.regex' => 'Enter the 16-character code provided by barangay staff.',
        ];
    }
}
