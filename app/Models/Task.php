<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Task extends Model
{
    protected $fillable = [
        'title',
        'description',
        'start_date',
        'due_date',
        'estimated_hours',
        'actual_hours',
        'priority_id',
        'status_id',
        'owner_id',
        'parent_task_id',
    ];

    protected $casts = [
        'start_date' => 'immutable_datetime',
        'due_date' => 'immutable_datetime',
        'estimated_hours' => 'decimal:2',
        'actual_hours' => 'decimal:2',
    ];

    public function priority(): BelongsTo
    {
        return $this->belongsTo(TaskPriorities::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(TaskStatuses::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_task_id');
    }

    public function assignees()
    {
        return $this->belongsToMany(
            User::class,
            'task_assignments',
            'task_id',
            'user_id'
        )->withPivot('assigned_at')
            ->using(TaskAssignments::class);
    }

    public function scopeDue($query, $date)
    {
        return $query->whereDate('due_date', '<=', $date);
    }
}
