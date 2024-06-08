<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkingLot extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'total_spots'];

    public function parkingSpots()
    {
        return $this->hasMany(ParkingSpot::class);
    }
}
