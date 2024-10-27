<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Data\Task\CreateData;
use App\Http\Data\Task\GetData;
use App\Http\Data\Task\UpdateData;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

final class TaskController extends Controller
{
    public function __construct(
        private readonly TaskService $taskService,
    ) {}

    public function getPaginated(Request $request, int $page): JsonResponse
    {
        return response()
            ->json($this->taskService->getPaginated(
                GetData::validateAndCreate([
                    'page' => $page, ...$request->all(),
                ])
            ), Response::HTTP_OK);
    }

    public function create(Request $request): JsonResponse
    {
        return response()
            ->json($this->taskService->create(
                CreateData::validateAndCreate($request->all()), $request->user()
            ), Response::HTTP_CREATED);
    }

    public function update(Request $request, Task $task): JsonResponse
    {
        return response()
            ->json($this->taskService->update(
                UpdateData::validateAndCreate($request->all()), $task
            ), Response::HTTP_OK);
    }

    public function delete(Task $task): JsonResponse
    {
        return response()
            ->json($this->taskService->delete($task), Response::HTTP_OK);
    }
}
