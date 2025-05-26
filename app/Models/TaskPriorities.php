<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskPriorities extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'level'];
    public $timestamps = true;

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'priority_id');
    }
}
