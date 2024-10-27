<?php

declare(strict_types=1);

namespace App\Services;

use App\Http\Data\Task\CreateData;
use App\Http\Data\Task\GetData;
use App\Http\Data\Task\UpdateData;
use App\Models\Task;
use App\Models\User;

final readonly class TaskService
{
    public function getPaginated(GetData $data): array
    {
        $tasks = Task::query()
            ->with('user')
            ->completed($data->isCompleted)
            ->orderBy($data->sortField, $data->sortDirection);

        return [
            'total' => $tasks->count(),
            'tasks' => $tasks
                ->forPage($data->page, $data->perPage)
                ->get(),
        ];
    }

    public function create(CreateData $data, User $user): Task
    {
        $task = new Task($data->toArray());

        $task->user()->associate($user);

        $task->save();

        return $task;
    }

    public function update(UpdateData $data, Task $task): Task
    {
        if ($data->name !== null) {
            $task->name = $data->name;
        }

        if ($data->description !== null) {
            $task->description = $data->description;
        }

        if ($data->completedAt !== null) {
            $task->completed_at = $data->completedAt;
        }

        $task->save();

        return $task;
    }

    public function delete(Task $task): Task
    {
        $task->delete();

        return $task;
    }
}
