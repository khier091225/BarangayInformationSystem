<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreResidentBlotterRequest extends FormRequest
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
            'respondent' => 'required|string|max:255',
            'incident' => 'required|string|min:10|max:5000',
            'incident_date' => 'required|date|before_or_equal:today',
        ];
    }
}
