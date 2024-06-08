<?php

namespace App\Repositories;

use App\Models\ParkingSpot;
use App\Models\Vehicle;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ParkingSpotRepository
{
    public function findSpot(string $vehicleType): array
    {
        $results = [];

        $spotSize = config('vehicle_accommodation.' . $vehicleType . '.spot_size');

        $availableSpots = $this->getAvailableSpots();

        if($spotSize === 0 || $availableSpots->count() === 0) {
            return $results;
        }

        if($spotSize === 1) {
            $results = $this->singleAvailableSpots($availableSpots);
        } elseif ($availableSpots->count() >= $spotSize){
            $results = $this->multiAvailableSpots($availableSpots, $spotSize);
        }

        return $results;
    }

    public function park(array $requestValidated): void
    {
        /** @var Vehicle $vehicle */
        $vehicle = Vehicle::query()
            ->where('license_plate', $requestValidated['license_plate'])
            ->firstOrFail();

        $vehicle->parkingSpot()->attach($requestValidated['ids'], ['parking_start_at' => now()]);

        ParkingSpot::query()->whereIn('id', $requestValidated['ids'])->update(['is_occupied' => true]);
    }

    public function unPark(array $requestValidated): void
    {
        /** @var Vehicle $vehicle */
        $vehicle = Vehicle::query()
            ->where('license_plate', $requestValidated['license_plate'])
            ->withWhereHas('parkingSpot', function ($query) use($requestValidated) {
                $query->whereIn('id', $requestValidated['ids']);
            })
            ->firstOrFail();

        $now = now();
        foreach ($vehicle->getRelationValue('parkingSpot') as $parkingSpot ){
            $parkingSpot->vehicles()->updateExistingPivot($vehicle->getKey(), ['parking_end_at' => $now]);
            $parkingSpot->setAttribute('is_occupied', false);
            $parkingSpot->save();
        }
    }

    private function getAvailableSpots(): Collection
    {
        return ParkingSpot::query()
            ->where('is_occupied', false)
            ->orderBy('parking_lot_id')
            ->orderBy('aisle')
            ->orderBy('id')
            ->get();
    }

    private function singleAvailableSpots(Collection $availableSpots): array
    {
        $results = [];
        foreach($availableSpots as $availableSpot) {
            $results[] = [
                'parking_lot_id' => $availableSpot->getAttribute('parking_lot_id'),
                'aisle' => $availableSpot->getAttribute('aisle'),
                'ids' => [$availableSpot->getKey()]
            ];
        }
        return $results;
    }
    private function multiAvailableSpots(Collection $availableSpots, int $spotSize): array
    {
        $availableSpots = $availableSpots->groupBy(function ($item) {
            return $item->parking_lot_id . '-' . $item->aisle;
        });
        $results = [];

        foreach ($availableSpots as $groupKey => $spots) {
            $spotIds = $spots->pluck('id')->toArray();

            for ($i = 0; $i <= count($spotIds) - $spotSize; $i++) {
                $consecutive = true;

                for ($j = 1; $j < $spotSize; $j++) {
                    if ($spotIds[$i + $j] != $spotIds[$i] + $j) {
                        $consecutive = false;
                        break;
                    }
                }

                if ($consecutive) {
                    $resultSpots = array_slice($spotIds, $i, $spotSize);
                    $keys = explode('-', $groupKey);
                    $results[] = [
                        'parking_lot_id' => $keys[0],
                        'aisle' => $keys[1],
                        'ids' => $resultSpots
                    ];
                    $i += $spotSize - 1; // Jump to the end of the found sequence
                }
            }
        }
        return $results;
    }
}
