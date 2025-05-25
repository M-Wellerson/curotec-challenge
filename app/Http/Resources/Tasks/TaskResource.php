<?php

namespace App\Http\Resources\Tasks;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'due_date' => $this->due_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'priority' => [
                'id' => $this->priority->id,
                'name' => $this->priority->name,
            ],
            'status' => [
                'id' => $this->status->id,
                'name' => $this->status->name,
            ],
            'categories' => $this->categories->map->only(['id', 'name']),
        ];
    }
}
