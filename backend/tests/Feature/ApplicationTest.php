<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class ApplicationTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_application_is_up(): void
    {
        $this->get('/up')
            ->assertOk()
            ->assertSee('Application up');
    }
}
