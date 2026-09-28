<?php

namespace App\Http\Requests;

use App\Models\IncidentReport;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateIncidentReportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->role === 'staff';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'action' => ['required', Rule::in(['accept', 'advance'])],
            'team' => [Rule::requiredIf(fn (): bool => $this->input('action') === 'accept'), 'nullable', Rule::in(array_keys(IncidentReport::TEAM_LABELS))],
            'message' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
