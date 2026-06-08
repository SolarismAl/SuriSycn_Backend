<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'recurrence' => $this->recurrence,
            'color' => $this->color,
            'created_by' => $this->created_by,
            'department_id' => $this->department_id,
            'is_meeting' => $this->is_meeting,
            'external_participants' => $this->external_participants,
            'tagged_users' => $this->whenLoaded('taggedUsers'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
