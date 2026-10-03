<?php

namespace Database\Factories;

use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition(): array
    {
        return [
            'user_id'         => User::factory(),
            'type'            => fake()->randomElement(['Mini Bus', 'Coaster', 'Van', 'Bus']),
            'registration_no' => strtoupper(fake()->unique()->bothify('??-###-??')),
            'model'           => fake()->randomElement(['Toyota', 'Hino', 'Isuzu', 'Suzuki']) . ' ' . fake()->year(),
            'total_seats'     => fake()->numberBetween(10, 50),
            'status'          => 'Active',
        ];
    }
}
