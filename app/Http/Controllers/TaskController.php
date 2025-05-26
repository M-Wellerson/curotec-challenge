<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\TaskBatchRequest;
use App\Http\Requests\Task\TaskStoreRequest;
use App\Http\Resources\Categories\CategoryResource;
use App\Http\Resources\Tasks\TaskPriorityResource;
use App\Http\Resources\Tasks\TaskResource;
use App\Http\Resources\Tasks\TaskStatusResource;
use App\Jobs\CreateMultipleTasks;
use App\Models\Category;
use App\Models\Task;
use App\Models\TaskPriorities;
use App\Models\TaskStatuses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController
{
    public function index(Request $request)
    {
        $statusIds   = $request->filled('status_ids')
            ? array_map('intval', explode(',', $request->status_ids))
            : [];

        $categoryIds = $request->filled('category_ids')
            ? array_map('intval', explode(',', $request->category_ids))
            : [];

        $tasks = Task::with(['priority:id,name,level', 'status:id,name', 'categories:id,name'])
            ->whereOwnerId(Auth::id())
            ->when($statusIds,   fn ($q) => $q->whereIn('status_id', $statusIds))
            ->when($categoryIds, fn ($q) =>
            $q->whereHas('categories', fn ($c) => $c->whereIn('categories.id', $categoryIds))
            )
            ->when($request->filled('from'), fn ($q) => $q->whereDate('due_date', '>=', $request->from))
            ->when($request->filled('to'),   fn ($q) => $q->whereDate('due_date', '<=', $request->to))
            ->latest('due_date')
            ->paginate(10)
            ->withQueryString();

        return inertia('Dashboard', [
            'tasks'      => TaskResource::collection($tasks),
            'priorities' => TaskPriorityResource::collection(TaskPriorities::orderBy('level')->get(['id','name'])),
            'categories' => CategoryResource::collection(Category::whereUserId(Auth::id())->orderBy('name')->get(['id','name'])),
            'statuses'   => TaskStatusResource::collection(TaskStatuses::all(['id','name'])),
        ]);
    }

    public function store(TaskStoreRequest $request)
    {
        $data = $request->validated();
        $task = $request->user()->tasks()->create($data);

        if (!empty($data['category_ids'])) {
            $task->categories()->sync($data['category_ids']);
        }

        $task->assignees()->attach($request->user()->id);

        return back();
    }

    public function destroy(Task $task)
    {
        abort_unless($task->owner_id === auth()->id(), 403);

        $task->delete();

        return back();
    }

    public function update(Request $r, Task $task)
    {
        $r->validate(['status_id'=>'required|exists:task_statuses,id']);
        abort_if($task->owner_id !== auth()->id(), 403);
        $task->update(['status_id'=>$r->status_id]);
        return back();
    }

    public function batch(TaskBatchRequest $request)
    {
        $userId = Auth::id();
        $tasksData = $request->input('tasks');

        CreateMultipleTasks::dispatch($userId, $tasksData);

        return response()->json([
            'message' => 'Tasks queued for creation',
        ], 202);
    }
}
