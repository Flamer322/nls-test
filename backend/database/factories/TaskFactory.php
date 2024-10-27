<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
final class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(),
            'description' => fake()->paragraph(),
        ];
    }

    public function completed(): TaskFactory
    {
        return $this->state(fn (array $attributes) => [
            'completed_at' => fake()->date(),
        ]);
    }
}
