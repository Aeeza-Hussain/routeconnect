<?php

namespace Database\Factories;

use App\Models\Stop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Stop>
 */
class StopFactory extends Factory
{
    protected $model = Stop::class;

    public function definition(): array
    {
        return [
            'name'     => fake()->unique()->city(),
            'location' => fake()->address(),
            'status'   => 'Active',
        ];
    }
}
