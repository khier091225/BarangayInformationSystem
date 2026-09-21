<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveBlotterRequest extends FormRequest
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
            'complainant' => 'required|string|max:255',
            'respondent' => 'required|string|max:255',
            'incident' => 'required|string',
            'incident_date' => 'required|date',
            'status' => 'required|in:Pending,Settled,Dismissed',
        ];
    }
}
