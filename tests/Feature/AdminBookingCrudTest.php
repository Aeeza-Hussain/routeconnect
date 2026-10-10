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

class AdminBookingCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $driver;
    private User $passenger;
    private Vehicle $vehicle;
    private Route $route;
    private Stop $originStop;
    private Stop $destStop;
    private Trip $trip;
    private Booking $booking;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name'      => 'Admin Boss',
            'email'     => 'admin@boss.test',
            'user_type' => 1,
        ]);

        $this->driver = User::factory()->create([
            'name'          => 'Dan Driver',
            'email'         => 'dan@driver.test',
            'user_type'     => 2,
            'driver_status' => 'approved',
        ]);

        $this->passenger = User::factory()->create([
            'name'      => 'Amina Passenger',
            'email'     => 'amina@passenger.test',
            'phone'     => '0311-9988776',
            'user_type' => 0,
        ]);

        $this->vehicle = Vehicle::create([
            'user_id'         => $this->driver->id,
            'registration_no' => 'GLT-1122',
            'model'           => 'Toyota HiAce',
            'type'            => 'Passenger Van',
            'total_seats'     => 14,
            'status'          => 'Active',
        ]);

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

        $this->trip = Trip::create([
            'user_id'         => $this->driver->id,
            'vehicle_id'      => $this->vehicle->id,
            'route_id'        => $this->route->id,
            'trip_date'       => '2026-11-25',
            'departure_time'  => '08:30:00',
            'available_seats' => 10,
            'fare'            => 1500.00,
            'status'          => 'Scheduled',
        ]);

        $this->booking = Booking::create([
            'user_id'           => $this->passenger->id,
            'trip_id'           => $this->trip->id,
            'from_stop_id'      => $this->originStop->id,
            'to_stop_id'        => $this->destStop->id,
            'seats'             => 2,
            'total_fare'        => 3000.00,
            'booking_reference' => 'RC-TEST-9999',
            'status'            => 'confirmed',
            'booking_status'    => 'confirmed',
        ]);
    }

    public function test_guest_cannot_access_admin_bookings(): void
    {
        $response = $this->get('/admin/bookings');
        $response->assertRedirect('/login');
    }

    public function test_passenger_cannot_access_admin_bookings(): void
    {
        $response = $this->actingAs($this->passenger)->get('/admin/bookings');
        $response->assertStatus(403);
    }

    public function test_driver_cannot_access_admin_bookings(): void
    {
        $response = $this->actingAs($this->driver)->get('/admin/bookings');
        $response->assertStatus(403);
    }

    public function test_admin_can_view_bookings_index(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/bookings');
        $response->assertStatus(200);
        $response->assertSee('Bookings Management');
        $response->assertSee('RC-TEST-9999');
        $response->assertSee('Amina Passenger');
        $response->assertSee('Rs. 3,000');
    }

    public function test_admin_can_search_bookings_by_reference(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/bookings?search=RC-TEST-9999');
        $response->assertStatus(200);
        $response->assertSee('RC-TEST-9999');

        $emptyResponse = $this->actingAs($this->admin)->get('/admin/bookings?search=NONEXISTENT');
        $emptyResponse->assertStatus(200);
        $emptyResponse->assertDontSee('RC-TEST-9999');
        $emptyResponse->assertSee('No Bookings Found');
    }

    public function test_admin_can_view_booking_details(): void
    {
        $response = $this->actingAs($this->admin)->get("/admin/bookings/{$this->booking->id}");
        $response->assertStatus(200);
        $response->assertSee('RC-TEST-9999');
        $response->assertSee('Amina Passenger');
        $response->assertSee('Gilgit - Hunza Express');
        $response->assertSee('Dan Driver');
        $response->assertSee('GLT-1122');
        $response->assertSee('2 Seats');
    }

    public function test_admin_can_cancel_booking_and_restore_trip_seats(): void
    {
        $this->assertEquals(10, $this->trip->fresh()->available_seats);

        $response = $this->actingAs($this->admin)->post("/admin/bookings/{$this->booking->id}/cancel");
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->booking->refresh();
        $this->assertEquals('cancelled', $this->booking->booking_status);
        $this->assertEquals('cancelled', $this->booking->status);

        // Seats restored (10 + 2 = 12)
        $this->assertEquals(12, $this->trip->fresh()->available_seats);
    }
}
