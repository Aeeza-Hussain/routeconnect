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

class SeatBookingTest extends TestCase
{
    use RefreshDatabase;

    private User $passenger;
    private User $driver;
    private User $admin;
    private Vehicle $vehicle;
    private Route $route;
    private Stop $originStop;
    private Stop $destStop;
    private Trip $trip;
    private Trip $fullyBookedTrip;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Users
        $this->passenger = User::factory()->create([
            'name'      => 'Amina Passenger',
            'email'     => 'amina@passenger.test',
            'user_type' => 0,
        ]);

        $this->driver = User::factory()->create([
            'name'          => 'Rashid Driver',
            'email'         => 'rashid@driver.test',
            'user_type'     => 2,
            'driver_status' => 'approved',
        ]);

        $this->admin = User::factory()->create([
            'name'      => 'System Admin',
            'email'     => 'admin@routeconnect.test',
            'user_type' => 1,
        ]);

        // 2. Vehicle
        $this->vehicle = Vehicle::factory()->create([
            'user_id'         => $this->driver->id,
            'registration_no' => 'GLT-5544',
            'model'           => 'Toyota HiAce',
            'type'            => 'Passenger Van',
            'total_seats'     => 14,
            'status'          => 'Active',
        ]);

        // 3. Stops & Route
        $this->originStop = Stop::create(['name' => 'Gilgit Terminal', 'location' => 'Gilgit', 'status' => 'Active']);
        $this->destStop   = Stop::create(['name' => 'Hunza Station',   'location' => 'Hunza',  'status' => 'Active']);

        $this->route = Route::create([
            'name'           => 'Gilgit - Hunza Express',
            'start_location' => 'Gilgit Terminal',
            'end_location'   => 'Hunza Station',
            'status'         => 'Active',
            'start_stop_id'  => $this->originStop->id,
            'end_stop_id'    => $this->destStop->id,
        ]);

        RouteStop::create(['route_id' => $this->route->id, 'stop_id' => $this->originStop->id, 'stop_order' => 1]);
        RouteStop::create(['route_id' => $this->route->id, 'stop_id' => $this->destStop->id,   'stop_order' => 2]);

        // 4. Trip with 6 available seats
        $this->trip = Trip::create([
            'user_id'         => $this->driver->id,
            'vehicle_id'      => $this->vehicle->id,
            'route_id'        => $this->route->id,
            'trip_date'       => '2026-11-25',
            'departure_time'  => '08:30:00',
            'available_seats' => 6,
            'fare'            => 1200.00,
            'pickup_point'    => 'Central Gilgit Stand',
            'status'          => 'Scheduled',
        ]);

