<?php

namespace App\Http\Requests;

use App\Enums\VehicleTypes;
use App\Models\ParkingSpot;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ParkingSpotParkRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        //return auth()->check(); TO-DO: use once implement under auth account
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ids' => 'required|array',
            'ids.*' => 'integer',
            'license_plate' => 'required|string',//TO-DO: Add regex
            'vehicleType' => [Rule::enum(VehicleTypes::class)],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  Validator  $validator
     * @return void
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $ids = $this->input('ids');
            // Check if all provided ids are still available
            $availableCount = ParkingSpot::query()->whereIn('id', $ids)->where('is_occupied', false)->count();

            if ($availableCount !== count($ids)) {
                $validator->errors()->add(
                    'ids',
                    trans('Some of the parking spots are not available.')
                );
            }
        });
    }
}
