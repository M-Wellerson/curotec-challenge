<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\TaskPriorities;
use App\Models\TaskStatuses;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    protected $model = Task::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'owner_id' => User::factory(),
            'status_id' => TaskStatuses::factory(),
            'priority_id' => TaskPriorities::factory(),
            'due_date' => $this->faker->dateTimeBetween('now', '+1 month'),
        ];
    }
}
