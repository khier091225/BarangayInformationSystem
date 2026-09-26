<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'certificate_type' => 'required|string|in:Barangay Clearance,Certificate of Residency,Certificate of Indigency,Business Clearance',
            'purpose' => 'required|string|max:255',
        ];
    }
}
