<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Category;
use App\Models\Task;
use App\Models\TaskPriorities;
use App\Models\TaskStatuses;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_paginated_tasks_for_authenticated_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Task::factory()->count(15)->for($user, 'owner')->create();

        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn($assert) => $assert->has('tasks'));
    }

    /** @test */
    public function it_filters_tasks_by_status_ids()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $status1 = TaskStatuses::factory()->create();
        $status2 = TaskStatuses::factory()->create();

        Task::factory()->for($user, 'owner')->create(['status_id' => $status1->id]);
        Task::factory()->for($user, 'owner')->create(['status_id' => $status2->id]);

        $response = $this->get('/dashboard?status_ids=' . $status1->id);

        $response->assertStatus(200);
    }

    /** @test */
    public function it_creates_a_task_with_categories()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $priority = TaskPriorities::factory()->create();
        $status = TaskStatuses::factory()->create();

        $categories = Category::factory()->count(2)->create([
            'user_id' => $user->id,
        ]);

        $data = [
            'title' => 'New Task',
            'description' => 'Task Description',
            'due_date' => now()->addDays(7)->format('Y-m-d'),
            'category_ids' => $categories->pluck('id')->toArray(),
            'priority_id' => $priority->id,
            'status_id' => $status->id,
        ];

        $response = $this->post('/tasks', $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', ['title' => 'New Task']);
        $this->assertDatabaseCount('category_task', 2);
    }

    /** @test */
    public function it_prevents_deleting_task_not_owned_by_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $task = Task::factory()->create();

        $response = $this->delete('/tasks/' . $task->id);

        $response->assertStatus(403);
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    /** @test */
    public function it_deletes_task_owned_by_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $task = Task::factory()->for($user, 'owner')->create();

        $response = $this->delete('/tasks/' . $task->id);

        $response->assertRedirect();
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }
}
