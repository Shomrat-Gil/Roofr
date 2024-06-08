<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Vehicle extends Model
{
    use HasFactory;
    protected $fillable = [
        'license_plate',
        'type',
    ];

    public function parkingSpot(): BelongsToMany
    {
        return $this->belongsToMany(ParkingSpot::class, 'parking_spot_vehicles')
            ->withPivot('parking_start_at', 'parking_end_at')
            ->withTimestamps();
    }
}
