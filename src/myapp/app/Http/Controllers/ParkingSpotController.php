<?php

namespace App\Http\Controllers;

use App\Http\Requests\ParkingSpotParkRequest;
use App\Http\Requests\ParkingSpotUnParkRequest;
use Illuminate\Http\JsonResponse;
use App\Repositories\ParkingSpotRepository;

class ParkingSpotController extends Controller
{
    protected ParkingSpotRepository $parkingSpotRepository;

    public function __construct(ParkingSpotRepository $parkingSpotRepository)
    {
        $this->parkingSpotRepository = $parkingSpotRepository;
    }

    public function park(ParkingSpotParkRequest $request): JsonResponse
    {
        $this->parkingSpotRepository->park($request->validated());

        return response()->json([
            'success' => true,
            'message' => trans('Vehicle parked successfully')
        ]);
    }

    public function unPark(ParkingSpotUnParkRequest $request): JsonResponse
    {
        $this->parkingSpotRepository->unPark($request->validated());

        return response()->json([
            'success' => true,
            'message' => trans('Vehicle un parked successfully')]
        );
    }
}
