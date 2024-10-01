<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ContactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "full_name" => $this->full_name,
            "email" => $this->email,
            "avatar" => $this->avatar,
            'avatar_url' => $this->avatar ? Storage::exists($this->avatar) ? Storage::url($this->avatar) : null : "",
            "phone" => $this->phone,
            "player_id" => $this->player_id,
             "text_to_contact" => $this->text_to_contact,
             "call_to_contact" => $this->call_to_contact,
             "contact_to_player" => $this->contact_to_player,
        ];
    }
}
