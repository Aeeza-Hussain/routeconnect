<?php

namespace Database\Factories;

use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Route;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Trip>
 */
class TripFactory extends Factory
{
    protected $model = Trip::class;

    public function definition(): array
    {
        return [
            'user_id'         => User::factory(),
            'vehicle_id'      => Vehicle::factory(),
            'route_id'        => Route::factory(),
            'trip_date'       => fake()->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'departure_time'  => fake()->time('H:i:s'),
            'available_seats' => fake()->numberBetween(5, 30),
            'fare'            => fake()->randomFloat(2, 100, 2000),
            'pickup_point'    => fake()->optional()->city(),
            'status'          => 'Scheduled',
        ];
    }
}
