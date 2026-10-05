<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Route;
use App\Models\Stop;
use App\Models\RouteStop;
use App\Models\Trip;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MyBookingsTest extends TestCase
{
    use RefreshDatabase;

    private User $passenger1;
    private User $passenger2;
    private User $driver;
    private User $admin;
    private Vehicle $vehicle;
    private Route $route;
    private Stop $originStop;
    private Stop $destStop;
    private Trip $trip;
    private Booking $booking1;
    private Booking $booking2;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Users
        $this->passenger1 = User::factory()->create([
            'name'      => 'Amina Passenger',
            'email'     => 'amina@routeconnect.test',
            'user_type' => 0,
        ]);

        $this->passenger2 = User::factory()->create([
            'name'      => 'Bilal Passenger',
            'email'     => 'bilal@routeconnect.test',
            'user_type' => 0,
        ]);

        $this->driver = User::factory()->create([
            'name'          => 'Kareem Driver',
            'email'         => 'kareem@routeconnect.test',
            'user_type'     => 2,
            'driver_status' => 'approved',
        ]);

        $this->admin = User::factory()->create([
            'name'      => 'Admin Controller',
            'email'     => 'admin@routeconnect.test',
            'user_type' => 1,
        ]);

        // 2. Vehicle
        $this->vehicle = Vehicle::factory()->create([
            'user_id'         => $this->driver->id,
            'registration_no' => 'GLT-8899',
            'model'           => 'Toyota HiAce GL',
            'type'            => 'Van',
            'total_seats'     => 14,
            'status'          => 'Active',
        ]);

        // 3. Stops & Route
        $this->originStop = Stop::create(['name' => 'Gilgit Terminal', 'location' => 'Gilgit', 'status' => 'Active']);
        $this->destStop   = Stop::create(['name' => 'Hunza Station',   'location' => 'Hunza',  'status' => 'Active']);

        $this->route = Route::create([
            'name'           => 'Hunza Valley Express',
            'start_location' => 'Gilgit Terminal',
            'end_location'   => 'Hunza Station',
            'status'         => 'Active',
            'start_stop_id'  => $this->originStop->id,
            'end_stop_id'    => $this->destStop->id,
        ]);

        RouteStop::create(['route_id' => $this->route->id, 'stop_id' => $this->originStop->id, 'stop_order' => 1]);
        RouteStop::create(['route_id' => $this->route->id, 'stop_id' => $this->destStop->id,   'stop_order' => 2]);

        // 4. Trip: Initially 14 seats, 5 booked by passenger1, 3 booked by passenger2 -> 6 available
        $this->trip = Trip::create([
            'user_id'         => $this->driver->id,
            'vehicle_id'      => $this->vehicle->id,
            'route_id'        => $this->route->id,
            'trip_date'       => '2026-11-28',
            'departure_time'  => '08:45:00',
            'available_seats' => 6,
            'fare'            => 1500.00,
            'pickup_point'    => 'Central Gilgit Station',
            'status'          => 'Scheduled',
        ]);

        // 5. Booking for Passenger 1
        $this->booking1 = Booking::create([
            'user_id'           => $this->passenger1->id,
            'trip_id'           => $this->trip->id,
            'from_stop_id'      => $this->originStop->id,
            'to_stop_id'        => $this->destStop->id,
            'seats'             => 3,
            'total_fare'        => 4500.00,
            'booking_reference' => 'RC-AMN-1001',
            'status'            => 'confirmed',
            'booking_status'    => 'confirmed',
        ]);

        // 6. Booking for Passenger 2
        $this->booking2 = Booking::create([
            'user_id'           => $this->passenger2->id,
            'trip_id'           => $this->trip->id,
            'from_stop_id'      => $this->originStop->id,
            'to_stop_id'        => $this->destStop->id,
            'seats'             => 2,
            'total_fare'        => 3000.00,
            'booking_reference' => 'RC-BIL-2002',
            'status'            => 'confirmed',
            'booking_status'    => 'confirmed',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 1. UNAUTHORIZED ACCESS TESTS
    // ─────────────────────────────────────────────────────────────────────────

    public function test_guest_cannot_access_my_bookings(): void
    {
        $response = $this->get('/my-bookings');

        $response->assertRedirect('/login');
    }

    public function test_driver_cannot_access_my_bookings(): void
    {
        $response = $this->actingAs($this->driver)->get('/my-bookings');

        $response->assertStatus(403);
    }

    public function test_admin_cannot_access_my_bookings(): void
    {
        $response = $this->actingAs($this->admin)->get('/my-bookings');

        $response->assertStatus(403);
    }

    public function test_passenger_cannot_view_another_passengers_booking_details(): void
    {
        $response = $this->actingAs($this->passenger1)->get("/my-bookings/{$this->booking2->id}");

        $response->assertStatus(403);
    }

    public function test_passenger_cannot_cancel_another_passengers_booking(): void
    {
        $response = $this->actingAs($this->passenger1)->post("/my-bookings/{$this->booking2->id}/cancel");

        $response->assertStatus(403);

        // Booking 2 must remain confirmed
        $this->booking2->refresh();
        $this->assertEquals('confirmed', $this->booking2->booking_status);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 2. VIEW BOOKINGS TESTS
    // ─────────────────────────────────────────────────────────────────────────

    public function test_passenger_can_view_their_bookings(): void
    {
        $response = $this->actingAs($this->passenger1)->get('/my-bookings');

        $response->assertStatus(200);
        $response->assertViewIs('frontend.my_bookings');

        // Shows passenger1's booking attributes
        $response->assertSee('RC-AMN-1001');
        $response->assertSee('Hunza Valley Express');
        $response->assertSee('Kareem Driver');
        $response->assertSee('GLT-8899');
        $response->assertSee('28 Nov 2026');
        $response->assertSee('08:45 AM');
        $response->assertSee('3 Seats');
        $response->assertSee('Confirmed');
        $response->assertSee('View Details');
        $response->assertSee('Cancel');

        // Does NOT show passenger2's booking reference
        $response->assertDontSee('RC-BIL-2002');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 3. BOOKING DETAILS TESTS
    // ─────────────────────────────────────────────────────────────────────────

    public function test_passenger_can_view_booking_details(): void
    {
        $response = $this->actingAs($this->passenger1)->get("/my-bookings/{$this->booking1->id}");

        $response->assertStatus(200);
        $response->assertViewIs('frontend.booking_details');
        $response->assertSee('RC-AMN-1001');
        $response->assertSee('Hunza Valley Express');
        $response->assertSee('Gilgit Terminal');
        $response->assertSee('Hunza Station');
        $response->assertSee('Kareem Driver');
        $response->assertSee('GLT-8899');
        $response->assertSee('3 Seats');
        $response->assertSee('Rs. 4,500.00');
        $response->assertSee('Confirmed');
        $response->assertSee('Cancel This Booking');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 4. CANCELLATION & SEAT RESTORATION TESTS
    // ─────────────────────────────────────────────────────────────────────────

    public function test_passenger_can_cancel_their_booking(): void
    {
        $response = $this->actingAs($this->passenger1)->post("/my-bookings/{$this->booking1->id}/cancel");

        $response->assertRedirect('/my-bookings');
        $response->assertSessionHas('success');

        $this->booking1->refresh();
        $this->assertEquals('cancelled', $this->booking1->booking_status);
        $this->assertEquals('cancelled', $this->booking1->status);
    }

    public function test_cancelling_booking_restores_seats_to_trip(): void
    {
        $initialSeats = $this->trip->available_seats; // 6
        $bookedSeats  = $this->booking1->seats;       // 3

        $this->actingAs($this->passenger1)->post("/my-bookings/{$this->booking1->id}/cancel");

        $this->trip->refresh();
        $this->assertEquals($initialSeats + $bookedSeats, $this->trip->available_seats); // 6 + 3 = 9
    }

    public function test_seat_restoration_does_not_exceed_vehicle_total_capacity(): void
    {
        // Set trip available_seats near capacity (13 of 14)
        $this->trip->update(['available_seats' => 13]);

        // Booking has 3 seats, restoring would be 16, but total_seats is 14
        $this->actingAs($this->passenger1)->post("/my-bookings/{$this->booking1->id}/cancel");

        $this->trip->refresh();
        $this->assertEquals(14, $this->trip->available_seats); // Capped at total_seats (14)
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 5. ALREADY CANCELLED BOOKING TESTS
    // ─────────────────────────────────────────────────────────────────────────

    public function test_already_cancelled_booking_cannot_be_cancelled_again(): void
    {
        // Cancel the booking first time
        $this->actingAs($this->passenger1)->post("/my-bookings/{$this->booking1->id}/cancel");
        $this->trip->refresh();
        $seatsAfterFirstCancel = $this->trip->available_seats; // 9

        // Attempt to cancel a second time
        $response = $this->actingAs($this->passenger1)->post("/my-bookings/{$this->booking1->id}/cancel");

        $response->assertSessionHas('error', 'This booking has already been cancelled.');

        // Verify seats were NOT restored a second time
        $this->trip->refresh();
        $this->assertEquals($seatsAfterFirstCancel, $this->trip->available_seats);
    }
}
