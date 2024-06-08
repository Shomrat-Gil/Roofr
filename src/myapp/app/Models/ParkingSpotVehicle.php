<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ParkingSpotVehicle extends Pivot
{
    protected $fillable = ['parking_spot_id', 'vehicle_id', 'parking_start_at', 'parking_end_at'];
}
