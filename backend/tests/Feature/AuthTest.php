<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_register_request_is_successful(): void
    {
        $this->postJson(route('auth.register'),
            [
                'name' => $this->faker->name(),
                'email' => $this->faker->unique()->safeEmail(),
                'password' => $this->faker->password(8, 20),
            ])
            ->assertCreated();
    }

    public function test_register_request_fails_for_non_unique_email(): void
    {
        $email = $this->faker->unique()->safeEmail();

        User::factory()->create([
            'email' => $email,
        ]);

        $this->postJson(route('auth.register'),
            [
                'name' => $this->faker->name(),
                'email' => $email,
                'password' => $this->faker->password(8, 20),
            ])
            ->assertUnprocessable()
            ->assertSee('The email has already been taken.')
            ->assertJsonValidationErrors([
                'email',
            ]);
    }

    public function test_register_request_validation(): void
    {
        $this->postJson(route('auth.register'),
            [
                'email' => $this->faker->sentence,
                'password' => $this->faker->password(1, 7),
            ])
            ->assertUnprocessable()
            ->assertSee('The name field is required. (and 2 more errors)')
            ->assertJsonValidationErrors([
                'name',
                'email',
                'password',
            ]);
    }

    public function test_login_request_is_successful(): void
    {
        $password = $this->faker->password(8, 20);

        /* @var User $user */
        $user = User::factory()->create([
            'password' => Hash::make($password),
        ]);

        $this->postJson(route('auth.login'),
            [
                'email' => $user->email,
                'password' => $password,
            ])
            ->assertNoContent()
            ->assertCookieNotExpired('token');
    }

    public function test_login_request_fails_for_non_existing_user(): void
    {
        $this->postJson(route('auth.login'),
            [
                'email' => $this->faker->unique()->safeEmail(),
                'password' => $this->faker->password(8, 20),
            ])
            ->assertUnprocessable()
            ->assertSee('Wrong email or password')
            ->assertJsonStructure([
                'message',
            ]);
    }

    public function test_login_request_validation(): void
    {
        $this->postJson(route('auth.login'),
            [
                'email' => $this->faker->sentence,
                'password' => $this->faker->password(1, 7),
            ])
            ->assertUnprocessable()
            ->assertSee('The email field must be a valid email address. (and 1 more error)')
            ->assertJsonValidationErrors([
                'email',
                'password',
            ]);
    }

    public function test_logout_request_is_successful(): void
    {
        $this->authorize();

        $this->postJson(route('auth.logout'))
            ->assertNoContent()
            ->assertCookieExpired('token');
    }

    public function test_logout_request_fails_for_non_authorized_user(): void
    {
        $this->postJson(route('auth.logout'))
            ->assertUnauthorized()
            ->assertSee('Unauthenticated');
    }

    public function test_me_request_is_successful(): void
    {
        $this->authorize();

        $this->postJson(route('auth.me'))
            ->assertOk()
            ->assertJsonFragment([
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ])
            ->assertJsonStructure([
                'id',
                'name',
                'email',
                'email_verified_at',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_me_request_fails_for_non_authorized_user(): void
    {
        $this->postJson(route('auth.me'))
            ->assertUnauthorized()
            ->assertSee('Unauthenticated');
    }
}
