<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Route;
use App\Models\Stop;
use App\Models\RouteStop;
use App\Models\Trip;
use App\Models\Booking;
use Carbon\Carbon;

class DriverVehicleTripWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $passenger;
    protected Route $route;
    protected Stop $stopGilgit;
    protected Stop $stopDanyor;
    protected Stop $stopNagar;
    protected Stop $stopHunza;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Admin
        $this->admin = User::factory()->create([
            'name'          => 'Admin Super',
            'email'         => 'admin@routeconnect.com',
            'user_type'     => 1,
            'driver_status' => null,
        ]);

        // 2. Passenger
        $this->passenger = User::factory()->create([
            'name'          => 'Passenger Alice',
            'email'         => 'passenger@routeconnect.com',
            'user_type'     => 0,
            'driver_status' => null,
        ]);

        // 3. Stops
        $this->stopGilgit = Stop::create(['name' => 'Gilgit Terminal', 'location' => 'Gilgit', 'status' => 'Active']);
        $this->stopDanyor = Stop::create(['name' => 'Danyor Chowk', 'location' => 'Danyor', 'status' => 'Active']);
        $this->stopNagar  = Stop::create(['name' => 'Nagar Valley', 'location' => 'Nagar', 'status' => 'Active']);
        $this->stopHunza  = Stop::create(['name' => 'Hunza Aliabad', 'location' => 'Hunza', 'status' => 'Active']);

        // 4. Route: Gilgit -> Danyor -> Nagar -> Hunza
        $this->route = Route::create([
            'name'           => 'Gilgit to Hunza Express',
            'start_location' => 'Gilgit Terminal',
            'end_location'   => 'Hunza Aliabad',
            'start_stop_id'  => $this->stopGilgit->id,
            'end_stop_id'    => $this->stopHunza->id,
            'status'         => 'Active',
        ]);

        RouteStop::create(['route_id' => $this->route->id, 'stop_id' => $this->stopGilgit->id, 'stop_order' => 1]);
        RouteStop::create(['route_id' => $this->route->id, 'stop_id' => $this->stopDanyor->id, 'stop_order' => 2]);
        RouteStop::create(['route_id' => $this->route->id, 'stop_id' => $this->stopNagar->id,  'stop_order' => 3]);
        RouteStop::create(['route_id' => $this->route->id, 'stop_id' => $this->stopHunza->id,  'stop_order' => 4]);
    }

    /**
     * Steps 1 & 2: Driver registers with pending status, then Admin approves driver.
     */
    public function test_driver_registration_and_admin_approval_workflow(): void
    {
        // 1. Driver registration
        $regResponse = $this->post('/driver/register', [
            'name'                  => 'Driver Bilal',
            'email'                 => 'bilal@routeconnect.com',
            'phone'                 => '03001234567',
            'license_no'            => 'GLT-DL-9988',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
            'role'                  => 'driver',
        ]);

        $regResponse->assertRedirect(route('driver.pending'));
        $driver = User::where('email', 'bilal@routeconnect.com')->first();
        $this->assertNotNull($driver);
        $this->assertEquals(2, $driver->user_type);
        $this->assertEquals('pending', $driver->driver_status);

        // Pending driver cannot access approved driver workspace
        $pendingDashboard = $this->actingAs($driver)->get(route('driver.dashboard'));
        $pendingDashboard->assertRedirect(route('driver.pending'));

        // 2. Admin approves driver
        $approveResponse = $this->actingAs($this->admin)->post(route('admin.drivers.approve', $driver->id));
        $approveResponse->assertRedirect(route('admin.drivers.applications'));
        $this->assertEquals('approved', $driver->fresh()->driver_status);

        // Approved driver can now access driver workspace
        $approvedDashboard = $this->actingAs($driver->fresh())->get(route('driver.dashboard'));
        $approvedDashboard->assertStatus(200);
    }

    /**
     * Steps 3 & 4: Driver registers multiple vehicles; Admin approves one and rejects the other.
     */
    public function test_driver_multiple_vehicle_registration_and_admin_review(): void
    {
        $driver = User::factory()->create([
            'name'          => 'Driver Karim',
            'email'         => 'karim@routeconnect.com',
            'user_type'     => 2,
            'driver_status' => 'approved',
        ]);

        // Driver registers Vehicle 1
        $res1 = $this->actingAs($driver)->post(route('driver.vehicles.store'), [
            'registration_no' => 'GLT-VAN-101',
            'type'            => 'Van',
            'model'           => 'Toyota HiAce 2022',
            'total_seats'     => 14,
        ]);
        $res1->assertRedirect(route('driver.vehicles.index'));

        // Driver registers Vehicle 2
        $res2 = $this->actingAs($driver)->post(route('driver.vehicles.store'), [
            'registration_no' => 'GLT-BUS-202',
            'type'            => 'Bus',
            'model'           => 'Toyota Coaster 2023',
            'total_seats'     => 28,
        ]);
        $res2->assertRedirect(route('driver.vehicles.index'));

        $v1 = Vehicle::where('registration_no', 'GLT-VAN-101')->firstOrFail();
        $v2 = Vehicle::where('registration_no', 'GLT-BUS-202')->firstOrFail();

        // Both start as pending
        $this->assertEquals('pending', $v1->status);
        $this->assertEquals('pending', $v2->status);

        // Admin approves Vehicle 1
        $apprRes = $this->actingAs($this->admin)->post(route('admin.vehicles.approve', $v1->id));
        $apprRes->assertSessionHas('success');
        $this->assertTrue($v1->fresh()->isApproved());

        // Admin rejects Vehicle 2 with reason
        $rejRes = $this->actingAs($this->admin)->post(route('admin.vehicles.reject', $v2->id), [
            'rejection_reason' => 'Fitness certificate expired.',
        ]);
        $rejRes->assertSessionHas('success');
        $this->assertEquals('rejected', $v2->fresh()->status);
        $this->assertEquals('Fitness certificate expired.', $v2->fresh()->rejection_reason);

        // Driver can view both vehicles in My Vehicles page
        $vehIndex = $this->actingAs($driver)->get(route('driver.vehicles.index'));
        $vehIndex->assertStatus(200);
        $vehIndex->assertSee('GLT-VAN-101');
        $vehIndex->assertSee('GLT-BUS-202');
        $vehIndex->assertSee('Approved');
        $vehIndex->assertSee('Rejected');
    }

    /**
     * Steps 5 & 14: Driver schedules trip using approved vehicle; pending/rejected vehicles are rejected.
     */
    public function test_driver_schedules_trip_with_approved_vehicle_only(): void
    {
        $driver = User::factory()->create([
            'user_type'     => 2,
            'driver_status' => 'approved',
        ]);

        $approvedVehicle = Vehicle::create([
            'user_id'         => $driver->id,
            'registration_no' => 'GLT-APP-001',
            'type'            => 'Van',
            'model'           => 'HiAce 2022',
            'total_seats'     => 10,
            'status'          => 'Active',
        ]);

        $pendingVehicle = Vehicle::create([
            'user_id'         => $driver->id,
            'registration_no' => 'GLT-PND-002',
            'type'            => 'Van',
            'model'           => 'HiAce 2020',
            'total_seats'     => 10,
            'status'          => 'pending',
        ]);

        // Attempting to schedule with pending vehicle must fail
        $failRes = $this->actingAs($driver)->post(route('driver.trips.store'), [
            'vehicle_id'      => $pendingVehicle->id,
            'route_id'        => $this->route->id,
            'trip_date'       => Carbon::now()->addDays(2)->format('Y-m-d'),
            'departure_time'  => '09:00',
            'available_seats' => 10,
            'fare'            => 800,
        ]);
        $failRes->assertSessionHasErrors(['vehicle_id']);

        // Scheduling with approved vehicle succeeds
        $tripDate = Carbon::now()->addDays(2)->format('Y-m-d');
        $successRes = $this->actingAs($driver)->post(route('driver.trips.store'), [
            'vehicle_id'            => $approvedVehicle->id,
            'route_id'              => $this->route->id,
            'trip_date'             => $tripDate,
            'departure_time'        => '09:00',
            'expected_arrival_time' => '13:00',
            'available_seats'       => 10,
            'fare'                  => 800,
            'pickup_point'          => 'Gilgit Terminal',
            'notes'                 => 'AC High-speed van',
        ]);

        $trip = Trip::where('vehicle_id', $approvedVehicle->id)->first();
        $this->assertNotNull($trip);
        $this->assertEquals(10, $trip->available_seats);
        $this->assertEquals('scheduled', strtolower($trip->status));
        $successRes->assertRedirect(route('driver.trips.show', $trip->id));
    }

    /**
     * Steps 6, 7, 8, 9 & 12: Public search, intermediate-stop search, multi-seat booking, capacity updates, and exact cancellation restoration.
     */
    public function test_passenger_search_intermediate_booking_and_cancellation_flow(): void
    {
        $driver = User::factory()->create(['user_type' => 2, 'driver_status' => 'approved']);
        $vehicle = Vehicle::create([
            'user_id'         => $driver->id,
            'registration_no' => 'RC-TRIP-99',
            'type'            => 'Van',
            'model'           => 'HiAce Grand Cabin',
            'total_seats'     => 10,
            'status'          => 'Active',
        ]);

        $tripDate = Carbon::now()->addDays(3)->format('Y-m-d');

        $trip = Trip::create([
            'user_id'               => $driver->id,
            'vehicle_id'            => $vehicle->id,
            'route_id'              => $this->route->id,
            'trip_date'             => $tripDate,
            'departure_time'        => '09:00:00',
            'expected_arrival_time' => '13:00:00',
            'available_seats'       => 10,
            'fare'                  => 800,
            'pickup_point'          => 'Gilgit Terminal',
            'status'                => 'scheduled',
        ]);

        // Step 6 & 12: Trip appears in search for Origin -> Destination
        $searchRes1 = $this->get("/trips?from=Gilgit&to=Hunza&date={$tripDate}");
        $searchRes1->assertStatus(200);
        $searchRes1->assertSee('RC-TRIP-99');

        // Intermediate stop search: Nagar -> Hunza
        $searchRes2 = $this->get("/trips?from=Nagar&to=Hunza&date={$tripDate}");
        $searchRes2->assertStatus(200);
        $searchRes2->assertSee('RC-TRIP-99');

        // Step 7: Passenger Alice books 3 seats
        $bookRes1 = $this->actingAs($this->passenger)->post("/booking/{$trip->id}", [
            'seats' => 3,
        ]);
        $bookRes1->assertSessionHas('success');
        $this->assertEquals(7, $trip->fresh()->available_seats);

        $booking1 = Booking::where('trip_id', $trip->id)->firstOrFail();
        $this->assertEquals(3, $booking1->seats);
        $this->assertEquals('confirmed', $booking1->booking_status);

        // Step 8: Second passenger books 2 seats
        $passenger2 = User::factory()->create(['user_type' => 0]);
        $bookRes2 = $this->actingAs($passenger2)->post("/booking/{$trip->id}", [
            'seats' => 2,
        ]);
        $bookRes2->assertSessionHas('success');
        $this->assertEquals(5, $trip->fresh()->available_seats);

        // Cannot book more than remaining seats (5 available, trying to book 6)
        $overbookRes = $this->actingAs($passenger2)->post("/booking/{$trip->id}", [
            'seats' => 6,
        ]);
        $overbookRes->assertSessionHasErrors(['seats']);
        $this->assertEquals(5, $trip->fresh()->available_seats);

        // Step 9: Cancellation restores seats exactly once
        $cancelRes = $this->actingAs($this->passenger)->post("/my-bookings/{$booking1->id}/cancel");
        $cancelRes->assertSessionHas('success');
        $this->assertEquals(8, $trip->fresh()->available_seats); // 5 + 3 = 8
        $this->assertEquals('cancelled', $booking1->fresh()->booking_status);

        // Second cancellation attempt does NOT restore seats again (duplicate prevention)
        $dupCancelRes = $this->actingAs($this->passenger)->post("/my-bookings/{$booking1->id}/cancel");
        $this->assertEquals(8, $trip->fresh()->available_seats);
    }

    /**
     * Steps 10 & 11: Prevent overlapping trips for the same driver, even with different vehicles; allow non-overlapping trips.
     */
    public function test_driver_overlap_schedule_validation(): void
    {
        $driver = User::factory()->create(['user_type' => 2, 'driver_status' => 'approved']);
        $vehicle1 = Vehicle::create([
            'user_id'         => $driver->id,
            'registration_no' => 'OVL-01',
            'type'            => 'Van',
            'model'           => 'HiAce',
            'total_seats'     => 14,
            'status'          => 'Active',
        ]);
        $vehicle2 = Vehicle::create([
            'user_id'         => $driver->id,
            'registration_no' => 'OVL-02',
            'type'            => 'Bus',
            'model'           => 'Coaster',
            'total_seats'     => 28,
            'status'          => 'Active',
        ]);

        $tripDate = Carbon::now()->addDays(5)->format('Y-m-d');

        // Trip 1: 09:00 to 13:00 on $tripDate using Vehicle 1
        $t1 = Trip::create([
            'user_id'               => $driver->id,
            'vehicle_id'            => $vehicle1->id,
            'route_id'              => $this->route->id,
            'trip_date'             => $tripDate,
            'departure_time'        => '09:00:00',
            'expected_arrival_time' => '13:00:00',
            'available_seats'       => 14,
            'fare'                  => 800,
            'status'                => 'scheduled',
        ]);

        // Attempting to schedule overlapping trip: 10:00 to 14:00 using Vehicle 2 MUST BE REJECTED
        $overlapRes = $this->actingAs($driver)->post(route('driver.trips.store'), [
            'vehicle_id'            => $vehicle2->id,
            'route_id'              => $this->route->id,
            'trip_date'             => $tripDate,
            'departure_time'        => '10:00',
            'expected_arrival_time' => '14:00',
            'available_seats'       => 20,
            'fare'                  => 1000,
        ]);
        $overlapRes->assertSessionHasErrors(['departure_time']);

        // Attempting to schedule another overlap: 08:00 to 10:00 using Vehicle 2 MUST BE REJECTED
        $overlapRes2 = $this->actingAs($driver)->post(route('driver.trips.store'), [
            'vehicle_id'            => $vehicle2->id,
            'route_id'              => $this->route->id,
            'trip_date'             => $tripDate,
            'departure_time'        => '08:00',
            'expected_arrival_time' => '10:00',
            'available_seats'       => 20,
            'fare'                  => 1000,
        ]);
        $overlapRes2->assertSessionHasErrors(['departure_time']);

        // Step 11: Non-overlapping trip: 14:00 to 18:00 using Vehicle 2 MUST SUCCEED
        $validRes = $this->actingAs($driver)->post(route('driver.trips.store'), [
            'vehicle_id'            => $vehicle2->id,
            'route_id'              => $this->route->id,
            'trip_date'             => $tripDate,
            'departure_time'        => '14:00',
            'expected_arrival_time' => '18:00',
            'available_seats'       => 20,
            'fare'                  => 1000,
        ]);
        $validRes->assertSessionHasNoErrors();
        $this->assertDatabaseHas('trips', [
            'user_id'        => $driver->id,
            'vehicle_id'     => $vehicle2->id,
            'departure_time' => '14:00',
        ]);
    }

    /**
     * Step 13: Unauthorized access prevention.
     */
    public function test_role_authorization_and_cross_driver_isolation(): void
    {
        $driver1 = User::factory()->create(['user_type' => 2, 'driver_status' => 'approved']);
        $driver2 = User::factory()->create(['user_type' => 2, 'driver_status' => 'approved']);

        $veh1 = Vehicle::create([
            'user_id'         => $driver1->id,
            'registration_no' => 'D1-VEH-1',
            'type'            => 'Van',
            'model'           => 'HiAce',
            'total_seats'     => 14,
            'status'          => 'Active',
        ]);

        $trip1 = Trip::create([
            'user_id'         => $driver1->id,
            'vehicle_id'      => $veh1->id,
            'route_id'        => $this->route->id,
            'trip_date'       => Carbon::now()->addDays(2)->format('Y-m-d'),
            'departure_time'  => '10:00:00',
            'available_seats' => 14,
            'status'          => 'scheduled',
        ]);

        // Passenger cannot access driver dashboard
        $passengerAccess = $this->actingAs($this->passenger)->get(route('driver.dashboard'));
        $passengerAccess->assertStatus(403);

        // Driver cannot access admin dashboard
        $driverAdminAccess = $this->actingAs($driver1)->get(route('admin.dashboard'));
        $driverAdminAccess->assertStatus(403);

        // Driver 2 cannot edit Driver 1's trip
        $crossDriverAccess = $this->actingAs($driver2)->get(route('driver.trips.edit', $trip1->id));
        $crossDriverAccess->assertStatus(403);

        // Driver 2 cannot schedule a trip assigning Driver 1's vehicle
        $stealVehRes = $this->actingAs($driver2)->post(route('driver.trips.store'), [
            'vehicle_id'      => $veh1->id,
            'route_id'        => $this->route->id,
            'trip_date'       => Carbon::now()->addDays(2)->format('Y-m-d'),
            'departure_time'  => '15:00',
            'available_seats' => 10,
            'fare'            => 800,
        ]);
        $stealVehRes->assertSessionHasErrors(['vehicle_id']);
    }
}
