<?php

namespace App\Models;
use App\Models\Event;
use App\Enums\ActivityType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class RecurringEvent extends Model
{
    protected $fillable = [
        'event_id',
        'date',
        'parent_event'
    ];

    public function events()
    {
        return $this->belongsTo(Event::class, 'event_id', 'id');
    }
}
