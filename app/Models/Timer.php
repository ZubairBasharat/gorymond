<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timer extends Model
{
    protected $fillable = [
        'name',
        'duration',
        'reminders',
        'expires_at',
        'player_id'
    ];

    protected $casts = [
        'reminder' => 'array',
    ];

    //    protected $dates = ['expires_at'];

    public function player()
    {
        return  $this->belongsTo(Player::class);
    }
}
