<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlayerConfigurationResource;
use App\Models\Player;
use Illuminate\Http\Request;

class PlayerConfigurationController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param Player $request
     * @return void
     */
    public function show(Player $player)
    {
        return new PlayerConfigurationResource($player);
    }
}
