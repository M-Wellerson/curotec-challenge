<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskStatuses extends Model
{
    protected $fillable = ['name'];
    public $timestamps = true;

    public function tasks(): HasMany
    {
        return $this->hasMany(Tasks::class, 'status_id');
    }
}
