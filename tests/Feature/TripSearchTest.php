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
use Illuminate\Foundation\Testing\RefreshDatabase;

class TripSearchTest extends TestCase
{
    use RefreshDatabase;

    private User $driver;
    private Vehicle $vehicle;
    private Route $route;
    private Stop $gilgitStop;
    private Stop $nagarStop;
    private Stop $hunzaStop;
    private Stop $unrelatedStop;
    private Trip $scheduledTrip;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Driver
        $this->driver = User::factory()->create([
            'name'          => 'Kareem Driver',
            'user_type'     => 2,
            'driver_status' => 'approved',
        ]);

        // 2. Vehicle
        $this->vehicle = Vehicle::factory()->create([
            'user_id'         => $this->driver->id,
            'type'            => 'HiAce Van',
            'registration_no' => 'GLT-9900',
            'model'           => 'Toyota HiAce GL',
            'total_seats'     => 14,
            'status'          => 'Active',
        ]);

        // 3. Stops
        $this->gilgitStop    = Stop::create(['name' => 'Gilgit', 'location' => 'Gilgit City', 'status' => 'Active']);
        $this->nagarStop     = Stop::create(['name' => 'Nagar',  'location' => 'Nagar Valley', 'status' => 'Active']);
        $this->hunzaStop     = Stop::create(['name' => 'Hunza',  'location' => 'Karimabad', 'status' => 'Active']);
        $this->unrelatedStop = Stop::create(['name' => 'Skardu', 'location' => 'Baltistan', 'status' => 'Active']);

        // 4. Route: Gilgit → Hunza
        $this->route = Route::create([
            'name'           => 'Gilgit - Hunza Corridor',
            'start_location' => 'Gilgit',
            'end_location'   => 'Hunza',
            'status'         => 'Active',
            'start_stop_id'  => $this->gilgitStop->id,
            'end_stop_id'    => $this->hunzaStop->id,
        ]);

        // Route stops order: 1: Gilgit, 2: Nagar, 3: Hunza
        RouteStop::create(['route_id' => $this->route->id, 'stop_id' => $this->gilgitStop->id, 'stop_order' => 1]);
        RouteStop::create(['route_id' => $this->route->id, 'stop_id' => $this->nagarStop->id,  'stop_order' => 2]);
        RouteStop::create(['route_id' => $this->route->id, 'stop_id' => $this->hunzaStop->id,  'stop_order' => 3]);

        // 5. Trip: Scheduled for 2026-10-15 at 08:00 AM with 10 available seats
        $this->scheduledTrip = Trip::create([
            'user_id'         => $this->driver->id,
            'vehicle_id'      => $this->vehicle->id,
            'route_id'        => $this->route->id,
            'trip_date'       => '2026-10-15',
            'departure_time'  => '08:00:00',
            'available_seats' => 10,
            'fare'            => 1200.00,
            'pickup_point'    => 'Gilgit Bus Stand',
            'status'          => 'scheduled',
        ]);

