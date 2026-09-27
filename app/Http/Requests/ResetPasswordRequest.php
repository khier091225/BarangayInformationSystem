<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string|Password>>
     */
    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'email' => ['bail', 'required', 'string', 'email', 'max:255'],
            'password' => ['bail', 'required', 'string', Password::min(8)->max(72), 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ];
    }
}
