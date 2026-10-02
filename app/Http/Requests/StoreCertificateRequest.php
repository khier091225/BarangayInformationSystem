<?php

namespace App\Http\Requests;

use App\Support\CertificateFees;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCertificateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'resident_id' => 'required|exists:residents,id',
            'certificate_type' => ['required', 'string', Rule::in(CertificateFees::types())],
            'purpose' => 'required|string|max:255',
            'date_issued' => 'required|date',
        ];
    }
}
