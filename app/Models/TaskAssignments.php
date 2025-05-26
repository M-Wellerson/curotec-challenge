<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TaskAssignments extends Pivot
{
    use HasFactory;

    public $incrementing = false;
    protected $table = 'task_assignments';
    protected $fillable = ['user_id', 'task_id', 'assigned_at'];
    public $timestamps = false;

    protected $casts = [
        'assigned_at' => 'immutable_datetime',
    ];
}
