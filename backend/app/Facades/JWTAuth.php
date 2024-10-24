<?php

declare(strict_types=1);

namespace App\Facades;

use Tymon\JWTAuth\Facades\JWTAuth as BaseJWTAuth;

/**
 * @method static string fromUser(\App\Models\User $user)
 */
final class JWTAuth extends BaseJWTAuth {}
