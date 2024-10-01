<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TimerStoreRequest;
use App\Http\Requests\TimerUpdateRequest;
use App\Http\Resources\TimerResource;
use App\Models\Player;
use App\Models\Timer;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PlayerTimerController extends Controller
{

    public function index(Player $player)
    {
        $query =  $player->timers();

        return TimerResource::collection($query->get());
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param TimerStoreRequest $request
     * @param Player $player
     * @return void
     */
    public function store(TimerStoreRequest $request, Player $player)
    {
        $data = $request->only('name', 'duration');
        if ($request->has('reminders')) {
            $data['reminders'] = json_encode($request->reminders);
        }
        $timer = $player->timers()->create($data);

        return $timer;
    }

    /**
     * Display the specified resource.
     *
     * @param Player $player
     * @param Timer $timer
     * @return Timer
     */
    public function show(Player $player, Timer $timer)
    {
        return $timer;
    }


    /**
     * Update the specified resource in storage.
     *
     * @param TimerUpdateRequest $request
     * @param Player $player
     * @param Timer $timer
     * @return Timer
     */
    public function update(TimerUpdateRequest $request, Player $player, Timer $timer)
    {
        $data = $request->toArray();
        $timer->update($data);

        return $timer->refresh();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Player $player
     * @param Timer $timer
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Player $player, Timer $timer)
    {
        if ($timer->delete()) {
            return response()->json('Success', 200);
        } else {
            return response()->json('Error', 500);
        }
    }
}
