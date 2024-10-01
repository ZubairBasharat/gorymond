<?php

namespace App\Http\Controllers\Api;

use App\Models\Contact;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContactRequest;
use App\Http\Resources\ContactResource;
use App\Library\ImageService;
use App\Models\Player;
use Illuminate\Http\Request;

class PlayerContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Player $player
     * @return \Illuminate\Http\Response
     */
    public function index(Player $player)
    {
        return ContactResource::collection($player->contacts);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param ContactRequest $request
     * @param Player $player
     * @return Contact
     */
    public function store(ContactRequest $request, Player $player)
    {
        $data = $request->toArray();
        if($request->hasFile('avatar')) {
            $filename = ImageService::uploadProfileImage($this->request->file('avatar'));
            $data['avatar'] = $filename;
        }
        $contact = $player->contacts()->create($data);

        return $contact;
    }

    /**
     * Display the specified resource.
     *
     * @param Player $player
     * @param Contact $contact
     * @return ContactResource
     */
    public function show(Player $player, Contact $contact)
    {
        return new ContactResource($contact);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param ContactRequest $request
     * @param Player $player
     * @param Contact $contact
     * @return ContactResource
     */
    public function update(ContactRequest $request, Player $player, Contact $contact)
    {
        $data = $request->toArray();
        if($request->hasFile('avatar')) {
            $filename = ImageService::uploadProfileImage($this->request->file('avatar'));
            $data['avatar'] = $filename;
        }
        $contact->update($data);

        return new ContactResource($contact->refresh());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Player $player
     * @param Contact $contact
     * @return void
     * @throws \Exception
     */
    public function destroy( Player $player, Contact $contact)
    {
        if( $contact->delete()) {
            return response()->json('Success', 200);
        } else {
            return response()->json('Error', 500);
        }
    }

    public function avatar(Request $request, Player $player, Contact $contact)
    {

        if ($request->hasFile('avatar')) {
            $filename = ImageService::uploadProfileImage($request->file('avatar'), $contact->avatar ?? null);
            $data['avatar'] = $filename;
            $contact->update($data);
        }

        return new ContactResource($contact->refresh());
    }
}
