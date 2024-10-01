<?php

namespace App\Http\Resources;

use App\Library\ImageService;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "phone" => $this->phone,
            "email" => $this->email,
            "email_verified_at" => $this->email_verified_at,
            "role" => $this->role,
            "timezone" => $this->timezone,
            "email_notifications" => $this->email_notifications,
            "email_opt_in" => $this->email_opt_in,
            "sms_notifications" => $this->sms_notifications,
            "sms_opt_in" => $this->sms_opt_in,
            "avatar" => $this->avatar,
            "avatar_url" => $this->avatar? Storage::exists($this->avatar)? Storage::url($this->avatar): null : "",
            "registered_at" => $this->registered_at,
            "status" => $this->status,
            "stripe_id" => $this->stripe_id,
            "card_brand" => $this->card_brand,
            "card_last_four" => $this->card_last_four,
            "trial_ends_at" => $this->trial_ends_at,
            "current_location" => $this->current_location,
            "player" => $this->player
        ];
    }
}

/**
 * {"id":1,
 * "first_name":"admin",
 * "last_name":null,
 * "phone":null,
 * "email":"admin@www.com",
 * "email_verified_at":null,
 * "role":0,
 * "timezone":null,
 * "email_notifications":0,
 * "email_opt_in":0,
 * "sms_notifications":0,
 * "sms_opt_in":0,
 * "avatar":null,
 * "registered_at":null,
 * "status":0,
 * "created_at":"2019-11-18 21:16:50",
 * "updated_at":"2019-11-18 21:39:07",
 * "stripe_id":"cus_GChKFLDd0GAAlY",
 * "card_brand":null,
 * "card_last_four":null,
 * "trial_ends_at":null,
 * "current_location":null}}
 */