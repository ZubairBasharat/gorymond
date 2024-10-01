<?php

namespace App\Models;

use App\Enums\ActivityType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'start',
        'end',
        'name',
        'meet_with',
        'location_name',
        'location_id',
        'notes',
        'meet_with',
        'reminders',
        'recurring_active',
        'repeat_options',
        'recurring_days',
        'end_date_options',
        'player_id',
        'parent_id'
    ];

    protected $casts = [

        'reminders' => 'array',
        'end_date_options' => 'array',
        'recurring_days' => 'array',
        'recurring_active' => 'boolean'
    ];

    protected $dates = [
        'start', 'end'
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'parent_id', 'id');
    }

    public function parent() {
        return $this->belongsTo(Event::class, 'parent_id', 'id');
    }

    public function event_reminders()
    {
        return $this->hasMany(EventReminder::class);
    }

    public function activity_log()
    {
        return $this->morphOne(ActivityLog::class, 'loggable');
    }


    public function scopeDue($query, Carbon $datetime = null, $minutes = 1)
    {
        $datetime = $datetime ?: now();
        $start = clone ($datetime)->setSeconds(0);
        $end = clone ($start)->addMinutes($minutes);

        return $query->whereBetween('start', [$start, $end]);

    }


    public function addActivityLog(string $details = null)
    {
        // check if log exist
        if (!$this->activity_log) {
            $this->activity_log()->create([
                'date' => now(),
                'details' => $details ?? $this->name,
                'interacted_with' => $this->meet_with . ' @ ' . $this->location_name,
                'type' => ActivityType::Event,
                'action' => 'event',
                'player_id' => $this->player_id
            ]);
        }
        // add update

    }

    public function updateActivityLog($text = null)
    {
        if (!$this->activity_log) {
            $this->addActivityLog();
        }

        $this->refresh()
            ->activity_log->updates()->create([
            'date' => now(),
                'text' => $text ?? 'Event',
        ]);

        $log = $this->activity_log;
        $log->date = now();
        $log->save();
    }
}
