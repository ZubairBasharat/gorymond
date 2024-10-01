<?php

namespace App\Http\Resources;

use App\Enums\ActivityType;
use App\Enums\ToneSituationType;
use Illuminate\Http\Resources\Json\JsonResource;

class PlayerToneResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'situation_type' => ToneSituationType::getKey($this->pivot->situation_type),
            'activity_type' => ActivityType::getKey($this->pivot->activity_type),
        ];
    }
}
