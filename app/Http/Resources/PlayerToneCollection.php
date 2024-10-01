<?php

namespace App\Http\Resources;

use App\Models\Tone;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PlayerToneCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => PlayerToneResource::collection($this->collection),
            'tones' => ToneResource::collection(Tone::get())
        ];
    }
}
