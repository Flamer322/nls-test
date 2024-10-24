<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use Exception;

final readonly class UserRepository
{
    public function save(User $user): void
    {
        if (! $user->save()) {
            throw new Exception('The error occurred while saving the user.');
        }
    }
}
