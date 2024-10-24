<?php

declare(strict_types=1);

namespace App\Http\Data\Auth;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

final class LoginData extends Data
{
    public string $email;

    public string $password;

    public static function rules(ValidationContext $context): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:8'],
        ];
    }
}
