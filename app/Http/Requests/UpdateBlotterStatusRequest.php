<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBlotterStatusRequest extends FormRequest
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
            'action' => ['required', Rule::in(['accept', 'schedule', 'start', 'settle', 'dismiss'])],
            'hearing_at' => [
                Rule::requiredIf($this->input('action') === 'schedule'),
                'nullable',
                'date',
                'after_or_equal:today',
            ],
            'message' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
