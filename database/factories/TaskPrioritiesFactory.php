<?php

namespace Database\Factories;

use App\Models\TaskPriorities;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskPrioritiesFactory extends Factory
{
    protected $model = TaskPriorities::class;

    public function definition()
    {
        static $level = 0;
        $level++;

        return [
            'name' => "Priority {$level}",
            'level' => $level,
        ];
    }
}
