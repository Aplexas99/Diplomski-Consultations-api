<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConsultationRequestResource extends JsonResource
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
            'student' => new StudentResource($this->student),
            'professor' => new ProfessorResource($this->professor),
            'schedule' => new ScheduleResource($this->schedule),
            'status' => $this->status,
            'note' => $this->note,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'type' => $this->type,
            'link' => $this->link,
            'location' => $this->location,
            'reason' => $this->reason,
        ];
    }
}
