<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskPriorities extends Model
{
    protected $fillable = ['name', 'level'];
    public $timestamps = true;

    public function tasks(): HasMany
    {
        return $this->hasMany(Tasks::class, 'priority_id');
    }
}
