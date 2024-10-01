<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{

    protected $fillable = [
        'name',
        'fence',
        'alerts',
        'radius',
        'latitude',
        'longitude',
        'player_id',
        'custom',
        'polygon_array',
        'zoom_level',
        'address'
    ];
    public function player()
    {
        return  $this->belongsTo(Player::class);
    }


    public function scopeInGeoFence($query, $latitude, $longitude)
    {
        $feet_to_meters = 0.3048;

        /// if we don't have coordinates make a query that returns nothing
        if (empty($latitude) or empty($longitude)) {
            return $query->whereNull('name');
        }

        return $query->havingRaw("distance <= radius")
            ->where('fence', true)
            ->selectRaw(
                "locations.*, 
                (  ST_Distance_Sphere( 
                    point(longitude, latitude), 
                    point($longitude, $latitude) 
                ) / $feet_to_meters ) as distance "
            );
    }
}
