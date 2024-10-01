<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\PlayerLocationResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App\Models\Location;
use FontLib\Table\Type\loca;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


use App\Events\MessageSent;

class PlayerLocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(string $id)
    {
        $locations = Location::where('player_id', $id)->get();
        Log::info('MAIN index for location', [
            'id' => $id,
            'locations' => $locations
        ]);

        if ($locations->isEmpty()) {
            return response()->json(['message' => 'No locations found for this player'], 404);
        }

        $locationsJson = json_encode($locations);

        // broadcast(new MessageSent("index of player locations ", $locationsJson, Auth::id()))->toOthers();
        return PlayerLocationResource::collection($locations);
    }


    /**
     * Store a newly created resource in storage.
     */
    // public function store(StorePlayerLocationRequest $request, $player)
    public function store(Request $request, string $id)
    {
        // Get the currently authenticated user
        $user = Auth::user();
        $userId = Auth::id();

        Log::info("SUER ID  "  . $userId);

        Log::info('store request received', [
            'request_data' => $request->all(),
            'id' => $id
        ]);


        $locationsJson = json_encode($request->all());
        // broadcast(new MessageSent("updated player location", $locationsJson, Auth::id()))->toOthers();


        // 1. Validation
        $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $isFence = $request->fence  ? 1 : 0;
        $isAlerts = $request->alerts ? 1 : 0;

        $saveData = [
            'name' => $request->name,
            'radius' => $request->radius, // Just a dummy radius for now, you can change it to 'location radius' or 'location distance
            'latitude'  => $request->latitude,
            'longitude' => $request->longitude,
            'zoom_level' => $request->zoom_level,
            'address' => $request->address,
            'fence' => $isFence,
            'alerts' => $isAlerts,
            'player_id' => $id, // This is the player id, you can change it to 'user_id' or 'player_id
            // ... other fields ...
        ];


        if ($request->mode == 'save') {
            // 2. Create location using the model
            $playerLocation = Location::create($saveData);
        } else if ($request->mode == 'update') {
            // Update existing location

            // is there a location_id?
            if (empty($request->location_id)) {
                return response()->json([
                    'message' => 'Location ID is required for update.',
                ], 400);
            } else {
                $findID = $request->location_id;
            }

            $playerLocation = Location::find($findID);

            if ($playerLocation) {
                // Update data only if location found
                $playerLocation->update($saveData);
            } else {
                // Location not found, handle error (optional)
                // You can throw an exception or return an error response here
                return response()->json([
                    'message' => 'Location not found for update.',
                ], 404);
            }
        }



        // 3. Return success response
        return response()->json([
            'message' => 'Location stored successfully.',
            'data'    => $playerLocation,
            'mode'    => $request->mode
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    // public function show(Request $request, $player, PlayerLocation $location)
    public function show(string $id)
    {


        Log::info('GET request received', [
            'id' => $id
        ]);
        // return new PlayerLocationResource($location);
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, string $id)
    public function update(Request $request)
    {
        // this is not working, so we work off of the 'mode' field in store()
        Log::info('PUT request received', [
            // 'id' => $id,
            'request_data' => $request->all()  // Log the complete request data
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($player_id, $id)
    {
        Log::info('DELETE request received', [
            'player_id' => $player_id,
            'id' => $id
        ]);


        try {
            // Validate that the player and location exist
            $location = Location::where('player_id', +$player_id)->where('id', $id)->firstOrFail();
            // $locations = Location::where('id', 5)->firstOrFail();

            // $locations = Location::all(); // Retrieve all locations

            // Delete the location
            $location->delete();

            Log::info('Location deleted successfully', [
                'l' => $location
            ]);

            // Return a success response
            return response()->json(['message' => 'Location has been deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            // Return an error response if the location is not found
            return response()->json([
                'message' => 'Location delete not found successfully.',
                'data'    => 3
            ], 201);

            // return response()->json(['error' => 'Location not found'], 404);
        } catch (\Exception $e) {


            Log::info('Location Exception successfully', [
                'e' => $e
            ]);

            return response()->json([
                'message' => 'Location delete found successfully.',
                'data'    => $e
            ], 201);

            // Handle any other exceptions
            // return response()->json(['error' => 'An unexpected error occurred'], 500);
            // return response()->json(['error' => 'An unexpected error occurred'], 201);
        }
    }
}
