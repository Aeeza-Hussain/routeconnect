<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Stop;
use App\Models\Route;
use App\Models\RouteStop;
use App\Models\Trip;
use App\Models\TripStop;
use App\Models\TravelRequest;
use App\Models\DriverApplication;

class MarketplaceSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure Stops exist
        $gilgit = Stop::firstOrCreate(
            ['name' => 'Gilgit'],
            ['location' => 'Gilgit Terminal', 'status' => 'Active']
        );
        $hunza = Stop::firstOrCreate(
            ['name' => 'Hunza'],
            ['location' => 'Karimabad Main', 'status' => 'Active']
        );
        $nagar = Stop::firstOrCreate(
            ['name' => 'Nagar'],
            ['location' => 'Nagar Valley', 'status' => 'Active']
        );
        $skardu = Stop::firstOrCreate(
            ['name' => 'Skardu'],
            ['location' => 'Skardu City', 'status' => 'Active']
        );

        // 2. Ensure Passenger exists
        $passenger = User::firstOrCreate(
            ['email' => 'passenger@routeconnect.com'],
            [
                'name'      => 'Aleeza Fatima',
                'password'  => Hash::make('password123'),
                'phone'     => '03009988776',
                'user_type' => 0, // Passenger
            ]
        );

        // 3. Drivers from Mockup
        // Driver 1: Ahmad Khan
        $driver1 = User::firstOrCreate(
            ['email' => 'ahmad.driver@routeconnect.com'],
            [
                'name'          => 'Ahmad Khan',
                'password'      => Hash::make('password123'),
                'phone'         => '03001234561',
                'user_type'     => 2,
                'driver_status' => 'approved',
            ]
        );

        // Driver 2: Saif Ullah
        $driver2 = User::firstOrCreate(
            ['email' => 'saif.driver@routeconnect.com'],
            [
                'name'          => 'Saif Ullah',
                'password'      => Hash::make('password123'),
                'phone'         => '03001234562',
                'user_type'     => 2,
                'driver_status' => 'approved',
            ]
        );

        // Driver 3: Bilal Ahmed
        $driver3 = User::firstOrCreate(
            ['email' => 'bilal.driver@routeconnect.com'],
            [
                'name'          => 'Bilal Ahmed',
                'password'      => Hash::make('password123'),
                'phone'         => '03001234563',
                'user_type'     => 2,
                'driver_status' => 'approved',
            ]
        );

        // 4. Vehicles from Mockup
        $vehicle1 = Vehicle::updateOrCreate(
            ['registration_no' => 'GLT-4488'],
            [
                'user_id'                 => $driver1->id,
                'model'                   => 'Toyota HiAce',
                'type'                    => 'Van',
                'total_seats'             => 12,
                'rental_rate_per_day'     => 5500.00,
                'is_available_for_rental' => true,
                'status'                  => 'approved',
                'image'                   => 'images/marketplace/hiace-van.jpg',
            ]
        );

        $vehicle2 = Vehicle::updateOrCreate(
            ['registration_no' => 'GLT-2211'],
            [
                'user_id'                 => $driver2->id,
                'model'                   => 'Toyota Corolla',
                'type'                    => 'Car',
                'total_seats'             => 4,
                'rental_rate_per_day'     => 4500.00,
                'is_available_for_rental' => true,
                'status'                  => 'approved',
                'image'                   => 'images/marketplace/corolla-car.jpg',
            ]
        );

        $vehicle3 = Vehicle::updateOrCreate(
            ['registration_no' => 'GLT-7733'],
            [
                'user_id'                 => $driver3->id,
                'model'                   => 'HiAce Daba',
                'type'                    => 'Daba',
                'total_seats'             => 14,
                'rental_rate_per_day'     => 5000.00,
                'is_available_for_rental' => true,
                'status'                  => 'approved',
                'image'                   => 'images/marketplace/hiace-daba.jpg',
            ]
        );

        // 5. Routes
        $routeGilgitHunza = Route::firstOrCreate(
            ['name' => 'Gilgit - Hunza Express'],
            [
                'start_location' => 'Gilgit',
                'end_location'   => 'Hunza',
                'start_stop_id'  => $gilgit->id,
                'end_stop_id'    => $hunza->id,
                'status'         => 'Active',
            ]
        );
        RouteStop::firstOrCreate(['route_id' => $routeGilgitHunza->id, 'stop_id' => $gilgit->id], ['stop_order' => 1]);
        RouteStop::firstOrCreate(['route_id' => $routeGilgitHunza->id, 'stop_id' => $nagar->id], ['stop_order' => 2]);
        RouteStop::firstOrCreate(['route_id' => $routeGilgitHunza->id, 'stop_id' => $hunza->id], ['stop_order' => 3]);

        $routeGilgitNagar = Route::firstOrCreate(
            ['name' => 'Gilgit - Nagar Route'],
            [
                'start_location' => 'Gilgit',
                'end_location'   => 'Nagar',
                'start_stop_id'  => $gilgit->id,
                'end_stop_id'    => $nagar->id,
                'status'         => 'Active',
            ]
        );
        RouteStop::firstOrCreate(['route_id' => $routeGilgitNagar->id, 'stop_id' => $gilgit->id], ['stop_order' => 1]);
        RouteStop::firstOrCreate(['route_id' => $routeGilgitNagar->id, 'stop_id' => $nagar->id], ['stop_order' => 2]);

        $routeGilgitSkardu = Route::firstOrCreate(
            ['name' => 'Gilgit - Skardu Route'],
            [
                'start_location' => 'Gilgit',
                'end_location'   => 'Skardu',
                'start_stop_id'  => $gilgit->id,
                'end_stop_id'    => $skardu->id,
                'status'         => 'Active',
            ]
        );
        RouteStop::firstOrCreate(['route_id' => $routeGilgitSkardu->id, 'stop_id' => $gilgit->id], ['stop_order' => 1]);
        RouteStop::firstOrCreate(['route_id' => $routeGilgitSkardu->id, 'stop_id' => $skardu->id], ['stop_order' => 2]);

        // 6. Trips matching reference image
        // Trip 1: Toyota HiAce (Van) • Ahmad Khan • Nov 02, 2026 • 09:00 AM • PKR 1,500
        $trip1 = Trip::firstOrCreate(
            [
                'user_id'    => $driver1->id,
                'vehicle_id' => $vehicle1->id,
                'route_id'   => $routeGilgitHunza->id,
                'trip_date'  => '2026-11-02',
            ],
            [
                'departure_time'        => '09:00:00',
                'expected_arrival_time' => '11:30:00',
                'available_seats'       => 8,
                'fare'                  => 1500.00,
                'pickup_point'          => 'Gilgit Bus Stand',
                'status'                => 'scheduled',
            ]
        );
        TripStop::firstOrCreate(['trip_id' => $trip1->id, 'stop_id' => $gilgit->id], ['stop_order' => 1, 'expected_time' => '09:00:00']);
        TripStop::firstOrCreate(['trip_id' => $trip1->id, 'stop_id' => $nagar->id],  ['stop_order' => 2, 'expected_time' => '10:15:00']);
        TripStop::firstOrCreate(['trip_id' => $trip1->id, 'stop_id' => $hunza->id],  ['stop_order' => 3, 'expected_time' => '11:30:00']);

        // Trip 2: Toyota Corolla (Car) • Saif Ullah • Nov 03, 2026 • 10:00 AM • PKR 2,000
        $trip2 = Trip::firstOrCreate(
            [
                'user_id'    => $driver2->id,
                'vehicle_id' => $vehicle2->id,
                'route_id'   => $routeGilgitNagar->id,
                'trip_date'  => '2026-11-03',
            ],
            [
                'departure_time'        => '10:00:00',
                'expected_arrival_time' => '11:15:00',
                'available_seats'       => 3,
                'fare'                  => 2000.00,
                'pickup_point'          => 'Gilgit Cantt Gate',
                'status'                => 'scheduled',
            ]
        );
        TripStop::firstOrCreate(['trip_id' => $trip2->id, 'stop_id' => $gilgit->id], ['stop_order' => 1, 'expected_time' => '10:00:00']);
        TripStop::firstOrCreate(['trip_id' => $trip2->id, 'stop_id' => $nagar->id],  ['stop_order' => 2, 'expected_time' => '11:15:00']);

        // Trip 3: HiAce Daba • Bilal Ahmed • Nov 05, 2026 • 08:00 AM • PKR 1,800
        $trip3 = Trip::firstOrCreate(
            [
                'user_id'    => $driver3->id,
                'vehicle_id' => $vehicle3->id,
                'route_id'   => $routeGilgitHunza->id,
                'trip_date'  => '2026-11-05',
            ],
            [
                'departure_time'        => '08:00:00',
                'expected_arrival_time' => '10:30:00',
                'available_seats'       => 10,
                'fare'                  => 1800.00,
                'pickup_point'          => 'Gilgit Stand #2',
                'status'                => 'scheduled',
            ]
        );
        TripStop::firstOrCreate(['trip_id' => $trip3->id, 'stop_id' => $gilgit->id], ['stop_order' => 1, 'expected_time' => '08:00:00']);
        TripStop::firstOrCreate(['trip_id' => $trip3->id, 'stop_id' => $nagar->id],  ['stop_order' => 2, 'expected_time' => '09:15:00']);
        TripStop::firstOrCreate(['trip_id' => $trip3->id, 'stop_id' => $hunza->id],  ['stop_order' => 3, 'expected_time' => '10:30:00']);

        // 7. Travel Requests from Mockup
        // Request 1: Gilgit → Hunza • 4 Passengers • Van preferred • Nov 04, 2026 • 09:00 AM • 3 Applications
        $req1 = TravelRequest::firstOrCreate(
            [
                'user_id'              => $passenger->id,
                'pickup_location'      => 'Gilgit',
                'destination_location' => 'Hunza',
                'travel_date'          => '2026-11-04',
            ],
            [
                'travel_time'  => '09:00 AM',
                'seats_needed' => 4,
                'budget'       => 1500.00,
                'notes'        => 'Van preferred',
                'status'       => 'open',
            ]
        );

        // Add 3 applications to req1
        DriverApplication::firstOrCreate(
            ['travel_request_id' => $req1->id, 'driver_id' => $driver1->id],
            ['vehicle_id' => $vehicle1->id, 'offered_fare' => 1500, 'proposed_time' => '09:00 AM', 'status' => 'pending']
        );
        DriverApplication::firstOrCreate(
            ['travel_request_id' => $req1->id, 'driver_id' => $driver2->id],
            ['vehicle_id' => $vehicle2->id, 'offered_fare' => 1600, 'proposed_time' => '09:15 AM', 'status' => 'pending']
        );
        DriverApplication::firstOrCreate(
            ['travel_request_id' => $req1->id, 'driver_id' => $driver3->id],
            ['vehicle_id' => $vehicle3->id, 'offered_fare' => 1500, 'proposed_time' => '09:00 AM', 'status' => 'pending']
        );

        // Request 2: Nagar → Hunza • 2 Passengers • Car preferred • Nov 05, 2026 • 11:00 AM • 1 Application
        $req2 = TravelRequest::firstOrCreate(
            [
                'user_id'              => $passenger->id,
                'pickup_location'      => 'Nagar',
                'destination_location' => 'Hunza',
                'travel_date'          => '2026-11-05',
            ],
            [
                'travel_time'  => '11:00 AM',
                'seats_needed' => 2,
                'budget'       => 2000.00,
                'notes'        => 'Car preferred',
                'status'       => 'open',
            ]
        );
        DriverApplication::firstOrCreate(
            ['travel_request_id' => $req2->id, 'driver_id' => $driver2->id],
            ['vehicle_id' => $vehicle2->id, 'offered_fare' => 2000, 'proposed_time' => '11:00 AM', 'status' => 'pending']
        );

        // Request 3: Gilgit → Skardu • 3 Passengers • Daba preferred • Nov 06, 2026 • 07:00 AM • 0 Applications
        $req3 = TravelRequest::firstOrCreate(
            [
                'user_id'              => $passenger->id,
                'pickup_location'      => 'Gilgit',
                'destination_location' => 'Skardu',
                'travel_date'          => '2026-11-06',
            ],
            [
                'travel_time'  => '07:00 AM',
                'seats_needed' => 3,
                'budget'       => 1800.00,
                'notes'        => 'Daba preferred',
                'status'       => 'open',
            ]
        );
    }
}

