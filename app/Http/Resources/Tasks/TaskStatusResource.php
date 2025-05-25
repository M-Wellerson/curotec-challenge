<?php

namespace App\Http\Resources\Tasks;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskStatusResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'   => $this->id,
            'name' => $this->name,
        ];
    }
}
