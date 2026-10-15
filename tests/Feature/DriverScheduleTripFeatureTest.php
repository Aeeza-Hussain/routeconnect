<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Route;
use App\Models\Stop;
use App\Models\Trip;
use App\Models\TripStop;
use App\Models\Booking;
use App\Models\Notification;
use Carbon\Carbon;

class DriverScheduleTripFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $approvedDriver;
    protected Vehicle $approvedVehicle;
    protected Route $route;
    protected Stop $stopA;
    protected Stop $stopB;
    protected Stop $intermediateStop;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create Approved Driver
        $this->approvedDriver = User::factory()->create([
            'name'          => 'Kareem Driver',
            'email'         => 'kareem.driver@routeconnect.test',
            'user_type'     => 2,
            'driver_status' => 'approved',
        ]);

        // 2. Create Approved Vehicle
        $this->approvedVehicle = Vehicle::create([
            'user_id'         => $this->approvedDriver->id,
            'registration_no' => 'GLT-8899',
            'type'            => 'HiAce',
            'model'           => 'Toyota Grand Cabin',
            'total_seats'     => 14,
            'status'          => 'approved',
        ]);

        // 3. Create Origin, Destination and Intermediate Stops
        $this->stopA = Stop::create(['name' => 'Gilgit Terminal', 'location' => 'Gilgit', 'status' => 'Active']);
        $this->stopB = Stop::create(['name' => 'Hunza Center', 'location' => 'Hunza', 'status' => 'Active']);
        $this->intermediateStop = Stop::create(['name' => 'Aliabad Junction', 'location' => 'Aliabad', 'status' => 'Active']);

        // 4. Create Active Route
        $this->route = Route::create([
            'name'           => 'Gilgit to Hunza Express',
            'start_location' => 'Gilgit Terminal',
            'end_location'   => 'Hunza Center',
            'start_stop_id'  => $this->stopA->id,
            'end_stop_id'    => $this->stopB->id,
            'status'         => 'Active',
        ]);
    }

    /**
     * 1. Only approved drivers can access or schedule trips.
     */
    public function test_unapproved_driver_cannot_access_or_schedule_trip(): void
    {
        $pendingDriver = User::factory()->create(['user_type' => 2, 'driver_status' => 'pending']);
        $rejectedDriver = User::factory()->create(['user_type' => 2, 'driver_status' => 'rejected']);
        $passenger = User::factory()->create(['user_type' => 0]);

        // Pending/Rejected drivers are redirected by middleware to driver.pending page
        $this->actingAs($pendingDriver)->get(route('driver.trips.create'))->assertRedirect(route('driver.pending'));
        $this->actingAs($rejectedDriver)->get(route('driver.trips.create'))->assertRedirect(route('driver.pending'));

        // Non-driver (passenger) gets 403 forbidden
        $this->actingAs($passenger)->get(route('driver.trips.create'))->assertStatus(403);

        $date = Carbon::now()->addDays(2)->format('Y-m-d');
        $this->actingAs($pendingDriver)->post(route('driver.trips.store'), [
            'vehicle_id'      => $this->approvedVehicle->id,
            'route_id'        => $this->route->id,
            'trip_date'       => $date,
            'departure_time'  => '08:00',
            'available_seats' => 10,
            'fare'            => 900,
        ])->assertRedirect(route('driver.pending'));
    }

    /**
     * 2. Approved driver can schedule a trip with origin, destination, ordered intermediate stops and arrival timings.
     */
    public function test_approved_driver_can_schedule_valid_trip_with_all_attributes_and_intermediate_stops(): void
    {
        $date = Carbon::now()->addDays(3)->format('Y-m-d');

        $response = $this->actingAs($this->approvedDriver)->post(route('driver.trips.store'), [
            'vehicle_id'            => $this->approvedVehicle->id,
            'origin'                => 'Gilgit Terminal',
            'destination'           => 'Hunza Center',
            'trip_date'             => $date,
            'departure_time'        => '09:00',
            'expected_arrival_time' => '13:00',
            'available_seats'       => 12,
            'fare'                  => 850,
            'pickup_point'          => 'Main Bus Terminal, Bay 2',
            'notes'                 => 'Fast transit, AC enabled, baggage allowed.',
            'stops'                 => [
                [
                    'stop_id'       => $this->intermediateStop->id,
                    'name'          => 'Aliabad Junction',
                    'stop_order'    => 1,
                    'expected_time' => '11:15',
                ],
            ],
        ]);

        $trip = Trip::where('user_id', $this->approvedDriver->id)->first();
        $this->assertNotNull($trip);
        $this->assertEquals($this->approvedVehicle->id, $trip->vehicle_id);
        $this->assertEquals(12, $trip->available_seats);
        $this->assertEquals(850, $trip->fare);
        $this->assertEquals('scheduled', strtolower($trip->status));
        $this->assertEquals('13:00', substr($trip->expected_arrival_time, 0, 5));
        $this->assertEquals('Fast transit, AC enabled, baggage allowed.', $trip->notes);

        // Verify intermediate stop was created
        $this->assertDatabaseHas('trip_stops', [
            'trip_id'       => $trip->id,
            'stop_id'       => $this->intermediateStop->id,
            'stop_order'    => 1,
            'expected_time' => '11:15',
        ]);

        $response->assertRedirect(route('driver.trips.show', $trip->id));
    }

    /**
     * 3. Trip passenger capacity cannot exceed vehicle total capacity.
     */
    public function test_trip_passenger_capacity_cannot_exceed_vehicle_capacity(): void
    {
        $date = Carbon::now()->addDays(2)->format('Y-m-d');

        // Vehicle has 14 seats, driver requests 16
        $res = $this->actingAs($this->approvedDriver)->post(route('driver.trips.store'), [
            'vehicle_id'            => $this->approvedVehicle->id,
            'route_id'              => $this->route->id,
            'trip_date'             => $date,
            'departure_time'        => '09:00',
            'expected_arrival_time' => '12:00',
            'available_seats'       => 16,
            'fare'                  => 750,
        ]);

        $res->assertSessionHasErrors(['available_seats']);
        $this->assertDatabaseMissing('trips', ['user_id' => $this->approvedDriver->id]);
    }

    /**
     * 4. Driver cannot use unapproved vehicle or another driver's vehicle.
     */
    public function test_driver_cannot_use_unapproved_or_another_drivers_vehicle(): void
    {
        $date = Carbon::now()->addDays(2)->format('Y-m-d');

        // Unapproved vehicle owned by same driver
        $pendingVeh = Vehicle::create([
            'user_id'         => $this->approvedDriver->id,
            'registration_no' => 'PND-123',
            'type'            => 'Van',
            'model'           => 'Model X',
            'total_seats'     => 10,
            'status'          => 'pending',
        ]);

        $res1 = $this->actingAs($this->approvedDriver)->post(route('driver.trips.store'), [
            'vehicle_id'      => $pendingVeh->id,
            'route_id'        => $this->route->id,
            'trip_date'       => $date,
            'departure_time'  => '08:00',
            'available_seats' => 8,
            'fare'            => 500,
        ]);
        $res1->assertSessionHasErrors(['vehicle_id']);

        // Another driver's vehicle
        $driver2 = User::factory()->create(['user_type' => 2, 'driver_status' => 'approved']);
        $veh2 = Vehicle::create([
            'user_id'         => $driver2->id,
            'registration_no' => 'OTHER-456',
            'type'            => 'Bus',
            'model'           => 'Coaster',
            'total_seats'     => 22,
            'status'          => 'approved',
        ]);

        $res2 = $this->actingAs($this->approvedDriver)->post(route('driver.trips.store'), [
            'vehicle_id'      => $veh2->id,
            'route_id'        => $this->route->id,
            'trip_date'       => $date,
            'departure_time'  => '08:00',
            'available_seats' => 15,
            'fare'            => 500,
        ]);
        $res2->assertSessionHasErrors(['vehicle_id']);
    }

    /**
     * 5. One driver with multiple vehicles must not be assigned to overlapping trips.
     */
    public function test_driver_with_multiple_vehicles_cannot_have_overlapping_schedules(): void
    {
        $date = Carbon::now()->addDays(4)->format('Y-m-d');

        // Create second vehicle for the same driver
        $veh2 = Vehicle::create([
            'user_id'         => $this->approvedDriver->id,
            'registration_no' => 'GLT-9900',
            'type'            => 'Coaster',
            'model'           => 'Toyota Coaster',
            'total_seats'     => 25,
            'status'          => 'approved',
        ]);

        // First trip on Vehicle 1: 09:00 to 13:00
        Trip::create([
            'user_id'               => $this->approvedDriver->id,
            'vehicle_id'            => $this->approvedVehicle->id,
            'route_id'              => $this->route->id,
            'trip_date'             => $date,
            'departure_time'        => '09:00:00',
            'expected_arrival_time' => '13:00:00',
            'available_seats'       => 10,
            'fare'                  => 800,
            'status'                => 'scheduled',
        ]);

        // Attempt overlapping trip with Vehicle 2: 10:30 to 14:00 (Must be rejected)
        $overlapRes1 = $this->actingAs($this->approvedDriver)->post(route('driver.trips.store'), [
            'vehicle_id'            => $veh2->id,
            'route_id'              => $this->route->id,
            'trip_date'             => $date,
            'departure_time'        => '10:30',
            'expected_arrival_time' => '14:00',
            'available_seats'       => 20,
            'fare'                  => 1000,
        ]);
        $overlapRes1->assertSessionHasErrors(['departure_time']);

        // Attempt overlapping trip with Vehicle 2: 08:00 to 10:00 (Must be rejected)
        $overlapRes2 = $this->actingAs($this->approvedDriver)->post(route('driver.trips.store'), [
            'vehicle_id'            => $veh2->id,
            'route_id'              => $this->route->id,
            'trip_date'             => $date,
            'departure_time'        => '08:00',
            'expected_arrival_time' => '10:00',
            'available_seats'       => 20,
            'fare'                  => 1000,
        ]);
        $overlapRes2->assertSessionHasErrors(['departure_time']);

        // Non-overlapping trip on Vehicle 2: 13:30 to 17:00 (Must succeed)
        $validRes = $this->actingAs($this->approvedDriver)->post(route('driver.trips.store'), [
            'vehicle_id'            => $veh2->id,
            'route_id'              => $this->route->id,
            'trip_date'             => $date,
            'departure_time'        => '13:30',
            'expected_arrival_time' => '17:00',
            'available_seats'       => 20,
            'fare'                  => 1000,
        ]);
        $validRes->assertSessionHasNoErrors();
        $this->assertDatabaseHas('trips', [
            'user_id'        => $this->approvedDriver->id,
            'vehicle_id'     => $veh2->id,
            'departure_time' => '13:30',
        ]);
    }

    /**
     * 6. Schedule conflict detection properly handles trips that cross midnight.
     */
    public function test_schedule_conflict_detects_trips_crossing_midnight(): void
    {
        $baseDate = Carbon::now()->addDays(5)->format('Y-m-d');
        $nextDate = Carbon::now()->addDays(6)->format('Y-m-d');

        // Existing trip crosses midnight: Departs 22:00, arrives 02:00 next day
        Trip::create([
            'user_id'               => $this->approvedDriver->id,
            'vehicle_id'            => $this->approvedVehicle->id,
            'route_id'              => $this->route->id,
            'trip_date'             => $baseDate,
            'departure_time'        => '22:00:00',
            'expected_arrival_time' => '02:00:00', // crosses midnight to $nextDate 02:00
            'available_seats'       => 10,
            'fare'                  => 1200,
            'status'                => 'scheduled',
        ]);

        // Attempting to schedule on next day at 01:00 to 04:00 overlaps with the 22:00 - 02:00 window!
        $conflictRes = $this->actingAs($this->approvedDriver)->post(route('driver.trips.store'), [
            'vehicle_id'            => $this->approvedVehicle->id,
            'route_id'              => $this->route->id,
            'trip_date'             => $nextDate,
            'departure_time'        => '01:00',
            'expected_arrival_time' => '04:00',
            'available_seats'       => 10,
            'fare'                  => 1200,
        ]);
        $conflictRes->assertSessionHasErrors(['departure_time']);

        // Scheduling on next day at 02:30 to 06:00 is after arrival -> must succeed!
        $okRes = $this->actingAs($this->approvedDriver)->post(route('driver.trips.store'), [
            'vehicle_id'            => $this->approvedVehicle->id,
            'route_id'              => $this->route->id,
            'trip_date'             => $nextDate,
            'departure_time'        => '02:30',
            'expected_arrival_time' => '06:00',
            'available_seats'       => 10,
            'fare'                  => 1200,
        ]);
        $okRes->assertSessionHasNoErrors();
        $this->assertDatabaseHas('trips', [
            'user_id'        => $this->approvedDriver->id,
            'departure_time' => '02:30',
        ]);
    }

    /**
     * 7. Driver can view their trips on My Trips page and filter by status and query.
     */
    public function test_driver_can_view_trips_in_my_trips_page_with_filters(): void
    {
        $date = Carbon::now()->addDays(2)->format('Y-m-d');

        $trip1 = Trip::create([
            'user_id'        => $this->approvedDriver->id,
            'vehicle_id'     => $this->approvedVehicle->id,
            'route_id'       => $this->route->id,
            'trip_date'      => $date,
            'departure_time' => '08:00:00',
            'available_seats'=> 10,
            'fare'           => 800,
            'status'         => 'scheduled',
        ]);

        $trip2 = Trip::create([
            'user_id'        => $this->approvedDriver->id,
            'vehicle_id'     => $this->approvedVehicle->id,
            'route_id'       => $this->route->id,
            'trip_date'      => $date,
            'departure_time' => '14:00:00',
            'available_seats'=> 5,
            'fare'           => 800,
            'status'         => 'completed',
        ]);

        $indexRes = $this->actingAs($this->approvedDriver)->get(route('driver.trips'));
        $indexRes->assertOk();
        $indexRes->assertSee('Scheduled Trips');
        $indexRes->assertSee('GLT-8899');

        // Filter by scheduled
        $schedRes = $this->actingAs($this->approvedDriver)->get(route('driver.trips', ['status' => 'scheduled']));
        $schedRes->assertOk();
        $schedRes->assertSee('08:00:00');
        $schedRes->assertDontSee('14:00:00');
    }

    /**
     * 8. Driver can edit their trip operational details, validated against schedule conflicts and seat limits.
     */
    public function test_driver_can_edit_trip_and_conflict_is_validated_on_edit(): void
    {
        $date = Carbon::now()->addDays(3)->format('Y-m-d');

        $tripA = Trip::create([
            'user_id'               => $this->approvedDriver->id,
            'vehicle_id'            => $this->approvedVehicle->id,
            'route_id'              => $this->route->id,
            'trip_date'             => $date,
            'departure_time'        => '08:00:00',
            'expected_arrival_time' => '11:00:00',
            'available_seats'       => 10,
            'fare'                  => 800,
            'status'                => 'scheduled',
        ]);

        $tripB = Trip::create([
            'user_id'               => $this->approvedDriver->id,
            'vehicle_id'            => $this->approvedVehicle->id,
            'route_id'              => $this->route->id,
            'trip_date'             => $date,
            'departure_time'        => '14:00:00',
            'expected_arrival_time' => '17:00:00',
            'available_seats'       => 10,
            'fare'                  => 800,
            'status'                => 'scheduled',
        ]);

        // Edit Trip B departure to 09:00 -> conflicts with Trip A (08:00-11:00)
        $failEdit = $this->actingAs($this->approvedDriver)->put(route('driver.trips.update', $tripB->id), [
            'status'          => 'scheduled',
            'departure_time'  => '09:00',
            'available_seats' => 10,
            'fare'            => 900,
        ]);
        $failEdit->assertSessionHasErrors(['departure_time']);

        // Valid edit for Trip B
        $okEdit = $this->actingAs($this->approvedDriver)->put(route('driver.trips.update', $tripB->id), [
            'status'                => 'boarding',
            'departure_time'        => '15:00',
            'expected_arrival_time' => '18:00',
            'available_seats'       => 8,
            'fare'                  => 850,
            'pickup_point'          => 'Bay 5',
        ]);
        $okEdit->assertRedirect(route('driver.trips.show', $tripB->id));

        $this->assertEquals('boarding', $tripB->fresh()->status);
        $this->assertEquals(8, $tripB->fresh()->available_seats);
    }

    /**
     * 9. Driver cannot modify or cancel completed or cancelled trips.
     */
    public function test_driver_cannot_edit_or_cancel_completed_trip(): void
    {
        $date = Carbon::now()->addDays(2)->format('Y-m-d');

        $completedTrip = Trip::create([
            'user_id'        => $this->approvedDriver->id,
            'vehicle_id'     => $this->approvedVehicle->id,
            'route_id'       => $this->route->id,
            'trip_date'      => $date,
            'departure_time' => '08:00:00',
            'available_seats'=> 10,
            'fare'           => 800,
            'status'         => 'completed',
        ]);

        $editRes = $this->actingAs($this->approvedDriver)->put(route('driver.trips.update', $completedTrip->id), [
            'status'          => 'scheduled',
            'departure_time'  => '08:00',
            'available_seats' => 10,
        ]);
        $editRes->assertSessionHas('error');

        $cancelRes = $this->actingAs($this->approvedDriver)->post(route('driver.trips.cancel', $completedTrip->id));
        $cancelRes->assertSessionHas('error');
        $this->assertEquals('completed', $completedTrip->fresh()->status);
    }

    /**
     * 10. Cancelling a trip updates its status, cancels active passenger bookings, and notifies passengers.
     */
    public function test_driver_trip_cancellation_cancels_bookings_and_notifies_passengers(): void
    {
        $date = Carbon::now()->addDays(3)->format('Y-m-d');
        $passenger = User::factory()->create(['user_type' => 0, 'name' => 'Alice Passenger']);

        $trip = Trip::create([
            'user_id'        => $this->approvedDriver->id,
            'vehicle_id'     => $this->approvedVehicle->id,
            'route_id'       => $this->route->id,
            'trip_date'      => $date,
            'departure_time' => '09:00:00',
            'available_seats'=> 8,
            'fare'           => 800,
            'status'         => 'scheduled',
        ]);

        $booking = Booking::create([
            'user_id'           => $passenger->id,
            'trip_id'           => $trip->id,
            'from_stop_id'      => $this->stopA->id,
            'to_stop_id'        => $this->stopB->id,
            'seats'             => 2,
            'total_fare'        => 1600,
            'booking_reference' => 'BK-TEST-123',
            'status'            => 'confirmed',
            'booking_status'    => 'confirmed',
        ]);

        $res = $this->actingAs($this->approvedDriver)->post(route('driver.trips.cancel', $trip->id));
        $res->assertRedirect(route('driver.trips.show', $trip->id));

        $this->assertEquals('cancelled', $trip->fresh()->status);
        $this->assertEquals('cancelled', $booking->fresh()->status);
        $this->assertEquals('cancelled', $booking->fresh()->booking_status);

        // Verify passenger notification
        $this->assertDatabaseHas('notifications', [
            'user_id' => $passenger->id,
            'trip_id' => $trip->id,
        ]);
    }

    /**
     * 11. Only valid trips with approved vehicles and drivers are published for passenger search.
     */
    public function test_only_valid_trips_using_approved_vehicles_published_for_passenger_search(): void
    {
        $date = Carbon::now()->addDays(2)->format('Y-m-d');

        // Valid trip on approved vehicle
        $validTrip = Trip::create([
            'user_id'        => $this->approvedDriver->id,
            'vehicle_id'     => $this->approvedVehicle->id,
            'route_id'       => $this->route->id,
            'trip_date'      => $date,
            'departure_time' => '09:00:00',
            'available_seats'=> 10,
            'fare'           => 800,
            'status'         => 'scheduled',
        ]);

        // Trip on pending vehicle (simulated)
        $unapprovedVeh = Vehicle::create([
            'user_id'         => $this->approvedDriver->id,
            'registration_no' => 'PND-999',
            'type'            => 'Van',
            'model'           => 'Test',
            'total_seats'     => 10,
            'status'          => 'pending',
        ]);
        $unapprovedTrip = Trip::create([
            'user_id'        => $this->approvedDriver->id,
            'vehicle_id'     => $unapprovedVeh->id,
            'route_id'       => $this->route->id,
            'trip_date'      => $date,
            'departure_time' => '14:00:00',
            'available_seats'=> 10,
            'fare'           => 800,
            'status'         => 'scheduled',
        ]);

        $searchRes = $this->get(route('trips.index', ['date' => $date]));
        $searchRes->assertOk();
        $searchRes->assertSee('GLT-8899');
        $searchRes->assertDontSee('PND-999');
    }
}
