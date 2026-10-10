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
use App\Models\Booking;
use App\Models\TripMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EndToEndUserJourneyTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_passenger_journey(): void
    {
        // Setup initial driver, vehicle, route, stops, and trip
        $driver = User::factory()->create([
            'name'          => 'Shahid Driver',
            'user_type'     => 2,
            'driver_status' => 'approved',
        ]);

        $vehicle = Vehicle::create([
            'user_id'         => $driver->id,
            'registration_no' => 'GLT-3322',
            'model'           => 'Toyota HiAce',
            'type'            => 'Passenger Van',
            'total_seats'     => 14,
            'status'          => 'Active',
        ]);

        $origin = Stop::create(['name' => 'Gilgit Terminal', 'location' => 'Gilgit', 'status' => 'Active']);
        $mid    = Stop::create(['name' => 'Nagar Stop',       'location' => 'Nagar',  'status' => 'Active']);
        $dest   = Stop::create(['name' => 'Hunza Terminal',  'location' => 'Hunza',  'status' => 'Active']);

        $route = Route::create([
            'name'           => 'Gilgit - Hunza Express',
            'start_location' => 'Gilgit Terminal',
            'end_location'   => 'Hunza Terminal',
            'status'         => 'Active',
            'start_stop_id'  => $origin->id,
            'end_stop_id'    => $dest->id,
        ]);

        RouteStop::create(['route_id' => $route->id, 'stop_id' => $origin->id, 'stop_order' => 1]);
        RouteStop::create(['route_id' => $route->id, 'stop_id' => $mid->id,    'stop_order' => 2]);
        RouteStop::create(['route_id' => $route->id, 'stop_id' => $dest->id,   'stop_order' => 3]);

        $trip = Trip::create([
            'user_id'         => $driver->id,
            'vehicle_id'      => $vehicle->id,
            'route_id'        => $route->id,
            'trip_date'       => now()->addDays(1)->format('Y-m-d'),
            'departure_time'  => '09:00:00',
            'available_seats' => 10,
            'fare'            => 1200.00,
            'status'          => 'Scheduled',
        ]);

        TripStop::create(['trip_id' => $trip->id, 'stop_id' => $origin->id, 'stop_order' => 1, 'expected_time' => '09:00:00']);
        TripStop::create(['trip_id' => $trip->id, 'stop_id' => $mid->id,    'stop_order' => 2, 'expected_time' => '10:30:00']);
        TripStop::create(['trip_id' => $trip->id, 'stop_id' => $dest->id,   'stop_order' => 3, 'expected_time' => '12:00:00']);

        // 1. Homepage loads
        $homeRes = $this->get('/');
        $homeRes->assertStatus(200);
        $homeRes->assertSee('RouteConnect');
        $homeRes->assertSee('Search Available Trips');

        // 2. Search Trips
        $searchRes = $this->get('/trips?from=Gilgit&to=Hunza');
        $searchRes->assertStatus(200);
        $searchRes->assertSee('Gilgit - Hunza Express');
        $searchRes->assertSee('10 Open Seats');

        // 3. View Stops and Timings
        $tripRes = $this->get("/trips/{$trip->id}");
        $tripRes->assertStatus(200);
        $tripRes->assertSee('Gilgit Terminal');
        $tripRes->assertSee('Nagar Stop');
        $tripRes->assertSee('Hunza Terminal');
        $tripRes->assertSee('Book Seats');

        // 4. Passenger registers & logs in
        $passenger = User::factory()->create([
            'name'      => 'Zara Passenger',
            'email'     => 'zara@journey.test',
            'user_type' => 0,
        ]);

        // 5. Access Booking Create Page
        $createRes = $this->actingAs($passenger)->get("/booking/{$trip->id}");
        $createRes->assertStatus(200);
        $createRes->assertSee('Reserve Your Seats');

        // 6. Submit Booking (2 seats)
        $storeRes = $this->actingAs($passenger)->post("/booking/{$trip->id}", [
            'seats'        => 2,
            'from_stop_id' => $origin->id,
            'to_stop_id'   => $dest->id,
        ]);
        $this->assertEquals(8, $trip->fresh()->available_seats);

        $booking = Booking::where('user_id', $passenger->id)->first();
        $this->assertNotNull($booking);
        $storeRes->assertRedirect(route('booking.confirmation', $booking->id));

        // 7. View Confirmation
        $confRes = $this->actingAs($passenger)->get(route('booking.confirmation', $booking->id));
        $confRes->assertStatus(200);
        $confRes->assertSee($booking->booking_reference);

        // 8. View My Bookings
        $myBookingsRes = $this->actingAs($passenger)->get('/my-bookings');
        $myBookingsRes->assertStatus(200);
        $myBookingsRes->assertSee($booking->booking_reference);

        // 9. View Single Booking Pass
        $showRes = $this->actingAs($passenger)->get("/my-bookings/{$booking->id}");
        $showRes->assertStatus(200);
        $showRes->assertSee($booking->booking_reference);

        // 10. Cancel Booking and verify seats return
        $cancelRes = $this->actingAs($passenger)->post("/my-bookings/{$booking->id}/cancel");
        $cancelRes->assertRedirect(route('passenger.bookings.index'));

        $this->assertEquals(10, $trip->fresh()->available_seats);
        $this->assertEquals('cancelled', $booking->fresh()->booking_status);
    }

    public function test_driver_registration_approval_and_publishing_journey(): void
    {
        $admin = User::factory()->create([
            'name'      => 'Admin Boss',
            'email'     => 'admin@gov.pk',
            'user_type' => 1,
        ]);

        // 1. Driver candidate registers
        $driverReg = $this->post('/driver/register', [
            'name'                  => 'Bilal Driver',
            'email'                 => 'bilal@driver.test',
            'phone'                 => '0345-1234567',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'license_no'            => 'DL-GILGIT-8877',
        ]);
        $driverReg->assertRedirect(route('driver.pending'));

        $driver = User::where('email', 'bilal@driver.test')->first();
        $this->assertNotNull($driver);
        $this->assertEquals('pending', $driver->driver_status);
        $this->assertEquals(2, $driver->user_type);

        // 2. Admin views application and approves
        $appRes = $this->actingAs($admin)->get('/admin/drivers/applications');
        $appRes->assertStatus(200);
        $appRes->assertSee('Bilal Driver');

        $approveRes = $this->actingAs($admin)->post("/admin/drivers/{$driver->id}/approve");
        $approveRes->assertRedirect();
        $this->assertEquals('approved', $driver->fresh()->driver_status);

        // 3. Driver now accesses Dashboard
        $dashRes = $this->actingAs($driver->fresh())->get('/driver/dashboard');
        $dashRes->assertStatus(200);
        $dashRes->assertSee('Driver Console');

        // Setup a vehicle and trip for driver
        $vehicle = Vehicle::create([
            'user_id'         => $driver->id,
            'registration_no' => 'GLT-9090',
            'model'           => 'Toyota HiAce',
            'type'            => 'Van',
            'total_seats'     => 12,
            'status'          => 'Active',
        ]);

        $route = Route::create([
            'name'           => 'Direct Karakoram',
            'start_location' => 'Gilgit',
            'end_location'   => 'Aliabad',
            'status'         => 'Active',
        ]);

        $trip = Trip::create([
            'user_id'         => $driver->id,
            'vehicle_id'      => $vehicle->id,
            'route_id'        => $route->id,
            'trip_date'       => now()->addDays(3)->format('Y-m-d'),
            'departure_time'  => '07:30:00',
            'available_seats' => 12,
            'status'          => 'scheduled',
        ]);

        // 4. Driver posts trip announcement message
        $msgRes = $this->actingAs($driver->fresh())->post('/driver/messages', [
            'trip_id' => $trip->id,
            'message' => 'Boarding starts at 07:15 AM at Central Platform 1.',
        ]);
        $msgRes->assertRedirect();

        $this->assertDatabaseHas('trip_messages', [
            'trip_id' => $trip->id,
            'user_id' => $driver->id,
            'message' => 'Boarding starts at 07:15 AM at Central Platform 1.',
        ]);
    }
}
