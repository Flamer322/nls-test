<?php

declare(strict_types=1);

namespace App\Http\Data\Auth;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class RegisterData extends Data
{
    public string $name;

    public string $email;

    public string $password;

    public static function rules(ValidationContext $context): array
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ];
    }
}
