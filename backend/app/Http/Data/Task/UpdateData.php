<?php

declare(strict_types=1);

namespace App\Http\Data\Task;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
final class UpdateData extends Data
{
    public ?string $name = null;
    public ?string $description = null;
    public ?string $completedAt = null;

    public static function rules(ValidationContext $context): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'completed_at' => ['nullable', 'string', 'date_format:Y-m-d'],
        ];
    }
}
