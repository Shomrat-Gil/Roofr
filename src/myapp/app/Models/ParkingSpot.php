<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ParkingSpot extends Model
{
    use HasFactory;
    protected $fillable = [
        'parking_lot_id',
        'type',
        'is_occupied',
    ];

    public function parkingLot(): BelongsTo
    {
        return $this->belongsTo(ParkingLot::class);
    }

    public function vehicles(): BelongsToMany
    {
        return $this->belongsToMany(Vehicle::class, 'parking_spot_vehicles')
            ->withPivot('parking_start_at', 'parking_end_at')
            ->withTimestamps();
    }
}
