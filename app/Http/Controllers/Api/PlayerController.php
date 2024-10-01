<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlayerStoreRequest;
use App\Http\Requests\PlayerUpdateRequest;
use App\Http\Resources\PlayerResource;
use App\Library\ImageService;
use App\Models\Player;
use Illuminate\Http\Request;

class PlayerController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new PlayerResource(auth()->user()->player);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return PlayerResource
     */
    public function store(PlayerStoreRequest $request)
    {


        $data = $request->toArray();
        // if($request->hasFile('avatar')) {
        //     $filename = ImageService::uploadProfileImage($this->request->file('avatar'));
        //     $data['avatar'] = $filename;
        // }

        $player = $request->user()->player()->create($request->toArray());

        return new PlayerResource($player);
    }

    public function update(PlayerUpdateRequest $request, Player $player)
    {
        $data = $request->toArray();
        if ($request->hasFile('avatar')) {
            $filename = ImageService::uploadProfileImage($this->request->file('avatar'));
            $data['avatar'] = $filename;
        }
        $player->update($data);

        return new PlayerResource($player->refresh());
    }

    public function avatar(Request $request, Player $player)
    {

        if ($request->hasFile('avatar')) {
            $filename = ImageService::uploadProfileImage($request->file('avatar'), $player->avatar ?? null);
            $data['avatar'] = $filename;
            $player->update($data);
        }

        return new PlayerResource($player->refresh());
    }
}
