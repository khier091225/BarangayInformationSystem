<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCertificateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'resident_id' => 'required|exists:residents,id',
            'certificate_type' => 'required|string|in:Barangay Clearance,Certificate of Residency,Certificate of Indigency,Business Clearance',
            'purpose' => 'required|string|max:255',
            'date_issued' => 'required|date',
        ];
    }
}
