<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\TaskStatuses;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => ['required','string','max:255'],
            'priority_id' => ['required','exists:task_priorities,id'],
            'due_date'    => ['nullable','date'],
        ]);

        $task = $request->user()->tasks()->create(
            $data + [
                'status_id' => TaskStatuses::where('name', 'Todo')->value('id'),
            ]
        );

        $task->assignees()->attach($request->user()->id);
        return back();
    }

    public function destroy(Task $task)
    {
        abort_unless($task->owner_id === auth()->id(), 403);

        $task->delete();

        return back();
    }
}
