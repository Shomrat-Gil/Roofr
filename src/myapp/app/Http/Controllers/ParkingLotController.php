<?php

namespace App\Http\Controllers;

use App\Enums\VehicleTypes;
use App\Repositories\ParkingSpotRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ParkingLotController extends Controller
{
    protected ParkingSpotRepository $parkingSpotRepository;

    public function __construct(ParkingSpotRepository $parkingSpotRepository)
    {
        $this->parkingSpotRepository = $parkingSpotRepository;
    }

    public function index(string $vehicleType): JsonResponse
    {
        $validator = Validator::make(
            [
                'vehicleType' => $vehicleType
            ], [
            'vehicleType' => [Rule::enum(VehicleTypes::class)],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 412);
        }

        $parkingSpots = $this->parkingSpotRepository->findSpot($vehicleType);

        if(count($parkingSpots) === 0) {
            return response()->json(['message' => 'No suitable parking spot found']);
        }

        return response()->json($parkingSpots);
    }
}
