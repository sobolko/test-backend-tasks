<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Task;
use App\Enums\TaskStatus;
use App\Http\Resources\TaskResource;
use App\Services\TaskStatsService;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        $sortBy = request('sort_by', 'created_at');
        $sortDirection = request('sort_order', 'desc');
        $perPage = request('per_page', 15);

        // Валидация поля для сортировки
        $allowedSortFields = ['id', 'title', 'status', 'created_at', 'updated_at'];
        if (!in_array($sortBy, $allowedSortFields)) {
            $sortBy = 'created_at';
        }

        // Валидация направления сортировки
        if (!in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        // Валидация количества элементов на страницу
        $perPage = max(1, min(100, (int) $perPage));

        $tasks = Task::where('user_id', Auth::id())
            ->with('user')
            ->orderBy($sortBy, $sortDirection)
            ->paginate($perPage);

        return TaskResource::collection($tasks);
    }

    public function store(TaskRequest $request)
    {
        $validated = $request->validated();
        $task = Task::create([
            'title' => $validated['title'],
            'status' => $validated['status'] ?? TaskStatus::New,
            'user_id' => Auth::id(),
        ]);
        return new TaskResource($task);
    }

    public function stats(TaskStatsService $service)
    {
        return response()->json(
            $service->get(Auth::user(), request('user_id'))
        );
    }

    public function show(Task $task)
    {
        $this->authorize('view', $task);
        return new TaskResource($task);
    }

    public function update(TaskRequest $request, string $id)
    {
        $task = Task::findOrFail($id);
        $this->authorize('update', $task);
        $validated = $request->validated();
        $task->update($validated);
        return new TaskResource($task);
    }

    public function destroy(string $id)
    {
        $task = Task::findOrFail($id);
        $this->authorize('delete', $task);
        $task->delete();
        return response()->json(null, 204);
    }
}
