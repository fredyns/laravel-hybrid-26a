<?php

namespace App\Features\User\Create;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    use PasswordValidationRules;
    use ProfileValidationRules;

    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        return self::rulesFor();
    }

    /**
     * Single source of validation rules for the Create slice,
     * shared by this request and the Livewire page.
     *
     * @return array<string, mixed>
     */
    public static function rulesFor(): array
    {
        $request = new self;

        return [
            'name' => $request->nameRules(),
            'email' => $request->emailRules(),
            'password' => $request->passwordRules(),
        ];
    }
}
