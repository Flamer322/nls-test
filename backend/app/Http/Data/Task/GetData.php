<?php

declare(strict_types=1);

namespace App\Http\Data\Task;

use App\Casts\StringBoolCast;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Support\Validation\ValidationContext;

#[MapName(SnakeCaseMapper::class)]
final class GetData extends Data
{
    public int $page;
    public int $perPage = 10;
    #[WithCast(StringBoolCast::class)]
    public ?bool $isCompleted = null;
    public ?string $sortField = 'id';
    public ?string $sortDirection = 'asc';

    public static function rules(ValidationContext $context): array
    {
        return [
            'page' => ['required', 'int', 'min:1'],
            'per_page' => ['nullable', 'int', 'min:1', 'max:100'],
            'is_completed' => ['nullable', 'string', 'in:true,false'],
            'sort_field' => ['nullable', 'string', 'in:id,name,description,user_id'],
            'sort_direction' => ['nullable', 'string', 'in:asc,desc'],
        ];
    }
}
