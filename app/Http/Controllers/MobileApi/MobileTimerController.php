<?php

namespace App\Http\Controllers\MobileApi;

use App\Http\Controllers\Controller;
use App\Http\Resources\TimerResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\PlayerResource;


use App\Models\Player;


use App\Models\Timer;
use App\Models\User;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;

class MobileTimerController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
   */
  public function index(Request $request)
  {

    $user = $request->user();
    $player = $user->player;
    // Log::info($player);
    return TimerResource::collection($player->timers);
  }


  /**
   * Display the specified resource.
   *
   * @param Timer $timer
   * @return void
   */
  public function show(Timer $timer)
  {
    return new TimerResource($timer);
  }

  public function start(Timer $timer)
  {
  }
}
