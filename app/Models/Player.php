<?php

namespace App\Models;

use App\Enums\ActivityType;
use App\Enums\ToneSituationType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;


use Illuminate\Support\Facades\Log;


class Player extends \Illuminate\Foundation\Auth\User
{
    use Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'pin',
        'phone',
        'event_display_number',
        'reminder_alerts',
        'avatar',
        'push_token',
        'api_token',
    ];

    protected $hidden = ['api_token'];

    /**
     *
     * Relationships
     *
     */

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coach()
    {
        return 0;
        // return $this->belongsTo(User::class, 'coach_id', 'id');
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function activity_logs()
    {
        // return $this->hasMany(ActivityLog::class);
    }

    public function timers()
    {
        Log::info('times TIMER');


        // return $this->hasMany(Location::class);
        // return $this->current_location();
        return $this->hasMany(Timer::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function tones()
    {
        return $this->belongsToMany(Tone::class, 'player_tones')
            ->withPivot('activity_type', 'situation_type');
    }


    /**
     * API Token Methods
     */

    public function updateToken()
    {
        $token = Str::random(80);

        $this->forceFill([
            'api_token' => hash('sha256', $token),
        ])->save();

        return $token;
    }

    public function removeToken()
    {
        $this->api_token = null;
        $this->save();
    }

    /**
     * Push token for Onesignal
     * It's actually the userId assigned by Onesignal since that's what's need to push
     * @return mixed
     */
    public function routeNotificationForOneSignal()
    {
        return $this->push_token;
    }

    /**
     * Determine if player is in a
     * @return mixed
     */
    public function current_location()
    {
        return $this->hasOne(Location::class)
            ->inGeoFence($this->latitude, $this->longitude);
    }


    /**
     * Takes an array removes all tones of activity type and adds ids listed
     *  [
     *   'model' => {Activity Type},
     *   'complete' => {Tone id},
     *   'reminder' => {Tone id},
     *  ]
     * @param $data
     * @return $this|void
     */
    public function syncTones($data)
    {

        if (
            empty($data) || empty($data['model']) ||
            !in_array(
                $data['model'],
                [
                    ActivityType::Call,
                    ActivityType::Event,
                    ActivityType::Timer,
                    ActivityType::Location,
                    ActivityType::Text
                ]
            )
        ) {
            return;
        }
        // if (empty($data) || empty($data['model']) || !ActivityType::hasKey($data['model'])) return;

        /// remove all player tones for the type given activity type (Event, Timer)
        $this->tones()
            ->wherePivot('activity_type', ActivityType::getValue($data['model']))
            ->detach();

        /// add timers back 1 by 1 because they could contain the same id for both situation types
        /// and sync methods doesn't support that
        if (!empty($data['complete'])) {
            $tone = [
                $data['complete'] => [
                    'activity_type' => ActivityType::getValue($data['model']),
                    'situation_type' => ToneSituationType::Complete()->value
                ]
            ];
            $this->tones()->attach($tone);
        }

        if (!empty($data['reminder'])) {
            $tone = [
                $data['reminder'] => [
                    'activity_type' => ActivityType::getValue($data['model']),
                    'situation_type' => ToneSituationType::Reminder()->value
                ]
            ];
            $this->tones()->attach($tone);
        }

        return $this;
    }
}
