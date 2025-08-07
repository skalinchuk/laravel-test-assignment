<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CheckCreditEligibilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'string', 'uuid'],
            'credit_id' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.required' => 'Client ID is required',
            'client_id.uuid' => 'Client ID must be a valid UUID',
            'credit_id.required' => 'Credit ID is required',
        ];
    }
}
