<?php

namespace App\Http\Requests;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
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
                'bail',
                Rule::requiredIf($this->input('action') === 'schedule'),
                'nullable',
                'date_format:Y-m-d\TH:i',
                function (string $attribute, mixed $value, Closure $fail): void {
                    $hearingAt = Carbon::createFromFormat('!Y-m-d\TH:i', (string) $value, 'Asia/Manila');

                    if ($hearingAt->lt(now('Asia/Manila')->startOfMinute())) {
                        $fail('Choose a mediation schedule at or after the current Philippine time.');
                    }
                },
            ],
            'message' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
