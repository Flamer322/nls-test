<?php

declare(strict_types=1);

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected User $user;

    final protected function authorize(): void
    {
        $this->user = User::factory()->create();

        $this->actingAs($this->user);
    }
}
