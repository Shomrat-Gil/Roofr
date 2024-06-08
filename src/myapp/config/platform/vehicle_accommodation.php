<?php

use App\Enums\VehicleTypes;

return [
    VehicleTypes::MOTORCYCLE->value => [
        'spot_size' => 1,
    ],
    VehicleTypes::CAR->value => [
        'spot_size' => 1,
    ],
    VehicleTypes::VAN->value => [
        'spot_size' => 3,
    ],
];
