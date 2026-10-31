<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Route;
use App\Models\Stop;
use App\Models\TravelRequest;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FindARideDynamicMarketplaceTest extends TestCase
{
    use RefreshDatabase;

    protected User $passenger;
    protected User $otherPassenger;
    protected User $driver;
    protected User $unapprovedDriver;
    protected Vehicle $vehicle;
    protected Route $route;
    protected Trip $trip;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Passenger
        $this->passenger = User::factory()->create([
            'name'      => 'Aleeza Fatima',
            'email'     => 'aleeza@example.com',
            'user_type' => 0,
        ]);

        // 2. Other Passenger
        $this->otherPassenger = User::factory()->create([
            'name'      => 'Other Passenger',
            'email'     => 'other@example.com',
            'user_type' => 0,
        ]);

        // 3. Approved Driver
        $this->driver = User::factory()->create([
            'name'          => 'Ahmad Khan',
            'email'         => 'ahmad@example.com',
            'user_type'     => 2,
            'driver_status' => 'approved',
        ]);

        // 4. Pending / Unapproved Driver
        $this->unapprovedDriver = User::factory()->create([
            'name'          => 'Pending Driver',
            'email'         => 'pending@example.com',
            'user_type'     => 2,
            'driver_status' => 'pending',
        ]);

        // 5. Approved Vehicle
        $this->vehicle = Vehicle::create([
            'user_id'                 => $this->driver->id,
            'model'                   => 'Toyota HiAce',
            'type'                    => 'Van',
            'registration_no'         => 'GLT-8899',
            'total_seats'             => 12,
            'status'                  => 'approved',
            'rental_rate_per_day'     => 5500.00,
            'is_available_for_rental' => true,
        ]);

        // 6. Route with Stops
        $stop1 = Stop::create(['name' => 'Gilgit Terminal', 'location' => 'Gilgit', 'status' => 'Active']);
        $stop2 = Stop::create(['name' => 'Aliabad Stop', 'location' => 'Hunza', 'status' => 'Active']);

        $this->route = Route::create([
            'name'           => 'Gilgit - Hunza Express',
            'start_stop_id'  => $stop1->id,
            'end_stop_id'    => $stop2->id,
            'start_location' => 'Gilgit Terminal',
            'end_location'   => 'Aliabad Stop',
            'distance_km'    => 100,
            'base_fare'      => 1500,
            'status'         => 'Active',
        ]);

        // 7. Scheduled Trip
        $this->trip = Trip::create([
            'user_id'         => $this->driver->id,
            'vehicle_id'      => $this->vehicle->id,
            'route_id'        => $this->route->id,
            'trip_date'       => Carbon::today()->addDays(2)->format('Y-m-d'),
            'departure_time'  => '09:00:00',
            'available_seats' => 8,
            'fare'            => 1500.00,
            'status'          => 'scheduled',
        ]);
    }

    /** 1. Marketplace page loads with real DB records and dark theme attributes */
    public function test_marketplace_page_loads_with_real_database_records(): void
    {
        $response = $this->get('/trips?tab=all');

        $response->assertStatus(200);
        $response->assertSee('Toyota HiAce (Van)');
        $response->assertSee('Ahmad Khan');
        $response->assertSee('1,500');
        $response->assertSee('8 Available');
    }

    /** 2. Passenger Privacy: Passenger only sees their own travel requests */
    public function test_passenger_privacy_only_sees_own_travel_requests(): void
    {
        // Passenger A creates a private travel request
        $reqA = TravelRequest::create([
            'user_id'              => $this->passenger->id,
            'pickup_location'      => 'Aleeza Home Secret',
            'destination_location' => 'Hunza Private Villa',
            'travel_date'          => Carbon::today()->addDays(3)->format('Y-m-d'),
            'travel_time'          => '09:00 AM',
            'seats_needed'         => 2,
            'budget'               => 3000,
            'status'               => 'open',
        ]);

        // Passenger B creates a private travel request
        $reqB = TravelRequest::create([
            'user_id'              => $this->otherPassenger->id,
            'pickup_location'      => 'Other Passenger Private Area',
            'destination_location' => 'Skardu Secret Resort',
            'travel_date'          => Carbon::today()->addDays(4)->format('Y-m-d'),
            'travel_time'          => '10:00 AM',
            'seats_needed'         => 3,
            'budget'               => 5000,
            'status'               => 'open',
        ]);

        // When Passenger A visits, they see Req A but NEVER Req B
        $responseA = $this->actingAs($this->passenger)->get('/trips?tab=request');
        $responseA->assertStatus(200);
        $responseA->assertSee('Aleeza Home Secret');
        $responseA->assertDontSee('Other Passenger Private Area');
        $responseA->assertSee('Private to You');

        // When Passenger B visits, they see Req B but NEVER Req A
        $responseB = $this->actingAs($this->otherPassenger)->get('/trips?tab=request');
        $responseB->assertStatus(200);
        $responseB->assertSee('Other Passenger Private Area');
        $responseB->assertDontSee('Aleeza Home Secret');
    }

    /** 3. Approved Drivers can view open travel requests and apply */
    public function test_approved_driver_can_view_open_requests_and_apply(): void
    {
        $travelRequest = TravelRequest::create([
            'user_id'              => $this->passenger->id,
            'pickup_location'      => 'Gilgit Airport',
            'destination_location' => 'Attabad Lake',
            'travel_date'          => Carbon::today()->addDays(5)->format('Y-m-d'),
            'travel_time'          => '11:00 AM',
            'seats_needed'         => 4,
            'budget'               => 6000,
            'status'               => 'open',
        ]);

        // Approved driver can see the request
        $response = $this->actingAs($this->driver)->get('/trips?tab=request');
        $response->assertStatus(200);
        $response->assertSee('Gilgit Airport');
        $response->assertSee('Attabad Lake');
        $response->assertSee('Driver Quoting');

        // Driver applies to request
        $applyResponse = $this->actingAs($this->driver)->post("/my-activity/requests/{$travelRequest->id}/apply", [
            'offered_fare' => 5800,
            'vehicle_id'   => $this->vehicle->id,
            'message'      => 'I can take you in my HiAce van.',
        ]);

        $applyResponse->assertRedirect();
        $this->assertDatabaseHas('driver_applications', [
            'travel_request_id' => $travelRequest->id,
            'driver_id'         => $this->driver->id,
            'offered_fare'      => 5800,
        ]);
    }

    /** 4. Seat Booking: Transaction decrements available seats atomically */
    public function test_seat_booking_decrements_available_seats(): void
    {
        $response = $this->actingAs($this->passenger)->post("/booking/{$this->trip->id}", [
            'seats' => 2,
        ]);

        $response->assertRedirect();
        $this->trip->refresh();
        $this->assertEquals(6, $this->trip->available_seats);

        $this->assertDatabaseHas('bookings', [
            'user_id'        => $this->passenger->id,
            'trip_id'        => $this->trip->id,
            'seats'          => 2,
            'booking_status' => 'confirmed',
        ]);
    }

    /** 5. Overbooking prevention */
    public function test_overbooking_is_prevented(): void
    {
        $response = $this->actingAs($this->passenger)->post("/booking/{$this->trip->id}", [
            'seats' => 9, // only 8 available
        ]);

        $response->assertSessionHasErrors(['seats']);
        $this->trip->refresh();
        $this->assertEquals(8, $this->trip->available_seats);
    }

    /** 6. Private Vehicle Reservation checks availability and saves reservation */
    public function test_private_vehicle_booking_saves_reservation(): void
    {
        $pickupDate = Carbon::today()->addDays(5)->format('Y-m-d');
        $returnDate = Carbon::today()->addDays(7)->format('Y-m-d');

        $response = $this->actingAs($this->passenger)->post("/book-vehicle/{$this->vehicle->id}/reserve", [
            'pickup_location'      => 'Islamabad Blue Area',
            'destination_location' => 'Gilgit Serena Hotel',
            'pickup_date'          => $pickupDate,
            'pickup_time'          => '09:00',
            'return_date'          => $returnDate,
            'return_time'          => '18:00',
            'passenger_notes'      => 'Private family tour.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('bookings', [
            'user_id'        => $this->passenger->id,
            'vehicle_id'     => $this->vehicle->id,
            'booking_type'   => 'private',
            'booking_status' => 'confirmed',
        ]);
    }
}
