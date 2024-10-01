<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;
use League\CommonMark\Block\Element\FencedCode;

class PlayerLocationResource extends JsonResource
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
            'name' => $this->name,
            'fence' => $this->fence,
            'alerts' => $this->alerts,
            'radius' => $this->radius,
            'player_id' => $this->player_id,
            'lat' => $this->latitude,
            'latitude' => $this->latitude,
            'lon' => $this->longitude,
            'longitude' => $this->longitude,
            'zoom_level' => $this->zoom_level,
            'address' => $this->address
        ];
    }
}