        // Add TripStops with expected arrival times
        TripStop::create([
            'trip_id'       => $this->scheduledTrip->id,
            'stop_id'       => $this->gilgitStop->id,
            'stop_order'    => 1,
            'expected_time' => '08:00:00',
        ]);
        TripStop::create([
            'trip_id'       => $this->scheduledTrip->id,
            'stop_id'       => $this->nagarStop->id,
            'stop_order'    => 2,
            'expected_time' => '09:30:00',
        ]);
        TripStop::create([
            'trip_id'       => $this->scheduledTrip->id,
            'stop_id'       => $this->hunzaStop->id,
            'stop_order'    => 3,
            'expected_time' => '11:00:00',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 1. PUBLIC PAGE ACCESSIBILITY
    // ─────────────────────────────────────────────────────────────────────────

    public function test_public_can_access_trips_search_page(): void
    {
        $response = $this->get('/trips');
        $response->assertStatus(200);
        $response->assertSee('Find Scheduled Trips');
        $response->assertSee('Search Trips');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 2. STOP ORDER SPECIFICATION & EXAMPLE (Nagar -> Hunza on Gilgit -> Hunza trip)
    // ─────────────────────────────────────────────────────────────────────────

    public function test_gilgit_to_hunza_trip_appears_when_searching_nagar_to_hunza(): void
    {
        // Example from requirement: Gilgit → Hunza trip should appear when searching Nagar → Hunza
        $response = $this->get('/trips?from=Nagar&to=Hunza&date=2026-10-15');

        $response->assertStatus(200);
        $response->assertSee('GLT-9900');
        $response->assertSee('Gilgit');
        $response->assertSee('Hunza');
        $response->assertSee('Kareem Driver');
        $response->assertSee('10 Open Seats');
        $response->assertDontSee('No trips found');
    }

    public function test_trip_appears_when_searching_origin_to_destination(): void
    {
        $response = $this->get('/trips?from=Gilgit&to=Hunza&date=2026-10-15');

        $response->assertStatus(200);
        $response->assertSee('GLT-9900');
        $response->assertSee('10 Open Seats');
    }

    public function test_trip_appears_when_searching_origin_to_intermediate_stop(): void
    {
        $response = $this->get('/trips?from=Gilgit&to=Nagar&date=2026-10-15');

        $response->assertStatus(200);
        $response->assertSee('GLT-9900');
    }

    public function test_trip_does_not_appear_when_from_occurs_after_to_in_route_order(): void
    {
        // Reverse direction: Hunza (order 3) to Nagar (order 2) — must NOT match!
        $response = $this->get('/trips?from=Hunza&to=Nagar&date=2026-10-15');

        $response->assertStatus(200);
        $response->assertSee('No trips found');
        $response->assertDontSee('GLT-9900');
    }

    public function test_trip_does_not_appear_when_destination_occurs_before_start(): void
    {
        // Reverse direction: Hunza (order 3) to Gilgit (order 1) — must NOT match!
        $response = $this->get('/trips?from=Hunza&to=Gilgit&date=2026-10-15');

        $response->assertStatus(200);
        $response->assertSee('No trips found');
        $response->assertDontSee('GLT-9900');
    }

    public function test_trip_does_not_appear_when_stop_is_not_on_route(): void
    {
        // Skardu is not on this route
        $response = $this->get('/trips?from=Skardu&to=Hunza&date=2026-10-15');

        $response->assertStatus(200);
        $response->assertSee('No trips found');
        $response->assertDontSee('GLT-9900');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 3. DATE MATCHING
    // ─────────────────────────────────────────────────────────────────────────

    public function test_trip_date_must_match_selected_date(): void
    {
        // Searching for different date
        $response = $this->get('/trips?from=Nagar&to=Hunza&date=2026-10-20');

        $response->assertStatus(200);
        $response->assertSee('No trips found');
        $response->assertDontSee('GLT-9900');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 4. STATUS REQUIREMENT (Must be Scheduled)
    // ─────────────────────────────────────────────────────────────────────────

    public function test_completed_trips_do_not_appear_in_search(): void
    {
        $this->scheduledTrip->update(['status' => 'completed']);

        $response = $this->get('/trips?from=Nagar&to=Hunza&date=2026-10-15');
        $response->assertStatus(200);
        $response->assertSee('No trips found');
        $response->assertDontSee('GLT-9900');
    }

    public function test_cancelled_trips_do_not_appear_in_search(): void
    {
        $this->scheduledTrip->update(['status' => 'cancelled']);

        $response = $this->get('/trips?from=Nagar&to=Hunza&date=2026-10-15');
        $response->assertStatus(200);
        $response->assertSee('No trips found');
        $response->assertDontSee('GLT-9900');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 5. AVAILABLE SEATS REQUIREMENT (available_seats > 0)
    // ─────────────────────────────────────────────────────────────────────────

    public function test_trips_with_zero_available_seats_do_not_appear(): void
    {
        $this->scheduledTrip->update(['available_seats' => 0]);

        $response = $this->get('/trips?from=Nagar&to=Hunza&date=2026-10-15');
        $response->assertStatus(200);
        $response->assertSee('No trips found');
        $response->assertDontSee('GLT-9900');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 6. OPTIONAL PREFERRED TIME FILTER
    // ─────────────────────────────────────────────────────────────────────────

    public function test_optional_preferred_time_filter_morning_matches(): void
    {
        // Trip is at 08:00:00 (morning)
        $response = $this->get('/trips?from=Nagar&to=Hunza&date=2026-10-15&time=morning');
        $response->assertStatus(200);
        $response->assertSee('GLT-9900');
    }

    public function test_optional_preferred_time_filter_afternoon_filters_out_morning_trip(): void
    {
        // Trip is at 08:00:00, user requests afternoon (12:00 - 16:59)
        $response = $this->get('/trips?from=Nagar&to=Hunza&date=2026-10-15&time=afternoon');
        $response->assertStatus(200);
        $response->assertSee('No trips found');
        $response->assertDontSee('GLT-9900');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 7. DISPLAYED ATTRIBUTES
    // ─────────────────────────────────────────────────────────────────────────

    public function test_search_results_display_all_required_attributes(): void
    {
        $response = $this->get('/trips?from=Nagar&to=Hunza&date=2026-10-15');

        $response->assertStatus(200);

        // Route
        $response->assertSee('Gilgit');
        $response->assertSee('Hunza');

        // Departure date and time
        $response->assertSee('08:00 AM');
        $response->assertSee('15 Oct 2026');

        // Driver
        $response->assertSee('Kareem Driver');

        // Vehicle
        $response->assertSee('GLT-9900');
        $response->assertSee('Toyota HiAce GL');

        // Available seats
        $response->assertSee('10 Open Seats');

        // Expected stop times
        $response->assertSee('Expected Stop Timings');
        $response->assertSee('09:30 AM'); // Nagar expected arrival time
        $response->assertSee('11:00 AM'); // Hunza expected arrival time

        // View Details button
        $response->assertSee('View Details');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 8. PUBLIC TRIP DETAILS PAGE
    // ─────────────────────────────────────────────────────────────────────────

    public function test_public_can_view_trip_details_page(): void
    {
        $response = $this->get("/trips/{$this->scheduledTrip->id}");

        $response->assertStatus(200);
        $response->assertSee("Trip Details #{$this->scheduledTrip->id}");
        $response->assertSee('GLT-9900');
        $response->assertSee('Kareem Driver');
        $response->assertSee('Nagar');
        $response->assertSee('09:30 AM');
        $response->assertSee('11:00 AM');
        $response->assertSee('Book Seats');
    }
}
