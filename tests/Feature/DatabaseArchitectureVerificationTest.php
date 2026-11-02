<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\DriverApplication;
use App\Models\Message;
use App\Models\Route;
use App\Models\Stop;
use App\Models\TravelRequest;
use App\Models\Trip;
use App\Models\TripStop;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseArchitectureVerificationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $driver1;
    private User $driver2;
    private User $passenger;
    private Vehicle $vehicle1;
    private Vehicle $vehicle2;
    private Route $route;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'user_type' => 1,
            'name'      => 'Platform Admin',
            'email'     => 'admin@platform.com',
        ]);

        $this->driver1 = User::factory()->create([
            'user_type'     => 2,
            'driver_status' => 'approved',
            'name'          => 'Driver One',
            'email'         => 'driver1@platform.com',
        ]);

        $this->driver2 = User::factory()->create([
            'user_type'     => 2,
            'driver_status' => 'approved',
            'name'          => 'Driver Two',
            'email'         => 'driver2@platform.com',
        ]);

        $this->passenger = User::factory()->create([
            'user_type' => 0,
            'name'      => 'Passenger One',
            'email'     => 'passenger1@platform.com',
        ]);

        $this->vehicle1 = Vehicle::create([
            'user_id'                 => $this->driver1->id,
            'type'                    => 'Van',
            'registration_no'         => 'GIL-101',
            'model'                   => 'Toyota HiAce',
            'total_seats'             => 14,
            'rental_rate_per_day'     => 5000.00,
            'is_available_for_rental' => true,
            'status'                  => 'approved',
        ]);

        $this->vehicle2 = Vehicle::create([
            'user_id'                 => $this->driver2->id,
            'type'                    => 'Car',
            'registration_no'         => 'ISB-202',
            'model'                   => 'Toyota Corolla',
            'total_seats'             => 4,
            'rental_rate_per_day'     => 3500.00,
            'is_available_for_rental' => true,
            'status'                  => 'approved',
        ]);

        $this->route = Route::create([
            'name'           => 'Gilgit to Hunza Express',
            'start_location' => 'Gilgit Terminal',
            'end_location'   => 'Aliabad Hunza',
            'distance_km'    => 100.00,
        ]);
    }

    /**
     * Domain 1: Shared Ride Listings Architecture Verification
     */
    public function test_shared_ride_listings_architecture_and_relationships(): void
    {
        $trip = Trip::create([
            'user_id'               => $this->driver1->id,
            'vehicle_id'            => $this->vehicle1->id,
            'route_id'              => $this->route->id,
            'trip_date'             => Carbon::tomorrow()->toDateString(),
            'departure_time'        => '09:00:00',
            'expected_arrival_time' => '12:00:00',
            'total_seats'           => 14,
            'available_seats'       => 14,
            'fare'                  => 1500.00,
            'pickup_point'          => 'Gilgit Main Station',
            'status'                => 'scheduled',
        ]);

        $stop = Stop::create(['name' => 'Nagar Halt', 'location' => 'Nagar Valley']);
        $tripStop = TripStop::create([
            'trip_id'            => $trip->id,
            'stop_id'            => $stop->id,
            'stop_order'         => 1,
            'expected_time'      => '10:30:00',
            'fare_from_origin'   => 800.00,
        ]);

        $this->assertDatabaseHas('trips', [
            'id'              => $trip->id,
            'user_id'         => $this->driver1->id,
            'vehicle_id'      => $this->vehicle1->id,
            'route_id'        => $this->route->id,
            'total_seats'     => 14,
            'available_seats' => 14,
            'fare'            => 1500.00,
            'status'          => 'scheduled',
        ]);

        // Verify Eloquent relationships
        $this->assertEquals($this->driver1->id, $trip->driver->id);
        $this->assertEquals($this->vehicle1->id, $trip->vehicle->id);
        $this->assertEquals($this->route->id, $trip->route->id);
        $this->assertCount(1, $trip->tripStops);
        $this->assertEquals($stop->id, $trip->tripStops->first()->stop->id);
        $this->assertTrue($trip->isScheduled());
    }

    /**
     * Domain 2: Private Vehicle Bookings Architecture & Overlap Prevention
     */
    public function test_private_vehicle_bookings_architecture_and_overlap_prevention(): void
    {
        $start = Carbon::tomorrow()->setTime(9, 0);
        $end   = Carbon::tomorrow()->addDays(2)->setTime(18, 0);

        $booking = Booking::create([
            'user_id'              => $this->passenger->id,
            'driver_id'            => $this->driver1->id,
            'vehicle_id'           => $this->vehicle1->id,
            'trip_id'              => null,
            'booking_type'         => 'private',
            'pickup_location'      => 'Serena Hotel Gilgit',
            'destination_location' => 'Attabad Lake Resort',
            'start_datetime'       => $start,
            'end_datetime'         => $end,
            'days'                 => 2,
            'daily_rate'           => 5000.00,
            'total_fare'           => 10000.00,
            'seats'                => 14,
            'booking_reference'    => 'RC-PV-TEST-0001',
            'status'               => 'confirmed',
            'booking_status'       => 'confirmed',
        ]);

        $this->assertDatabaseHas('bookings', [
            'id'                => $booking->id,
            'user_id'           => $this->passenger->id,
            'driver_id'         => $this->driver1->id,
            'vehicle_id'        => $this->vehicle1->id,
            'booking_type'      => 'private',
            'total_fare'        => 10000.00,
            'booking_reference' => 'RC-PV-TEST-0001',
            'booking_status'    => 'confirmed',
        ]);

        // Verify relationships
        $this->assertEquals($this->passenger->id, $booking->passenger->id);
        $this->assertEquals($this->driver1->id, $booking->driver->id);
        $this->assertEquals($this->vehicle1->id, $booking->vehicle->id);
        $this->assertTrue($booking->isPrivate());

        // Verify driverBookings and passengerBookings relationships on User
        $this->assertTrue($this->driver1->driverBookings->contains('id', $booking->id));
        $this->assertTrue($this->passenger->passengerBookings->contains('id', $booking->id));

        // Overlapping reservation conflict prevention check
        $overlappingStart = Carbon::tomorrow()->addDay()->setTime(10, 0);
        $overlappingEnd   = Carbon::tomorrow()->addDays(3)->setTime(12, 0);

        $conflictReason = $this->vehicle1->getConflictReason($overlappingStart, $overlappingEnd);
        $this->assertNotNull($conflictReason);
        $this->assertStringContainsString('already has a confirmed private reservation', $conflictReason);

        // Non-overlapping range should be allowed
        $futureStart = Carbon::tomorrow()->addDays(10)->setTime(9, 0);
        $futureEnd   = Carbon::tomorrow()->addDays(12)->setTime(18, 0);
        $this->assertNull($this->vehicle1->getConflictReason($futureStart, $futureEnd));
    }

    /**
     * Domain 3: Passenger Travel Requests Architecture Verification
     */
    public function test_passenger_travel_requests_architecture_and_expiry(): void
    {
        $futureDate = Carbon::tomorrow()->addDays(3);
        $expiryTime = Carbon::tomorrow()->addDays(2)->setTime(23, 59, 59);

        $request = TravelRequest::create([
            'user_id'                => $this->passenger->id,
            'pickup_location'        => 'Gilgit Airport',
            'destination_location'   => 'Karimabad Hunza',
            'intermediate_stops'     => ['Chalt', 'Nagar'],
            'travel_date'            => $futureDate->toDateString(),
            'travel_time'            => '10:00 AM',
            'seats_needed'           => 3,
            'preferred_vehicle_type' => 'Van',
            'budget'                 => 6000.00,
            'notes'                  => 'Need luggage space for 3 suitcases',
            'status'                 => 'open',
            'expires_at'             => $expiryTime,
        ]);

        $this->assertDatabaseHas('travel_requests', [
            'id'                     => $request->id,
            'user_id'                => $this->passenger->id,
            'preferred_vehicle_type' => 'Van',
            'seats_needed'           => 3,
            'budget'                 => 6000.00,
            'status'                 => 'open',
        ]);

        // Verify JSON cast on intermediate_stops
        $fresh = TravelRequest::find($request->id);
        $this->assertEquals(['Chalt', 'Nagar'], $fresh->intermediate_stops);
        $this->assertFalse($fresh->isExpired());
        $this->assertTrue($fresh->isOpen());

        // Test scopeOpen filters out expired requests
        $expiredRequest = TravelRequest::create([
            'user_id'                => $this->passenger->id,
            'pickup_location'        => 'Gilgit',
            'destination_location'   => 'Skardu',
            'travel_date'            => Carbon::yesterday()->toDateString(),
            'seats_needed'           => 2,
            'status'                 => 'open',
            'expires_at'             => Carbon::yesterday(),
        ]);

        $openRequests = TravelRequest::open()->get();
        $this->assertTrue($openRequests->contains('id', $request->id));
        $this->assertFalse($openRequests->contains('id', $expiredRequest->id));
    }

    /**
     * Domain 4: Driver Applications & Single Driver Selection Constraint
     */
    public function test_driver_applications_and_single_driver_selection_constraint(): void
    {
        $travelRequest = TravelRequest::create([
            'user_id'              => $this->passenger->id,
            'pickup_location'      => 'Gilgit',
            'destination_location' => 'Hunza',
            'travel_date'          => Carbon::tomorrow()->toDateString(),
            'travel_time'          => '09:00 AM',
            'seats_needed'         => 2,
            'status'               => 'open',
        ]);

        // Driver 1 applies
        $app1 = DriverApplication::create([
            'travel_request_id' => $travelRequest->id,
            'driver_id'         => $this->driver1->id,
            'vehicle_id'        => $this->vehicle1->id,
            'available_seats'   => 14,
            'offered_fare'      => 4000.00,
            'proposed_time'     => '09:30 AM',
            'message'           => 'Available with HiAce Van',
            'status'            => 'pending',
        ]);

        // Driver 2 applies
        $app2 = DriverApplication::create([
            'travel_request_id' => $travelRequest->id,
            'driver_id'         => $this->driver2->id,
            'vehicle_id'        => $this->vehicle2->id,
            'available_seats'   => 4,
            'offered_fare'      => 3500.00,
            'proposed_time'     => '09:00 AM',
            'message'           => 'Available with Corolla Car',
            'status'            => 'pending',
        ]);

        $this->assertCount(2, $travelRequest->fresh()->applications);
        $this->assertEquals(14, $app1->available_seats);
        $this->assertEquals(4, $app2->available_seats);

        // Passenger selects Driver 2's application
        $travelRequest->selectApplication($app2);

        // Assert single driver selection constraint
        $travelRequest->refresh();
        $app1->refresh();
        $app2->refresh();

        $this->assertEquals($app2->id, $travelRequest->selected_application_id);
        $this->assertEquals('confirmed', $travelRequest->status);
        $this->assertTrue($app2->isAccepted());
        $this->assertTrue($app1->isRejected());
        $this->assertEquals($this->driver2->id, $travelRequest->selectedApplication->driver_id);
    }

    /**
     * Domain 5: Messaging Architecture & Relational Entity References
     */
    public function test_messaging_architecture_with_direct_entity_references(): void
    {
        $trip = Trip::create([
            'user_id'         => $this->driver1->id,
            'vehicle_id'      => $this->vehicle1->id,
            'route_id'        => $this->route->id,
            'trip_date'       => Carbon::tomorrow()->toDateString(),
            'departure_time'  => '08:00:00',
            'total_seats'     => 14,
            'available_seats' => 14,
            'fare'            => 1200.00,
            'status'          => 'scheduled',
        ]);

        $conversation = Conversation::create([
            'title'              => 'Trip Inquiry: Gilgit to Hunza',
            'subject_type'       => 'trip',
            'subject_id'         => $trip->id,
            'trip_id'            => $trip->id,
            'status'             => 'active',
            'arrangement_status' => 'pending',
            'created_by'         => $this->passenger->id,
        ]);

        // Add participants
        $p1 = ConversationParticipant::create([
            'conversation_id' => $conversation->id,
            'user_id'         => $this->passenger->id,
            'last_read_at'    => now(),
        ]);

        $p2 = ConversationParticipant::create([
            'conversation_id' => $conversation->id,
            'user_id'         => $this->driver1->id,
            'last_read_at'    => null,
        ]);

        // Send messages
        $msg1 = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $this->passenger->id,
            'body'            => 'Hi driver, do you have room for ski equipment?',
            'type'            => 'text',
            'is_read'         => false,
        ]);

        $this->assertDatabaseHas('conversations', [
            'id'      => $conversation->id,
            'trip_id' => $trip->id,
        ]);

        // Direct foreign key relationship
        $this->assertEquals($trip->id, $conversation->trip->id);
        $this->assertTrue($trip->conversations->contains('id', $conversation->id));

        // Read/unread status count check
        $this->assertEquals(0, $conversation->unreadCountFor($this->passenger));
        $this->assertEquals(1, $conversation->unreadCountFor($this->driver1));

        // Mark as read for driver
        $p2->update(['last_read_at' => now()->addMinute()]);
        $this->assertEquals(0, $conversation->fresh()->unreadCountFor($this->driver1));
    }
}

