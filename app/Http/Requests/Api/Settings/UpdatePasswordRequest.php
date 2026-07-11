<?php

namespace App\Http\Requests\Api\Settings;

use App\Concerns\PasswordValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePasswordRequest extends FormRequest
{
    use PasswordValidationRules;

    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string', 'current_password:sanctum'],
            'password' => $this->passwordRules(),
        ];
    }
}
