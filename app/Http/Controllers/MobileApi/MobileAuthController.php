<?php

namespace App\Http\Controllers\MobileApi;

use App\Http\Controllers\Controller;
use App\Http\Resources\PlayerResource;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\Password;
// use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class MobileAuthController extends Controller
{
    // use SendsPasswordResetEmails;

    public function login(Request $request)
    {
        Log::info('Request Payload: ' . json_encode($request->all()));

        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        Log::info('Request Credentials: ' . json_encode($credentials));

        if (!Auth::attempt($credentials)) {
            return Response::json(['error' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('mobile')->plainTextToken; // Sanctum token

        Log::info('Generated Token: ' . $token);
        // Log::info('User ID: ' . json_encode($user));
        // Log::info('Player: ' . json_encode($user->player));

        try {
            // Update player's api_token field with the generated token
            $user->player->update([
                'api_token' => $token
            ]);
        } catch (\Exception $exception) {
            Log::error('Error updating player API token: ' . $exception->getMessage());
            return Response::json(['error' => 'Failed to update API token'], 500);
        }

        return Response::json([
            'api_token' => $token,
            'token_type' => 'bearer',
            'player' => new PlayerResource($user->player),
        ]);
    }


    public function logout()
    {
        $user = Auth::user();
        $user->player->removeToken();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function register(Request $request)
    {
        // $player = auth()->user()->player()->find($request->player_id);

        // $token = $player->updateToken();

        return response()->json([
            'test' => 'test'
            // 'api_token' => $token,
            // 'token_type'   => 'bearer',
            // 'player' => new PlayerResource($player)
        ]);
    }

    public function passwordRecovery(Request $request)
    {

        $response = $this->validateEmail($request);
        if (!$response) {
            try {
                $user = User::where('email', request()->input('email'))->first();
                if (!$user) {
                    return response()->json(["error" => "User does not exist."], 400);
                }
                $token = Password::getRepository()->create($user);
                $user->sendPasswordResetNotification($token);
            } catch (\Exception $exception) {
                return response()->json(["error" => "Something went wrong."], 400);
            }
        } else {
            return response()->json($response, 400);
        }

        return response()->json(["error" => false], 200);
    }
}
