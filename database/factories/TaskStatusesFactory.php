<?php

namespace Database\Factories;

use App\Models\TaskStatuses;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskStatusesFactory extends Factory
{
    protected $model = TaskStatuses::class;

    public function definition()
    {
        static $counter = 0;
        $counter++;

        return [
            'name' => "Status {$counter}",
        ];
    }
}
