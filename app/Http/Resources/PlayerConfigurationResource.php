<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class PlayerConfigurationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "phone" => $this->phone,
            "pin" => $this->pin,
            "email" => $this->email,
            "reminder_alerts" => $this->reminder_alerts,
            "event_display_number" => $this->event_display_number,
            "avatar" => $this->avatar,
            'avatar_url' =>  $this->avatar ? Storage::exists($this->avatar) ? Storage::url($this->avatar) : null : "",
            "coach_id" => $this->coach_id,
            "latitude" => $this->latitude,
            "longitude" => $this->longitude,
            "contacts" => ContactResource::collection($this->contacts),
        ];
    }
}
