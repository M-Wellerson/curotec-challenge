<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskStatuses extends Model
{
    use HasFactory;
    protected $fillable = ['name'];
    public $timestamps = true;

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'status_id');
    }
}
