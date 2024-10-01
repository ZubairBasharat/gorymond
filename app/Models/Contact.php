<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'name',
        'full_name',
        'text_to_contact',
        'call_to_contact',
        'contact_to_player',
        'phone',
        'avatar',
        'email'
    ];

    public function player()
    {
        return  $this->belongsTo(Player::class);
    }
}
