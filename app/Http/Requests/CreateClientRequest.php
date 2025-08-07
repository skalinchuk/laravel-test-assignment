<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Domain\Client\ValueObjects\Region;
use Illuminate\Foundation\Http\FormRequest;

final class CreateClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $allowedRegions = Region::getAllowedRegions();
        $minAge = config('credit.age.min', 18);
        $maxAge = config('credit.age.max', 60);

        return [
            'name' => ['required', 'string', 'max:255'],
            'age' => ['required', 'integer', "min:{$minAge}", "max:{$maxAge}"],
            'region' => ['required', 'string', 'in:'.implode(',', $allowedRegions)],
            'income' => ['required', 'integer', 'min:0'],
            'score' => ['required', 'integer', 'min:0', 'max:1000'],
            'pin' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        $minAge = config('credit.age.min', 18);
        $maxAge = config('credit.age.max', 60);
        $allowedRegions = implode(', ', Region::getAllowedRegions());

        return [
            'name.required' => 'Name is required',
            'age.required' => 'Age is required',
            'age.min' => "Age must be at least {$minAge}",
            'age.max' => "Age must not exceed {$maxAge}",
            'region.required' => 'Region is required',
            'region.in' => "Region must be one of: {$allowedRegions}",
            'income.required' => 'Income is required',
            'income.min' => 'Income cannot be negative',
            'score.required' => 'Credit score is required',
            'score.min' => 'Credit score cannot be negative',
            'score.max' => 'Credit score cannot exceed 1000',
            'pin.required' => 'PIN is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'phone.required' => 'Phone is required',
        ];
    }
}
