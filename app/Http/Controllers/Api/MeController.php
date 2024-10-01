<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Library\ImageService;
// use App\Notifications\EmailOptIn;
// use App\Notifications\SMSOptIn;
use App\Models\User;
use Illuminate\Http\Request;

class MeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        return auth()->user();
    }

    public function update(UserUpdateRequest $request)
    {

        $user = auth()->user();

        $data = $request->only(
            [
                'first_name',
                'last_name',
                'email',
                'email_notifications',
                'phone',
                'sms_notifications',
                'timezone'
            ]
        );
        if ($request->password) {
            $user->password = bcrypt($request->password);
            $user->save();
        }

        $user->update($data);

        return new UserResource($user->refresh());
    }

    public function avatar(Request $request)
    {
        $user = auth()->user();
        if ($request->hasFile('avatar')) {
            $filename = ImageService::uploadProfileImage($request->file('avatar'), $user->avatar ?? null);
            $data['avatar'] = $filename;
            $user->update($data);
        }

        return new UserResource($user);
    }

}
