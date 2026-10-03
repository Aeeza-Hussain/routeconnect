<?php

namespace Database\Factories;

use App\Models\Route;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Route>
 */
class RouteFactory extends Factory
{
    protected $model = Route::class;

    public function definition(): array
    {
        $cities = ['Gilgit', 'Hunza', 'Nagar', 'Danyor', 'Nomal', 'Aliabad', 'Skardu', 'Chilas'];
        $from   = fake()->randomElement($cities);
        $to     = fake()->randomElement(array_diff($cities, [$from]));

        return [
            'name'           => "{$from}–{$to}",
            'start_location' => $from,
            'end_location'   => $to,
            'status'         => 'Active',
            'start_stop_id'  => null,
            'end_stop_id'    => null,
        ];
    }
}
