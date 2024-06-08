<?php

namespace App\Http\Requests;

use App\Models\ParkingSpot;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ParkingSpotUnParkRequest extends FormRequest
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
            $this->validateParkingSpot($validator);
        });
    }
    private function validateParkingSpot(Validator $validator): void
    {
        $ids = $this->input('ids');
        $licensePlate = $this->input('license_plate');
        $unAvailableCount = ParkingSpot::query()
            ->whereIn('id', $ids)
            ->where('is_occupied', true)
            ->whereHas('vehicles', function ($query) use ($licensePlate) {
                $query->where('license_plate', $licensePlate)
                    ->wherePivotNull('parking_end_at');
            })
            ->count();

        if ($unAvailableCount !== count($ids)) {
            $validator->errors()->add(
                'ids',
                trans('Some of the parking spots are not attached tho this vehicle.')
            );
        }
    }
}
