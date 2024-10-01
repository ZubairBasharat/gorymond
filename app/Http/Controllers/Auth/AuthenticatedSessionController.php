<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
// use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // return response()->noContent();
        // echo to log file

        error_log('AuthenticatedSessionController@store: $userData: ' . __LINE__);

        $userData = [
            'id' => auth()->user()->id,
            'first_name' => auth()->user()->first_name,
            'last_name' => auth()->user()->last_name,
            'email' => auth()->user()->email
        ];

        // echo to error_log
        error_log('AuthenticatedSessionController@store: $userData: ' . print_r($userData, true));

        // Return JSON response with user data and appropriate status code
        return response()->json($userData, 200);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Logged out successfully and session invalidated.'
        ], 200);
    }
}
