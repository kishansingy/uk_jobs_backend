<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'topic'       => $this->topic,
            'notes'       => $this->notes,
            'status'      => $this->status,
            'meeting_url' => $this->meeting_url,
            'booked_at'   => $this->booked_at?->toISOString(),
            'advisor'     => $this->whenLoaded('advisor', fn() => [
                'id'           => $this->advisor->id,
                'display_name' => $this->advisor->display_name,
                'specialisms'  => $this->advisor->specialisms,
                'photo_path'   => $this->advisor->photo_path,
            ]),
            'availability' => $this->whenLoaded('availability', fn() => [
                'date'       => $this->availability->available_date?->toDateString(),
                'start_time' => $this->availability->start_time,
                'end_time'   => $this->availability->end_time,
            ]),
        ];
    }
}
