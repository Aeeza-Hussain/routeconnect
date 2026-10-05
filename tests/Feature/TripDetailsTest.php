<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Route;
use App\Models\Stop;
use App\Models\RouteStop;
use App\Models\Trip;
use App\Models\TripStop;
use App\Models\TripMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TripDetailsTest extends TestCase
{
    use RefreshDatabase;

    private User $driver;
    private Vehicle $vehicle;
    private Route $route;
    private Stop $gilgitStop;
    private Stop $nagarStop;
    private Stop $hunzaStop;
    private Trip $tripWithSeats;
    private Trip $fullyBookedTrip;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Driver
        $this->driver = User::factory()->create([
            'name'          => 'Ahmed Khan',
            'email'         => 'ahmed.driver@routeconnect.test',
            'phone'         => '0300-1234567',
            'user_type'     => 2,
            'driver_status' => 'approved',
            'bio'           => '10 years experience driving mountainous Karakoram highway.',
        ]);

        // 2. Vehicle
        $this->vehicle = Vehicle::factory()->create([
            'user_id'         => $this->driver->id,
            'type'            => 'Coaster Saloon',
            'registration_no' => 'GB-7788',
            'model'           => 'Toyota Coaster 2023',
            'total_seats'     => 22,
            'status'          => 'Active',
        ]);

        // 3. Stops
        $this->gilgitStop = Stop::create(['name' => 'Gilgit Terminal', 'location' => 'Gilgit City', 'status' => 'Active']);
        $this->nagarStop  = Stop::create(['name' => 'Nagar Khas',      'location' => 'Nagar Valley', 'status' => 'Active']);
        $this->hunzaStop  = Stop::create(['name' => 'Aliabad Hunza',   'location' => 'Hunza Valley', 'status' => 'Active']);

        // 4. Route
        $this->route = Route::create([
            'name'           => 'Karakoram Express Route',
            'start_location' => 'Gilgit Terminal',
            'end_location'   => 'Aliabad Hunza',
            'status'         => 'Active',
            'start_stop_id'  => $this->gilgitStop->id,
            'end_stop_id'    => $this->hunzaStop->id,
        ]);

        RouteStop::create(['route_id' => $this->route->id, 'stop_id' => $this->gilgitStop->id, 'stop_order' => 1]);
        RouteStop::create(['route_id' => $this->route->id, 'stop_id' => $this->nagarStop->id,  'stop_order' => 2]);
        RouteStop::create(['route_id' => $this->route->id, 'stop_id' => $this->hunzaStop->id,  'stop_order' => 3]);

        // 5. Trip with seats
        $this->tripWithSeats = Trip::create([
            'user_id'         => $this->driver->id,
            'vehicle_id'      => $this->vehicle->id,
            'route_id'        => $this->route->id,
            'trip_date'       => '2026-11-20',
            'departure_time'  => '09:00:00',
            'available_seats' => 12,
            'fare'            => 1500.00,
            'pickup_point'    => 'Gilgit Central Station',
            'status'          => 'Scheduled',
        ]);

        TripStop::create([
            'trip_id'       => $this->tripWithSeats->id,
            'stop_id'       => $this->nagarStop->id,
            'stop_order'    => 2,
            'expected_time' => '10:45:00',
        ]);

        TripStop::create([
            'trip_id'       => $this->tripWithSeats->id,
            'stop_id'       => $this->hunzaStop->id,
            'stop_order'    => 3,
            'expected_time' => '12:30:00',
        ]);

        // 6. Trip with 0 seats (Fully booked)
        $this->fullyBookedTrip = Trip::create([
            'user_id'         => $this->driver->id,
            'vehicle_id'      => $this->vehicle->id,
            'route_id'        => $this->route->id,
            'trip_date'       => '2026-11-21',
            'departure_time'  => '14:00:00',
            'available_seats' => 0,
            'fare'            => 1500.00,
            'pickup_point'    => 'Gilgit Central Station',
            'status'          => 'Scheduled',
        ]);
    }

    public function test_trip_details_page_loads_successfully_for_valid_trip(): void
    {
        $response = $this->get("/trips/{$this->tripWithSeats->id}");

        $response->assertStatus(200);
        $response->assertViewIs('frontend.trips_show');
    }

    public function test_trip_details_shows_route_name(): void
    {
        $response = $this->get("/trips/{$this->tripWithSeats->id}");

        $response->assertStatus(200);
        $response->assertSee('Karakoram Express Route');
        $response->assertSee('Gilgit Terminal');
        $response->assertSee('Aliabad Hunza');
    }

    public function test_trip_details_shows_driver_information(): void
    {
        $response = $this->get("/trips/{$this->tripWithSeats->id}");

        $response->assertStatus(200);
        $response->assertSee('Ahmed Khan');
        $response->assertSee('10 years experience driving mountainous Karakoram highway.');
    }

    public function test_trip_details_shows_vehicle_specifications(): void
    {
        $response = $this->get("/trips/{$this->tripWithSeats->id}");

        $response->assertStatus(200);
        $response->assertSee('GB-7788');
        $response->assertSee('Toyota Coaster 2023');
        $response->assertSee('Coaster Saloon');
        $response->assertSee('22 Passenger Seats');
    }

    public function test_trip_details_shows_departure_date_and_time(): void
    {
        $response = $this->get("/trips/{$this->tripWithSeats->id}");

        $response->assertStatus(200);
        $response->assertSee('09:00 AM');
        $response->assertSee('2026-11-20');
        $response->assertSee('Nov 20, 2026');
    }

    public function test_trip_details_shows_available_seats(): void
    {
        $response = $this->get("/trips/{$this->tripWithSeats->id}");

        $response->assertStatus(200);
        $response->assertSee('12 Open Seats');
    }

    public function test_trip_details_shows_complete_ordered_stops_with_expected_times(): void
    {
        $response = $this->get("/trips/{$this->tripWithSeats->id}");

        $response->assertStatus(200);
        $response->assertSee('Complete Ordered Stops');
        $response->assertSee('Gilgit Terminal');
        $response->assertSee('Nagar Khas');
        $response->assertSee('Aliabad Hunza');
        $response->assertSee('10:45 AM');
        $response->assertSee('12:30 PM');
    }

    public function test_trip_details_shows_driver_trip_messages_when_present(): void
    {
        TripMessage::create([
            'trip_id' => $this->tripWithSeats->id,
            'user_id' => $this->driver->id,
            'message' => 'Road clearance work in progress near Nagar. We might experience a 10-minute brief delay.',
        ]);

        $response = $this->get("/trips/{$this->tripWithSeats->id}");

        $response->assertStatus(200);
        $response->assertSee("Driver's Trip Messages", false);
        $response->assertSee('Road clearance work in progress near Nagar');
        $response->assertSee('Ahmed Khan');
    }

    public function test_trip_details_shows_empty_message_state_when_no_driver_messages(): void
    {
        $response = $this->get("/trips/{$this->tripWithSeats->id}");

        $response->assertStatus(200);
        $response->assertSee("Driver's Trip Messages", false);
        $response->assertSee('No trip messages or updates posted yet by the driver.');
    }

    public function test_book_seats_button_is_enabled_when_seats_are_available(): void
    {
        $response = $this->get("/trips/{$this->tripWithSeats->id}");

        $response->assertStatus(200);
        $response->assertSee('Book Seats');
        $response->assertSee(route('booking.index', ['trip_id' => $this->tripWithSeats->id]));
        $response->assertDontSee('disabled id="book-seats-btn"', false);
    }

    public function test_booking_is_disabled_and_shows_fully_booked_when_no_seats_available(): void
    {
        $response = $this->get("/trips/{$this->fullyBookedTrip->id}");

        $response->assertStatus(200);
        $response->assertSee('Fully Booked');
        $response->assertSee('0 Seats');
        $response->assertSee('disabled id="book-seats-btn"', false);
        $response->assertDontSee('href="' . route('booking.index', ['trip_id' => $this->fullyBookedTrip->id]) . '"', false);
    }

    public function test_non_existent_trip_returns_404(): void
    {
        $response = $this->get('/trips/999999');

        $response->assertStatus(404);
    }

    public function test_invalid_non_numeric_trip_id_returns_404(): void
    {
        $response = $this->get('/trips/invalid-id');

        $response->assertStatus(404);
    }
}
