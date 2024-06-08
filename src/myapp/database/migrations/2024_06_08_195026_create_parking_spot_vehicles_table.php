<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('parking_spot_vehicles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parking_spot_id');
            $table->unsignedBigInteger('vehicle_id');
            $table->timestamp('parking_start_at')->nullable();
            $table->timestamp('parking_end_at')->nullable();
            $table->timestamps();

            $table->foreign('parking_spot_id')->references('id')->on('parking_spots');
            $table->foreign('vehicle_id')->references('id')->on('vehicles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parking_spot_vehicles');
    }
};
