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

class AdminCompleteWorkflowDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $driver;
    protected User $passenger;
    protected Vehicle $vehicle;
    protected Route $route;
    protected Stop $stop1;
    protected Stop $stop2;
    protected Trip $trip;
    protected Booking $booking;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Admin
        $this->admin = User::factory()->create([
            'name'      => 'Operations Director',
            'email'     => 'admin@routeconnect.test',
            'user_type' => 1,
        ]);

        // 2. Driver (approved)
        $this->driver = User::factory()->create([
            'name'          => 'Rashid Khan',
            'email'         => 'rashid@driver.test',
            'user_type'     => 2,
            'driver_status' => 'approved',
            'license_no'    => 'DL-9900-GLT',
        ]);

        // 3. Passenger
        $this->passenger = User::factory()->create([
            'name'      => 'Amina Passenger',
            'email'     => 'amina@passenger.test',
            'user_type' => 0,
        ]);

        // 4. Vehicle (approved)
        $this->vehicle = Vehicle::factory()->create([
            'user_id'         => $this->driver->id,
            'registration_no' => 'GLT-4433',
            'model'           => 'Toyota HiAce Grand Cabin',
            'type'            => 'Van',
            'total_seats'     => 14,
            'status'          => 'Active',
        ]);

        // 5. Route & Stops
        $this->stop1 = Stop::create(['name' => 'Gilgit Central Terminal', 'location' => 'Gilgit', 'status' => 'Active']);
        $this->stop2 = Stop::create(['name' => 'Aliabad Station', 'location' => 'Hunza', 'status' => 'Active']);

        $this->route = Route::create([
            'name'           => 'Karakoram Fast Service',
            'start_location' => 'Gilgit Central Terminal',
            'end_location'   => 'Aliabad Station',
            'start_stop_id'  => $this->stop1->id,
            'end_stop_id'    => $this->stop2->id,
            'status'         => 'Active',
        ]);

        RouteStop::create(['route_id' => $this->route->id, 'stop_id' => $this->stop1->id, 'stop_order' => 1]);
        RouteStop::create(['route_id' => $this->route->id, 'stop_id' => $this->stop2->id, 'stop_order' => 2]);

        // 6. Trip
        $this->trip = Trip::create([
            'user_id'         => $this->driver->id,
            'vehicle_id'      => $this->vehicle->id,
            'route_id'        => $this->route->id,
            'trip_date'       => '2026-11-20',
            'departure_time'  => '08:30:00',
            'available_seats' => 11,
            'fare'            => 1500.00,
            'pickup_point'    => 'Main Gilgit Terminal',
            'status'          => 'Scheduled',
        ]);

        // 7. Booking
        $this->booking = Booking::create([
            'user_id'           => $this->passenger->id,
            'trip_id'           => $this->trip->id,
            'from_stop_id'      => $this->stop1->id,
            'to_stop_id'        => $this->stop2->id,
            'seats'             => 3,
            'total_fare'        => 4500.00,
            'booking_reference' => 'RC-ADM-7788',
            'status'            => 'confirmed',
            'booking_status'    => 'confirmed',
        ]);
    }

    /**
     * Test 1: Admin dashboard displays real database-backed statistics.
     */
    public function test_admin_dashboard_displays_real_database_backed_statistics(): void
    {
        // Add pending driver and pending vehicle
        $pendingDriver = User::factory()->create([
            'name'          => 'Zeeshan Applicant',
            'user_type'     => 2,
            'driver_status' => 'pending',
            'license_no'    => 'DL-PENDING-1',
        ]);

        $pendingVehicle = Vehicle::factory()->create([
            'user_id'         => $this->driver->id,
            'registration_no' => 'HUN-9999',
            'model'           => 'Coaster Express',
            'type'            => 'Bus',
            'total_seats'     => 25,
            'status'          => 'pending',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('backend.admin.dashboard');

        // Check cards
        $response->assertSee('Welcome back, Operations Director');
        $response->assertSee('Total Users');
        $response->assertSee('Pending Applications');
        $response->assertSee('Approved Drivers');
        $response->assertSee('Total Vehicles');
        $response->assertSee('Total Routes');
        $response->assertSee('Total Trips');
        $response->assertSee('Total Bookings');
        $response->assertSee('Platform Revenue');

        // Check real numbers
        $response->assertSee('Rs. 4,500.00'); // totalRevenue
        $response->assertSee('RC-ADM-7788'); // recent booking
        $response->assertSee('Zeeshan Applicant'); // recent pending driver
        $response->assertSee('HUN-9999'); // recent pending vehicle
        $response->assertSee('Karakoram Fast Service'); // recent scheduled trip
    }

    /**
     * Test 2: Admin dashboard contains working sections with status indicators and anchors.
     */
    public function test_admin_dashboard_contains_all_working_workflow_sections(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('id="driver-apps"', false);
        $response->assertSee('id="vehicle-approvals"', false);
        $response->assertSee('id="scheduled-trips"', false);
        $response->assertSee('id="passenger-bookings"', false);

        // Quick jump shortcuts
        $response->assertSee('Operations Hub');
        $response->assertSee(route('admin.drivers.applications'));
        $response->assertSee(route('admin.vehicles.index'));
        $response->assertSee(route('admin.users.index'));
        $response->assertSee(route('admin.routes.index'));
        $response->assertSee(route('admin.stops.index'));
        $response->assertSee(route('admin.trips.index'));
        $response->assertSee(route('admin.bookings.index'));
    }

    /**
     * Test 3: Unauthorized non-admin users cannot access admin dashboard.
     */
    public function test_unauthorized_users_cannot_access_admin_dashboard(): void
    {
        // Guest
        $guestRes = $this->get(route('admin.dashboard'));
        $guestRes->assertRedirect('/login');

        // Passenger
        $passengerRes = $this->actingAs($this->passenger)->get(route('admin.dashboard'));
        $passengerRes->assertStatus(403);

        // Driver
        $driverRes = $this->actingAs($this->driver)->get(route('admin.dashboard'));
        $driverRes->assertStatus(403);
    }

    /**
     * Test 4: Admin can approve and reject driver applications server-side.
     */
    public function test_admin_can_approve_and_reject_driver_applications(): void
    {
        $applicant = User::factory()->create([
            'user_type'     => 2,
            'driver_status' => 'pending',
            'license_no'    => 'DL-TEST-55',
        ]);

        // 1. Approve
        $approveRes = $this->actingAs($this->admin)->post(route('admin.drivers.approve', $applicant->id));
        $approveRes->assertSessionHas('success');
        $applicant->refresh();
        $this->assertEquals('approved', $applicant->driver_status);

        // 2. Reject with reason
        $rejectRes = $this->actingAs($this->admin)->post(route('admin.drivers.reject', $applicant->id), [
            'rejection_reason' => 'Invalid driving license document.',
        ]);
        $rejectRes->assertSessionHas('success');
        $applicant->refresh();
        $this->assertEquals('rejected', $applicant->driver_status);
        $this->assertEquals('Invalid driving license document.', $applicant->rejection_reason);
    }

    /**
     * Test 5: Admin can approve and reject vehicles separately for every vehicle.
     */
    public function test_admin_can_approve_and_reject_vehicles_individually(): void
    {
        $vehicle = Vehicle::factory()->create([
            'user_id'         => $this->driver->id,
            'registration_no' => 'KHI-7766',
            'model'           => 'Toyota HiAce',
            'type'            => 'Van',
            'total_seats'     => 14,
            'status'          => 'pending',
        ]);

        // 1. Approve
        $approveRes = $this->actingAs($this->admin)->post(route('admin.vehicles.approve', $vehicle->id));
        $approveRes->assertSessionHas('success');
        $vehicle->refresh();
        $this->assertTrue($vehicle->isApproved());

        // 2. Reject with reason
        $rejectRes = $this->actingAs($this->admin)->post(route('admin.vehicles.reject', $vehicle->id), [
            'rejection_reason' => 'Fitness certificate expired.',
        ]);
        $rejectRes->assertSessionHas('success');
        $vehicle->refresh();
        $this->assertEquals('rejected', strtolower($vehicle->status));
        $this->assertEquals('Fitness certificate expired.', $vehicle->rejection_reason);
    }

    /**
     * Test 6: Admin can cancel passenger bookings with atomic seat restoration.
     */
    public function test_admin_can_cancel_booking_and_restore_seats_atomically(): void
    {
        $initialAvailable = $this->trip->available_seats; // 11
        $bookedSeats = $this->booking->seats; // 3

        $response = $this->actingAs($this->admin)->post(route('admin.bookings.cancel', $this->booking->id));

        $response->assertSessionHas('success');
        $this->booking->refresh();
        $this->assertEquals('cancelled', $this->booking->booking_status);
        $this->assertEquals('cancelled', $this->booking->status);

        // Trip seats restored from 11 to 14
        $this->trip->refresh();
        $this->assertEquals($initialAvailable + $bookedSeats, $this->trip->available_seats);

        // Repeat cancellation has no duplicate effect
        $repeatRes = $this->actingAs($this->admin)->post(route('admin.bookings.cancel', $this->booking->id));
        $repeatRes->assertSessionHas('error');
        $this->trip->refresh();
        $this->assertEquals($initialAvailable + $bookedSeats, $this->trip->available_seats);
    }

    /**
     * Test 7: Non-admin users cannot perform admin actions (forbidden).
     */
    public function test_non_admin_cannot_perform_admin_approval_or_cancellation_actions(): void
    {
        // Passenger cannot approve driver
        $passApprove = $this->actingAs($this->passenger)->post(route('admin.drivers.approve', $this->driver->id));
        $passApprove->assertStatus(403);

        // Driver cannot approve vehicle
        $driverVehApprove = $this->actingAs($this->driver)->post(route('admin.vehicles.approve', $this->vehicle->id));
        $driverVehApprove->assertStatus(403);

        // Passenger cannot cancel another passenger's booking via admin route
        $otherBooking = Booking::create([
            'user_id'           => $this->driver->id,
            'trip_id'           => $this->trip->id,
            'seats'             => 1,
            'total_fare'        => 1500.00,
            'booking_reference' => 'RC-OTHER-12',
            'status'            => 'confirmed',
            'booking_status'    => 'confirmed',
        ]);

        $cancelRes = $this->actingAs($this->passenger)->post(route('admin.bookings.cancel', $otherBooking->id));
        $cancelRes->assertStatus(403);
    }

    /**
     * Test 8: Admin can view bookings index with search and filters.
     */
    public function test_admin_bookings_index_supports_search_and_filtering(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.bookings.index', [
            'search' => 'RC-ADM-7788',
            'status' => 'confirmed',
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('backend.admin.bookings.index');
        $response->assertSee('RC-ADM-7788');
        $response->assertSee('Amina Passenger');
        $response->assertSee('Confirmed');
    }

    /**
     * Test 9: Admin can view trips index with status filters and trip stops.
     */
    public function test_admin_trips_index_and_stops_are_accessible_and_filterable(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.trips.index', [
            'status' => 'scheduled',
        ]));

        $response->assertStatus(200);
        $response->assertViewIs('backend.admin.trips.index');
        $response->assertSee('Karakoram Fast Service');
        $response->assertSee('Scheduled');

        // Trip stops management page
        $stopsRes = $this->actingAs($this->admin)->get(route('admin.trips.stops.index', $this->trip->id));
        $stopsRes->assertStatus(200);
        $stopsRes->assertViewIs('backend.admin.trips.stops');
    }
}
