<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RecurringEventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {

        $data = [
            "id" => $this->id,
            "date" => $this->date,
            "event_id" => $this->event_id,
            "parent_event" => $this->parent_event
        ];

        return $data;
    }
}
