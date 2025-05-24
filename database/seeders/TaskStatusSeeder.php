<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskStatusSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('task_statuses')->insert([
            [
                'name' => 'Todo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'In Progress',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Blocked',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'In Review',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Done',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Archived',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
