<?php

namespace App\Http\Requests\Api\Settings;

use Illuminate\Foundation\Http\FormRequest;

class DeleteAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'password' => ['required', 'string', 'current_password:sanctum'],
        ];
    }
}
