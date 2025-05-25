<?php

namespace App\Http\Resources\Tasks;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskPriorityResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'level' => $this->level,
        ];
    }
}
