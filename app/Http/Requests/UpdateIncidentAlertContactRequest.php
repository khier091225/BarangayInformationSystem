<?php

namespace App\Http\Requests;

use App\Models\IncidentReport;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateIncidentAlertContactRequest extends FormRequest
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
     * @return array<string, list<string|Rule>>
     */
    public function rules(): array
    {
        return [
            'team_key' => ['required', Rule::in(array_keys(IncidentReport::TEAM_LABELS))],
            'contact_name' => ['nullable', 'string', 'max:100'],
            'phone' => [
                Rule::requiredIf(fn (): bool => $this->boolean('is_active') && $this->boolean('sms_enabled')),
                'nullable',
                'string',
                'max:32',
                'regex:/\A09\d{9}\z/',
            ],
            'email' => [
                Rule::requiredIf(fn (): bool => $this->boolean('is_active') && $this->boolean('email_enabled')),
                'nullable',
                'string',
                'email',
                'max:255',
            ],
            'sms_enabled' => ['required', 'boolean'],
            'email_enabled' => ['required', 'boolean'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => 'Enter a mobile number or turn off SMS alerts.',
            'phone.regex' => 'Enter a valid Philippine mobile number, such as 09912197679.',
            'email.required' => 'Enter an email address or turn off email alerts.',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($this->boolean('is_active')
                    && ! $this->boolean('sms_enabled')
                    && ! $this->boolean('email_enabled')) {
                    $validator->errors()->add('is_active', 'Enable SMS or email before activating this contact.');
                }
            },
        ];
    }

    protected function prepareForValidation(): void
    {
        $phone = preg_replace('/[\s()\-]/', '', trim((string) $this->input('phone')));

        if (preg_match('/\A\+?63(9\d{9})\z/', $phone, $matches)) {
            $phone = '0'.$matches[1];
        }

        $contactName = trim((string) $this->input('contact_name'));
        $email = Str::lower(trim((string) $this->input('email')));

        $this->merge([
            'team_key' => $this->route('team'),
            'contact_name' => $contactName !== '' ? $contactName : null,
            'phone' => $phone !== '' ? $phone : null,
            'email' => $email !== '' ? $email : null,
            'sms_enabled' => $this->boolean('sms_enabled'),
            'email_enabled' => $this->boolean('email_enabled'),
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}
