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
        $isCreating = $this->isMethod('post');

        return [
            'complainant' => 'required|string|max:255',
            'respondent' => $isCreating ? 'nullable|string|max:255' : 'required|string|max:255',
            'incident' => 'required|string',
            'incident_date' => $isCreating ? 'nullable|date' : 'required|date',
        ];
    }
}
