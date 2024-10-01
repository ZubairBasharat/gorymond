<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
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
            "start" => $this->start,
            "end" => $this->end,
            "name" => $this->name,
            "notes" => $this->notes,
            "meet_with" => $this->meet_with,
            "location_name" => $this->location_name,
            "location_id" => $this->location_id,
            "reminders" => $this->reminders,
            "end_date" => $this->end_date,
            "recurring_active" => $this->recurring_active,
            "end_date_options" => $this->end_date_options,
            "repeat_options" => $this->repeat_options,
            "player_id" => $this->player_id,
            "parent_id" => $this->parent_id,
            "parent" => $this->parent
        ];
        if ($this->parent) {
            $data['recurring_active'] = $this->parent->recurring_active;
            $data['reminders'] = $this->parent->reminders;
            $data['end_date_options'] = $this->parent->end_date_options;
            $data['end_date'] = $this->parent->end_date;
            $data['repeat_options'] = $this->parent->repeat_options;
        }
        return $data;
    }
}
