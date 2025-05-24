<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskPrioritySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('task_priorities')->insert([
            [
                'name' => 'Low',
                'level' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Medium',
                'level' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'High',
                'level' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Critical',
                'level' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'name' => 'Blocker',
                'level' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
