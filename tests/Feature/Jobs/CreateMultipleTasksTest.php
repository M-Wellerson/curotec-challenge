<?php

namespace Tests\Feature\Jobs;

use App\Jobs\CreateMultipleTasks;
use App\Models\Category;
use App\Models\Task;
use App\Models\TaskPriorities;
use App\Models\TaskStatuses;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CreateMultipleTasksTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_multiple_tasks_in_queue()
    {
        Bus::fake();
        $user = User::factory()->create();
        $this->actingAs($user);

        $priority = TaskPriorities::factory()->create();
        $status = TaskStatuses::factory()->create();

        $category = Category::factory()->create([
            'user_id' => $user->id
        ]);

        $tasksData = [
            [
                'title' => 'Task 1',
                'description' => 'Description 1',
                'due_date' => now()->addDays(1)->format('Y-m-d'),
                'priority_id' => $priority->id,
                'status_id' => $status->id,
                'category_ids' => [$category->id]
            ],
            [
                'title' => 'Task 2',
                'description' => 'Description 2',
                'due_date' => now()->addDays(2)->format('Y-m-d'),
                'priority_id' => $priority->id,
                'status_id' => $status->id,
                'category_ids' => [$category->id]
            ],
        ];

        $response = $this->post('/tasks/batch', [
            'tasks' => $tasksData
        ]);

        $response->assertStatus(202);

        Bus::assertDispatched(CreateMultipleTasks::class, function ($job) use ($user, $tasksData) {
            return $job->userId === $user->id &&
                $job->tasksData === $tasksData;
        });
    }

    /** @test */
    public function it_actually_creates_tasks_when_job_is_processed()
    {
        Queue::fake();
        $user = User::factory()->create();

        $priority = TaskPriorities::factory()->create();
        $status = TaskStatuses::factory()->create();

        $category = Category::factory()->create([
            'user_id' => $user->id
        ]);

        $tasksData = [
            [
                'title' => 'Task 1',
                'description' => 'Description 1',
                'due_date' => now()->addDays(1)->format('Y-m-d'),
                'priority_id' => $priority->id,
                'status_id' => $status->id,
                'category_ids' => [$category->id]
            ],
            [
                'title' => 'Task 2',
                'description' => 'Description 2',
                'due_date' => now()->addDays(2)->format('Y-m-d'),
                'priority_id' => $priority->id,
                'status_id' => $status->id,
                'category_ids' => [$category->id]
            ],
        ];

        $job = new CreateMultipleTasks($user->id, $tasksData);
        $job->handle();

        $this->assertDatabaseHas('tasks', [
            'title' => 'Task 1',
            'owner_id' => $user->id
        ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Task 2',
            'owner_id' => $user->id
        ]);

        $task1 = Task::where('title', 'Task 1')->first();
        $task2 = Task::where('title', 'Task 2')->first();

        $this->assertCount(1, $task1->categories);
        $this->assertCount(1, $task2->categories);
        $this->assertEquals($category->id, $task1->categories->first()->id);
    }

    /** @test */
    public function it_correctly_associates_categories_with_tasks()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $priority = TaskPriorities::factory()->create();
        $status = TaskStatuses::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $tasksData = [
            [
                'title' => 'Task 1',
                'priority_id' => $priority->id,
                'status_id' => $status->id,
                'category_ids' => [$category->id],
            ],
        ];

        $this->postJson('/tasks/batch', ['tasks' => $tasksData]);

        $this->assertDatabaseHas('tasks', ['title' => 'Task 1']);
        $task = Task::where('title', 'Task 1')->first();
        $this->assertCount(1, $task->categories);
        $this->assertEquals($category->id, $task->categories->first()->id);
    }

    /** @test */
    public function it_verifies_queued_jobs_execute_correctly()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $priority = TaskPriorities::factory()->create();
        $status = TaskStatuses::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $tasksData = [
            [
                'title' => 'Task from Queue Test',
                'description' => 'This task should be created by the queue',
                'due_date' => now()->addDays(1)->format('Y-m-d'),
                'priority_id' => $priority->id,
                'status_id' => $status->id,
                'category_ids' => [$category->id]
            ],
        ];

        config(['queue.default' => 'sync']);

        $response = $this->post('/tasks/batch', [
            'tasks' => $tasksData
        ]);

        $response->assertStatus(202);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Task from Queue Test',
            'owner_id' => $user->id
        ]);

        $task = Task::where('title', 'Task from Queue Test')->first();
        $this->assertCount(1, $task->categories);
        $this->assertEquals($category->id, $task->categories->first()->id);
    }

    /** @test */
    public function it_dispatches_job_to_correct_queue()
    {
        Bus::fake();
        $user = User::factory()->create();
        $this->actingAs($user);

        $priority = TaskPriorities::factory()->create();
        $status = TaskStatuses::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $tasksData = [
            [
                'title' => 'Queue Test Task',
                'priority_id' => $priority->id,
                'status_id' => $status->id,
                'category_ids' => [$category->id],
            ]
        ];

        $response = $this->postJson('/tasks/batch', ['tasks' => $tasksData]);
        $response->assertStatus(202);

        Bus::assertDispatched(CreateMultipleTasks::class, function ($job) use ($user, $tasksData) {
            return $job->userId === $user->id &&
                $job->tasksData === $tasksData;
        });
    }
}
