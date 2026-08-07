<?php

namespace App\Features\User\Update;

use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    use ProfileValidationRules;

    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    public function rules(): array
    {
        /** @var User $user */
        $user = $this->route('user');

        return self::rulesFor($user);
    }

    /**
     * Single source of validation rules for the Update slice,
     * shared by this request and the Livewire page.
     *
     * @return array<string, mixed>
     */
    public static function rulesFor(?User $user = null): array
    {
        $request = new self;

        return [
            'name' => $request->nameRules(),
            'email' => $request->emailRules($user?->id),
        ];
    }
}
