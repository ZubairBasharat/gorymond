<?php

namespace App\Http\Controllers\Api;

use App\Enums\ActivityType;
use App\Enums\ToneSituationType;
use App\Http\Resources\PlayerToneCollection;
use App\Http\Resources\PlayerToneResource;
use App\Models\Player;
use App\Models\Tone;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class PlayerToneController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return PlayerToneCollection
     */
    public function index(Player $player)
    {
        return new PlayerToneCollection($player->tones);
    }


    public function update(Request $request, Player $player)
    {
        $player->syncTones($request->only('reminder', 'complete', 'model'));

        return $player;

    }


}
