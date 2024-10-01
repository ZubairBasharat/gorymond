<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\PlayerConfigurationController;
use App\Http\Controllers\Api\PlayerContactController;
use App\Http\Controllers\Api\PlayerLocationController;
use App\Http\Controllers\MobileApi\MeController;
use App\Http\Controllers\MobileApi\MobileAuthController;
use App\Http\Controllers\MobileApi\MobileTimerController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\PlayerTimerController;
use App\Http\Controllers\Api\PlayerToneController;
use App\Http\Controllers\Api\ToneController;
use App\Http\Controllers\Api\PlayerEventController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('me', [MeController::class, 'update']);
    Route::get('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('players/configuration/{player}', [PlayerConfigurationController::class, 'show']);


    // this is misnamed, as it should be 'player' instead of 'players'
    Route::apiResource('/players', PlayerController::class);
    Route::get('tones', [ToneController::class, 'index']);
    Route::group(['prefix' => 'players/{player}'], function () {
        Route::apiResource('contacts', PlayerContactController::class);

        Route::apiResource('locations', PlayerLocationController::class);

        Route::apiResource('timers', PlayerTimerController::class);
        Route::apiResource('events', PlayerEventController::class);
        Route::get('tones', [PlayerToneController::class, 'index']);
        Route::post('tones', [PlayerToneController::class, 'update']);
        Route::post('avatar', [PlayerController::class, 'avatar']);
        Route::post('contacts/{contact}/avatar', [PlayerContactController::class, 'avatar']);
    });
});


/**
 * Mobile Routes
 * base_url/api/app/
 */
Route::group(['prefix' => 'app'], function () {
    Route::post('location', [MeController::class, 'postLocation']);
    Route::get('location/last', [MeController::class, 'getLastLocation']);

    Route::get('timers', [MobileTimerController::class, 'index']);

    /**
     * Mobile Auth Routes
     */

    Route::post('/login', [MobileAuthController::class, 'login']);
    // Route::post('/reset', [MobileApi\AuthController::class, 'passwordRecovery']);

    Route::group(['middleware' => 'auth:api'], function () {
        // Route::put('me/coordinates', [MeController::class, 'updateCoordinates']);


        // Route::post('/register', 'MobileApi\AuthController@register');
    });
});
