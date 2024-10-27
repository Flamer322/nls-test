<?php

declare(strict_types=1);

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

final class TaskTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_get_request_is_successful(): void
    {
        $this->authorize();

        $tasksNumber = fake()->numberBetween(1, 10);

        Task::factory($tasksNumber)
            ->for($this->user)
            ->create();

        $this->getJson(route('tasks.get_paginated', [
            'page' => 1,
        ]))
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('total', $tasksNumber)
                ->has('tasks', $tasksNumber)
                ->where('tasks.0.id', 1)
            );
    }

    public function test_get_request_with_sorting_is_successful(): void
    {
        $this->authorize();

        $tasksNumber = fake()->numberBetween(1, 10);

        Task::factory($tasksNumber)
            ->for($this->user)
            ->create();

        $this->getJson(route('tasks.get_paginated', [
            'page' => 1,
            'sort_direction' => 'desc',
        ]))
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('total', $tasksNumber)
                ->has('tasks', $tasksNumber)
                ->where('tasks.0.id', $tasksNumber)
            );
    }

    public function test_get_request_with_filtering_is_successful(): void
    {
        $this->authorize();

        $completedTasksNumber = fake()->numberBetween(1, 10);
        $unCompletedTasksNumber = fake()->numberBetween(1, 10);

        Task::factory($completedTasksNumber)
            ->for($this->user)
            ->completed()
            ->create();

        Task::factory($unCompletedTasksNumber)
            ->for($this->user)
            ->create();

        $this->getJson(route('tasks.get_paginated', [
            'page' => 1,
            'is_completed' => 'true',
        ]))
            ->assertOk()
            ->assertJson(['total' => $completedTasksNumber])
            ->assertJsonCount($completedTasksNumber, 'tasks')
            ->assertJsonPath('tasks.0.id', 1);

        $this->getJson(route('tasks.get_paginated', [
            'page' => 1,
            'is_completed' => 'false',
        ]))
            ->assertOk()
            ->assertJson(['total' => $unCompletedTasksNumber])
            ->assertJsonCount($unCompletedTasksNumber, 'tasks')
            ->assertJsonPath('tasks.0.id', $completedTasksNumber + 1);
    }

    public function test_get_request_validation(): void
    {
        $this->authorize();

        $this->getJson(route('tasks.get_paginated', [
            'page' => 0,
            'per_page' => 0,
            'sort_field' => 'wrong',
            'sort_direction' => 'wrong',
        ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'page', 'per_page', 'sort_field', 'sort_direction',
            ]);
    }

    public function test_get_request_fails_for_non_authorized_user(): void
    {
        $this->getJson(route('tasks.get_paginated', [
            'page' => 1,
        ]))
            ->assertUnauthorized();
    }

    public function test_create_request_is_successful(): void
    {
        $this->authorize();

        $taskData = [
            'name' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'completed_at' => null,
        ];

        $this->postJson(route('tasks.create'), $taskData)
            ->assertCreated()
            ->assertJson([
                'name' => $taskData['name'],
                'description' => $taskData['description'],
                'completed_at' => null,
            ]);
    }

    public function test_create_request_validation(): void
    {
        $this->authorize();

        $this->postJson(route('tasks.create'), [
            'name' => '',
            'description' => $this->faker->paragraph(),
            'completed_at' => $this->faker->word(),
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name', 'completed_at',
            ]);
    }

    public function test_create_request_fails_for_non_authorized_user(): void
    {
        $this->postJson(route('tasks.create'), [
            'name' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'completed_at' => null,
        ])
            ->assertUnauthorized();
    }

    public function test_update_request_is_successful(): void
    {
        $this->authorize();

        $task = Task::factory()
            ->for($this->user)
            ->create();

        $updateData = [
            'name' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
        ];

        $this->patchJson(route('tasks.update', [
            'task' => $task->id,
        ]), $updateData)
            ->assertOk()
            ->assertJson([
                'name' => $updateData['name'],
                'description' => $updateData['description'],
                'completed_at' => null,
            ]);
    }

    public function test_update_request_validation(): void
    {
        $this->authorize();

        $task = Task::factory()
            ->for($this->user)
            ->create();

        $updateData = [
            'completed_at' => fake()->word(),
        ];

        $this->patchJson(route('tasks.update', [
            'task' => $task->id,
        ]), $updateData)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['completed_at']);
    }

    public function test_update_request_fails_for_non_user_task(): void
    {
        $this->authorize();

        $user = User::factory()
            ->create();

        $task = Task::factory()
            ->for($user)
            ->create();

        $updateData = [
            'name' => fake()->sentence(),
        ];

        $this->patchJson(route('tasks.update', [
            'task' => $task->id,
        ]), $updateData)
            ->assertForbidden();
    }

    public function test_update_request_fails_for_non_existent_task(): void
    {
        $this->authorize();

        Task::factory()
            ->for($this->user)
            ->create();

        $updateData = [
            'name' => fake()->sentence(),
        ];

        $this->patchJson(route('tasks.update', [
            'task' => 10000,
        ]), $updateData)
            ->assertNotFound();
    }

    public function test_delete_request_is_successful(): void
    {
        $this->authorize();

        $task = Task::factory()
            ->for($this->user)
            ->create();

        $this->deleteJson(route('tasks.delete', [
            'task' => $task->id,
        ]))
            ->assertOk()
            ->assertJson([
                'name' => $task->name,
                'description' => $task->description,
                'completed_at' => null,
            ]);
    }

    public function test_delete_request_fails_for_non_user_task(): void
    {
        $this->authorize();

        $user = User::factory()
            ->create();

        $task = Task::factory()
            ->for($user)
            ->create();

        $this->patchJson(route('tasks.delete', [
            'task' => $task->id,
        ]))
            ->assertForbidden();
    }

    public function test_delete_request_fails_for_non_existent_task(): void
    {
        $this->authorize();

        Task::factory()
            ->for($this->user)
            ->create();

        $this->patchJson(route('tasks.delete', [
            'task' => 10000,
        ]))
            ->assertNotFound();
    }
}
