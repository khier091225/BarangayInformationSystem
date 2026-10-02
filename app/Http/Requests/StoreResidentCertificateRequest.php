<?php

namespace App\Http\Requests;

use App\Support\CertificateFees;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreResidentCertificateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === 'resident' && $this->user()?->resident_id !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'certificate_type' => ['required', 'string', Rule::in(CertificateFees::types())],
            'purpose' => 'required|string|max:255',
        ];
    }
}
