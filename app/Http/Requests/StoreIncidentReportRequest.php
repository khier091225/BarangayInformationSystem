<?php

namespace App\Http\Requests;

use App\Models\IncidentReport;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class StoreIncidentReportRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category' => ['required', Rule::in(array_keys(IncidentReport::CATEGORY_TEAMS))],
            'description' => 'required|string|min:10|max:5000',
            'location' => 'required|string|max:255',
            'occurred_at' => [
                'bail', 'nullable', 'date_format:Y-m-d\TH:i',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (Carbon::createFromFormat('Y-m-d\TH:i', (string) $value, 'Asia/Manila')->isFuture()) {
                        $fail('The incident date and time cannot be in the future.');
                    }
                },
            ],
            'keep_identity_confidential' => 'sometimes|boolean',
            'evidence' => 'nullable|file|mimetypes:image/jpeg,image/png,image/webp,video/mp4,video/quicktime|extensions:jpg,jpeg,png,webp,mp4,mov|max:20480',
        ];
    }
}
