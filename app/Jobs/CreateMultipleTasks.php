<?php

namespace App\Jobs;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateMultipleTasks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userId;
    public $tasksData;

    public function __construct(int $userId, array $tasksData)
    {
        $this->userId = $userId;
        $this->tasksData = $tasksData;
    }

    public function handle(): void
    {
        foreach ($this->tasksData as $taskData) {
            $categories = $taskData['category_ids'] ?? [];
            unset($taskData['category_ids']);

            $task = Task::create(array_merge($taskData, ['owner_id' => $this->userId]));
            $task->categories()->sync($categories);
        }
    }
}
