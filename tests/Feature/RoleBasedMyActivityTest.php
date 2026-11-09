<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\DriverApplication;
use App\Models\Route;
use App\Models\Stop;
use App\Models\TravelRequest;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleBasedMyActivityTest extends TestCase
{
    use RefreshDatabase;

    protected User $passenger1;
    protected User $passenger2;
    protected User $driver1;
    protected User $driver2;
    protected Vehicle $vehicle1;
    protected Vehicle $vehicle2;
    protected Route $route;
    protected Trip $trip1;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Passengers
        $this->passenger1 = User::factory()->create([
            'name'      => 'Aleeza Passenger One',
            'email'     => 'p1@routeconnect.test',
            'user_type' => 0,
        ]);

        $this->passenger2 = User::factory()->create([
            'name'      => 'Other Passenger Two',
            'email'     => 'p2@routeconnect.test',
            'user_type' => 0,
        ]);

        // 2. Drivers
        $this->driver1 = User::factory()->create([
            'name'          => 'Ahmad Driver One',
            'email'         => 'd1@routeconnect.test',
            'user_type'     => 2,
            'driver_status' => 'approved',
        ]);

        $this->driver2 = User::factory()->create([
            'name'          => 'Bilal Driver Two',
            'email'         => 'd2@routeconnect.test',
            'user_type'     => 2,
            'driver_status' => 'approved',
        ]);

        // 3. Vehicles
        $this->vehicle1 = Vehicle::create([
            'user_id'                 => $this->driver1->id,
            'model'                   => 'Toyota HiAce GL',
            'type'                    => 'Van',
            'registration_no'         => 'RC-ACT-01',
            'total_seats'             => 14,
            'status'                  => 'approved',
            'rental_rate_per_day'     => 6000.00,
            'is_available_for_rental' => true,
        ]);

        $this->vehicle2 = Vehicle::create([
            'user_id'                 => $this->driver2->id,
            'model'                   => 'Honda Civic Ex',
            'type'                    => 'Car',
            'registration_no'         => 'RC-ACT-02',
            'total_seats'             => 4,
            'status'                  => 'approved',
            'rental_rate_per_day'     => 4500.00,
            'is_available_for_rental' => true,
        ]);

        // 4. Stops & Route
        $s1 = Stop::create(['name' => 'Islamabad Terminal', 'location' => 'Islamabad', 'status' => 'Active']);
        $s2 = Stop::create(['name' => 'Murree Mall Road', 'location' => 'Murree', 'status' => 'Active']);

        $this->route = Route::create([
            'name'           => 'Islamabad - Murree Line',
            'start_stop_id'  => $s1->id,
            'end_stop_id'    => $s2->id,
            'start_location' => 'Islamabad',
            'end_location'   => 'Murree',
            'distance_km'    => 65,
            'base_fare'      => 800,
            'status'         => 'Active',
        ]);

        // 5. Trip
        $this->trip1 = Trip::create([
            'user_id'         => $this->driver1->id,
            'route_id'        => $this->route->id,
            'vehicle_id'      => $this->vehicle1->id,
            'trip_date'       => Carbon::tomorrow()->format('Y-m-d'),
            'departure_time'  => '08:30 AM',
            'fare'            => 800,
            'available_seats' => 14,
            'status'          => 'scheduled',
        ]);
    }

    /** 1. Passenger sees all 7 required organized sections */
    public function test_passenger_activity_displays_all_seven_organized_sections(): void
    {
        $response = $this->actingAs($this->passenger1)->get('/my-activity');
        $response->assertOk();

        $content = $response->getContent();
        $this->assertStringContainsString('My Seat Bookings', $content);
        $this->assertStringContainsString('My Private Vehicle Bookings', $content);
        $this->assertStringContainsString('My Travel Requests', $content);
        $this->assertStringContainsString('Applications Received', $content);
        $this->assertStringContainsString('Confirmed Arrangements', $content);
        $this->assertStringContainsString('Completed Trips', $content);
        $this->assertStringContainsString('Cancelled & Expired', $content);
    }

    /** 2. Privacy & Isolation: Passenger only sees their own records */
    public function test_passenger_privacy_cannot_see_other_passengers_records(): void
    {
        // Passenger 1 booking
        $b1 = Booking::create([
            'user_id'           => $this->passenger1->id,
            'trip_id'           => $this->trip1->id,
            'booking_type'      => 'shared',
            'seats'             => 2,
            'total_fare'        => 1600,
            'booking_reference' => 'RC-P1-OWN',
            'booking_status'    => 'confirmed',
            'status'            => 'confirmed',
        ]);

        // Passenger 2 booking
        $b2 = Booking::create([
            'user_id'           => $this->passenger2->id,
            'trip_id'           => $this->trip1->id,
            'booking_type'      => 'shared',
            'seats'             => 1,
            'total_fare'        => 800,
            'booking_reference' => 'RC-P2-SECRET',
            'booking_status'    => 'confirmed',
            'status'            => 'confirmed',
        ]);

        // Passenger 2 travel request
        TravelRequest::create([
            'user_id'              => $this->passenger2->id,
            'pickup_location'      => 'Secret Location P2',
            'destination_location' => 'Secret Drop P2',
            'travel_date'          => Carbon::tomorrow()->format('Y-m-d'),
            'seats_needed'         => 2,
            'status'               => 'open',
        ]);

        // Passenger 1 views their seat bookings
        $response = $this->actingAs($this->passenger1)->get('/my-activity?tab=seat-bookings');
        $response->assertOk();
        $response->assertSee('RC-P1-OWN');
        $response->assertDontSee('RC-P2-SECRET');

        // Passenger 1 views their travel requests
        $reqResponse = $this->actingAs($this->passenger1)->get('/my-activity?tab=travel-requests');
        $reqResponse->assertOk();
        $reqResponse->assertDontSee('Secret Location P2');
    }

    /** 3. Passenger can edit open travel request */
    public function test_passenger_can_edit_open_travel_request(): void
    {
        $req = TravelRequest::create([
            'user_id'              => $this->passenger1->id,
            'pickup_location'      => 'Rawalpindi Saddar',
            'destination_location' => 'Murree Mall Road',
            'travel_date'          => Carbon::today()->addDays(2)->format('Y-m-d'),
            'travel_time'          => '09:00 AM',
            'seats_needed'         => 2,
            'budget'               => 2000,
            'status'               => 'open',
        ]);

        $updateData = [
            'pickup_location'        => 'Islamabad F-10',
            'destination_location'   => 'Bhurban Pearl Continental',
            'travel_date'            => Carbon::today()->addDays(3)->format('Y-m-d'),
            'travel_time'            => '11:00 AM',
            'seats_needed'           => 3,
            'preferred_vehicle_type' => 'Car',
            'budget'                 => 2500,
            'notes'                  => 'Updated travel plans',
        ];

        $response = $this->actingAs($this->passenger1)->put("/my-activity/requests/{$req->id}", $updateData);
        $response->assertRedirect('/my-activity?tab=travel-requests');

        $req->refresh();
        $this->assertEquals('Islamabad F-10', $req->pickup_location);
        $this->assertEquals('Bhurban Pearl Continental', $req->destination_location);
        $this->assertEquals(3, $req->seats_needed);
        $this->assertEquals(2500, $req->budget);
    }

    /** 4. Backend security: Passenger cannot edit another passenger's travel request */
    public function test_passenger_cannot_edit_other_passengers_travel_request(): void
    {
        $req = TravelRequest::create([
            'user_id'              => $this->passenger2->id,
            'pickup_location'      => 'Passenger 2 Pickup',
            'destination_location' => 'Passenger 2 Destination',
            'travel_date'          => Carbon::today()->addDays(2)->format('Y-m-d'),
            'seats_needed'         => 1,
            'status'               => 'open',
        ]);

        $response = $this->actingAs($this->passenger1)->put("/my-activity/requests/{$req->id}", [
            'pickup_location'      => 'Hacked Pickup',
            'destination_location' => 'Hacked Drop',
            'travel_date'          => Carbon::today()->addDays(2)->format('Y-m-d'),
            'seats_needed'         => 5,
        ]);

        $response->assertStatus(403);
        $req->refresh();
        $this->assertEquals('Passenger 2 Pickup', $req->pickup_location);
    }

    /** 5. Passenger can cancel their booking and seats are restored */
    public function test_passenger_can_cancel_booking(): void
    {
        $this->trip1->update(['available_seats' => 12]);

        $booking = Booking::create([
            'user_id'           => $this->passenger1->id,
            'trip_id'           => $this->trip1->id,
            'booking_type'      => 'shared',
            'seats'             => 2,
            'total_fare'        => 1600,
            'booking_reference' => 'RC-CANCEL-TEST',
            'booking_status'    => 'confirmed',
            'status'            => 'confirmed',
        ]);

        $response = $this->actingAs($this->passenger1)->post("/my-activity/bookings/{$booking->id}/cancel", [
            'reason' => 'Schedule changed',
        ]);

        $response->assertRedirect('/my-activity?tab=cancelled-expired');

        $booking->refresh();
        $this->assertEquals('cancelled', $booking->status);
        $this->assertEquals('cancelled', $booking->booking_status);

        // Seats restored on trip
        $this->trip1->refresh();
        $this->assertEquals(14, $this->trip1->available_seats);

        // Data integrity: Record is retained, not deleted
        $this->assertDatabaseHas('bookings', [
            'id'             => $booking->id,
            'booking_status' => 'cancelled',
        ]);
    }

    /** 6. Driver sees all 8 organized sections */
    public function test_driver_activity_displays_all_eight_organized_sections(): void
    {
        $response = $this->actingAs($this->driver1)->get('/my-activity');
        $response->assertOk();

        $content = $response->getContent();
        $this->assertStringContainsString('Shared Ride Listings', $content);
        $this->assertStringContainsString('My Vehicles', $content);
        $this->assertStringContainsString('My Bookings', $content);
        $this->assertStringContainsString('Passenger Requests', $content);
        $this->assertStringContainsString('My Applications', $content);
        $this->assertStringContainsString('Confirmed Arrangements', $content);
        $this->assertStringContainsString('Completed Trips', $content);
        $this->assertStringContainsString('Cancelled & Expired', $content);
    }

    /** 7. Driver My Bookings shows both shared and private bookings with filters */
    public function test_driver_my_bookings_displays_both_shared_and_private_bookings(): void
    {
        // 1. Shared booking on driver 1's trip
        $sharedBooking = Booking::create([
            'user_id'           => $this->passenger1->id,
            'trip_id'           => $this->trip1->id,
            'booking_type'      => 'shared',
            'seats'             => 2,
            'total_fare'        => 1600,
            'booking_reference' => 'RC-DRV-SHARED',
            'booking_status'    => 'confirmed',
            'status'            => 'confirmed',
        ]);

        // 2. Private vehicle rental on driver 1's vehicle
        $privateBooking = Booking::create([
            'user_id'              => $this->passenger2->id,
            'driver_id'            => $this->driver1->id,
            'vehicle_id'           => $this->vehicle1->id,
            'booking_type'         => 'private',
            'pickup_location'      => 'Islamabad',
            'destination_location' => 'Naran',
            'start_datetime'       => Carbon::tomorrow(),
            'end_datetime'         => Carbon::tomorrow()->addDay(),
            'days'                 => 1,
            'seats'                => 10,
            'total_fare'           => 6000,
            'booking_reference'    => 'RC-DRV-PRIVATE',
            'booking_status'       => 'confirmed',
            'status'               => 'confirmed',
        ]);

        // 3. Unrelated booking for driver 2
        $unrelatedBooking = Booking::create([
            'user_id'           => $this->passenger1->id,
            'driver_id'         => $this->driver2->id,
            'vehicle_id'        => $this->vehicle2->id,
            'booking_type'      => 'private',
            'seats'             => 2,
            'total_fare'        => 4500,
            'booking_reference' => 'RC-DRV2-SECRET',
            'booking_status'    => 'confirmed',
            'status'            => 'confirmed',
        ]);

        $response = $this->actingAs($this->driver1)->get('/my-activity?tab=my-bookings');
        $response->assertOk();
        $response->assertSee('RC-DRV-SHARED');
        $response->assertSee('RC-DRV-PRIVATE');
        $response->assertDontSee('RC-DRV2-SECRET');

        // Test filtering by private rentals
        $filterPrivateResponse = $this->actingAs($this->driver1)->get('/my-activity?tab=my-bookings&booking_type_filter=private');
        $filterPrivateResponse->assertOk();
        $filterPrivateResponse->assertSee('RC-DRV-PRIVATE');
        $filterPrivateResponse->assertDontSee('RC-DRV-SHARED');
    }

    /** 8. Driver can cancel booking on their vehicle/trip */
    public function test_driver_can_cancel_customer_booking(): void
    {
        $booking = Booking::create([
            'user_id'           => $this->passenger1->id,
            'trip_id'           => $this->trip1->id,
            'booking_type'      => 'shared',
            'seats'             => 1,
            'total_fare'        => 800,
            'booking_reference' => 'RC-DRV-CANCEL',
            'booking_status'    => 'confirmed',
            'status'            => 'confirmed',
        ]);

        $response = $this->actingAs($this->driver1)->post("/my-activity/bookings/{$booking->id}/cancel", [
            'reason' => 'Emergency vehicle maintenance',
        ]);

        $response->assertRedirect('/my-activity?tab=cancelled-expired');

        $booking->refresh();
        $this->assertEquals('cancelled', $booking->status);
    }

    /** 9. Data Integrity: Completed and cancelled records are preserved */
    public function test_historical_records_are_never_hard_deleted(): void
    {
        $completedBooking = Booking::create([
            'user_id'           => $this->passenger1->id,
            'trip_id'           => $this->trip1->id,
            'booking_type'      => 'shared',
            'seats'             => 1,
            'total_fare'        => 800,
            'booking_reference' => 'RC-HIST-COMPLETED',
            'booking_status'    => 'completed',
            'status'            => 'completed',
        ]);

        $cancelledBooking = Booking::create([
            'user_id'           => $this->passenger1->id,
            'trip_id'           => $this->trip1->id,
            'booking_type'      => 'shared',
            'seats'             => 1,
            'total_fare'        => 800,
            'booking_reference' => 'RC-HIST-CANCELLED',
            'booking_status'    => 'cancelled',
            'status'            => 'cancelled',
        ]);

        // Passenger views Completed Trips tab
        $compResp = $this->actingAs($this->passenger1)->get('/my-activity?tab=completed-trips');
        $compResp->assertOk();
        $compResp->assertSee('RC-HIST-COMPLETED');
        $compResp->assertDontSee('RC-HIST-CANCELLED');

        // Passenger views Cancelled & Expired tab
        $cancResp = $this->actingAs($this->passenger1)->get('/my-activity?tab=cancelled-expired');
        $cancResp->assertOk();
        $cancResp->assertSee('RC-HIST-CANCELLED');
        $cancResp->assertDontSee('RC-HIST-COMPLETED');

        // Records still exist in DB
        $this->assertDatabaseHas('bookings', ['id' => $completedBooking->id, 'booking_status' => 'completed']);
        $this->assertDatabaseHas('bookings', ['id' => $cancelledBooking->id, 'booking_status' => 'cancelled']);
    }
}

