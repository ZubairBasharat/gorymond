<?php

namespace App\Http\Controllers\MobileApi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Player;
use Carbon\Carbon;

use App\Http\Resources\PlayerResource;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Auth;
use App\Events\MessageSent;


class MeController extends Controller
{

    // Get the last known location
    public function getLastLocation(Request $request)
    {
        $user = $request->user();
        $player = $user->player;

        Log::info('Request GET Payload: ' . __FILE__ .  json_encode($request->all()));

        return new PlayerResource($player);

        // return response()->json(['message' => 'No location data found'], 201);
    }


    public function postLocation(Request $request)
    {

        $user = $request->user();
        $player = $user->player;

        Log::info('user is ' . json_encode($user));
        Log::info('PLAYER is ' . json_encode($player));

        $headers = $request->headers->all();

        // Access specific header (e.g., Authorization)
        $authorizationHeader = $request->header('Authorization');

        // Log headers for debugging
        Log::info('Request Headers: ' . json_encode($headers));
        Log::info('Authorization Header: ' . $authorizationHeader);


        // 'id' => 'required|exists:players,id',
        // Validate latitude and longitude
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        Log::info('Request Payload: ' . json_encode($validated));

        if (!$player) {
            return response()->json(['error' => 'Player not found'], 404);
        }

        // Update player's coordinates
        $player->latitude = $validated['latitude'];
        $player->longitude = $validated['longitude'];

        $currentTimestamp = Carbon::now()->timestamp;

        // example of manipulating timestamp, if needed
        // $timestampMinus4Days = Carbon::now()->subHours(3)->subMinutes(33)->timestamp;
        $player->location_updated_at = $currentTimestamp;
        $player->save();



        // Log the update
        Log::info('Updated coordinates for Player ' . $player->id . ': Latitude ' . $player->latitude . ', Longitude ' . $player->longitude);


        $playerJSON = json_encode($player);

        // will come back to this when we figure out laravel reverb on aws
        // broadcast(new MessageSent("index of player locations ", $playerJSON, Auth::id()))->toOthers();


        return response()->json(['message' => 'Coordinates updated successfully'], 200);
    }
}