        // 5. Fully booked trip (0 available seats)
        $this->fullyBookedTrip = Trip::create([
            'user_id'         => $this->driver->id,
            'vehicle_id'      => $this->vehicle->id,
            'route_id'        => $this->route->id,
            'trip_date'       => '2026-11-26',
            'departure_time'  => '10:00:00',
            'available_seats' => 0,
            'fare'            => 1200.00,
            'pickup_point'    => 'Central Gilgit Stand',
            'status'          => 'Scheduled',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 1. AUTHENTICATION & AUTHORIZATION
    // ─────────────────────────────────────────────────────────────────────────

    public function test_unauthenticated_guest_cannot_access_booking_page(): void
    {
        $response = $this->get("/booking/{$this->trip->id}");

        $response->assertRedirect('/login');
    }

    public function test_unauthenticated_guest_cannot_post_booking(): void
    {
        $response = $this->post("/booking/{$this->trip->id}", [
            'seats' => 2,
        ]);

        $response->assertRedirect('/login');
    }

    public function test_driver_user_cannot_access_booking_page(): void
    {
        $response = $this->actingAs($this->driver)->get("/booking/{$this->trip->id}");

        $response->assertStatus(403);
    }

    public function test_driver_user_cannot_post_booking(): void
    {
        $response = $this->actingAs($this->driver)->post("/booking/{$this->trip->id}", [
            'seats' => 1,
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_user_cannot_access_booking_page(): void
    {
        $response = $this->actingAs($this->admin)->get("/booking/{$this->trip->id}");

        $response->assertStatus(403);
    }

    public function test_passenger_can_access_booking_page(): void
    {
        $response = $this->actingAs($this->passenger)->get("/booking/{$this->trip->id}");

        $response->assertStatus(200);
        $response->assertViewIs('frontend.booking_create');
        $response->assertSee('Reserve Your Seats');
        $response->assertSee('Gilgit - Hunza Express');
        $response->assertSee('Gilgit Terminal');
        $response->assertSee('Hunza Station');
        $response->assertSee('25 Nov 2026');
        $response->assertSee('08:30 AM');
        $response->assertSee('6 Seats Left');
        $response->assertSee('name="seats"', false);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 2. SUCCESSFUL BOOKING & DATABASE UPDATE
    // ─────────────────────────────────────────────────────────────────────────

    public function test_passenger_can_successfully_book_seats(): void
    {
        $initialSeats = $this->trip->available_seats; // 6
        $seatsToBook = 2;

        $response = $this->actingAs($this->passenger)->post("/booking/{$this->trip->id}", [
            'seats' => $seatsToBook,
        ]);

        // Assert redirect to confirmation
        $response->assertSessionHasNoErrors();
        $booking = Booking::where('trip_id', $this->trip->id)
            ->where('user_id', $this->passenger->id)
            ->first();

        $this->assertNotNull($booking);
        $response->assertRedirect(route('booking.confirmation', $booking->id));

        // Follow redirect to assert confirmation page content
        $confirmationResponse = $this->actingAs($this->passenger)->get(route('booking.confirmation', $booking->id));
        $confirmationResponse->assertStatus(200);
        $confirmationResponse->assertSee('Seats Booked Successfully!');
        $confirmationResponse->assertSee($booking->booking_reference);
        $confirmationResponse->assertSee('2 Seats Reserved');
        $confirmationResponse->assertSee('Rs. 2,400.00');
    }

    public function test_database_is_updated_accurately_after_booking(): void
    {
        $seatsToBook = 3;

        $this->actingAs($this->passenger)->post("/booking/{$this->trip->id}", [
            'seats' => $seatsToBook,
        ]);

        // 1. Verify trip available_seats was decremented from 6 to 3
        $this->trip->refresh();
        $this->assertEquals(3, $this->trip->available_seats);

        // 2. Verify booking record
        $booking = Booking::where('trip_id', $this->trip->id)->first();
        $this->assertNotNull($booking);
        $this->assertEquals($this->passenger->id, $booking->user_id);
        $this->assertEquals(3, $booking->seats);
        $this->assertEquals(3600.00, (float) $booking->total_fare);
        $this->assertNotEmpty($booking->booking_reference);
        $this->assertEquals('confirmed', $booking->status);
        $this->assertEquals('confirmed', $booking->booking_status);
    }

    public function test_unique_booking_reference_is_generated_for_each_booking(): void
    {
        $otherPassenger = User::factory()->create(['user_type' => 0]);

        $this->actingAs($this->passenger)->post("/booking/{$this->trip->id}", ['seats' => 1]);
        $this->actingAs($otherPassenger)->post("/booking/{$this->trip->id}", ['seats' => 1]);

        $bookings = Booking::where('trip_id', $this->trip->id)->get();
        $this->assertCount(2, $bookings);

        $ref1 = $bookings[0]->booking_reference;
        $ref2 = $bookings[1]->booking_reference;

        $this->assertNotEmpty($ref1);
        $this->assertNotEmpty($ref2);
        $this->assertNotEquals($ref1, $ref2);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 3. INSUFFICIENT SEATS & OVERBOOKING PREVENTION
    // ─────────────────────────────────────────────────────────────────────────

    public function test_cannot_book_more_seats_than_available(): void
    {
        // Trip has 6 available seats, attempt to book 7
        $response = $this->actingAs($this->passenger)->post("/booking/{$this->trip->id}", [
            'seats' => 7,
        ]);

        $response->assertSessionHasErrors(['seats']);

        // Assert available seats did not change
        $this->trip->refresh();
        $this->assertEquals(6, $this->trip->available_seats);

        // Assert no booking created
        $this->assertDatabaseMissing('bookings', [
            'trip_id' => $this->trip->id,
        ]);
    }

    public function test_seats_must_be_at_least_one(): void
    {
        // 0 seats
        $responseZero = $this->actingAs($this->passenger)->post("/booking/{$this->trip->id}", [
            'seats' => 0,
        ]);
        $responseZero->assertSessionHasErrors(['seats']);

        // Negative seats
        $responseNeg = $this->actingAs($this->passenger)->post("/booking/{$this->trip->id}", [
            'seats' => -2,
        ]);
        $responseNeg->assertSessionHasErrors(['seats']);

        $this->trip->refresh();
        $this->assertEquals(6, $this->trip->available_seats);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 4. FULLY BOOKED TRIP
    // ─────────────────────────────────────────────────────────────────────────

    public function test_fully_booked_trip_displays_fully_booked_message_and_disables_booking(): void
    {
        $response = $this->actingAs($this->passenger)->get("/booking/{$this->fullyBookedTrip->id}");

        $response->assertStatus(200);
        $response->assertSee('This Trip is Fully Booked');
        $response->assertSee('disabled', false);
    }

    public function test_cannot_book_seats_on_a_fully_booked_trip(): void
    {
        $response = $this->actingAs($this->passenger)->post("/booking/{$this->fullyBookedTrip->id}", [
            'seats' => 1,
        ]);

        $response->assertSessionHasErrors(['seats']);

        $this->fullyBookedTrip->refresh();
        $this->assertEquals(0, $this->fullyBookedTrip->available_seats);

        $this->assertDatabaseMissing('bookings', [
            'trip_id' => $this->fullyBookedTrip->id,
        ]);
    }
}
