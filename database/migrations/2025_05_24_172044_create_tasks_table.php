<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->timestampTz('start_date')->nullable();
            $table->timestampTz('due_date')->nullable();
            $table->decimal('estimated_hours', 6, 2)->nullable();
            $table->decimal('actual_hours', 6, 2)->nullable();
            $table->foreignId('priority_id')->nullable()->constrained('task_priorities')->nullOnDelete();
            $table->foreignId('status_id')->constrained('task_statuses')->restrictOnDelete();
            $table->foreignId('owner_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('parent_task_id')->nullable()->constrained('tasks')->cascadeOnDelete();
            $table->timestampsTz();

            $table->index('status_id', 'idx_tasks_status');
            $table->index('priority_id', 'idx_tasks_priority');
            $table->index('due_date', 'idx_tasks_due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
