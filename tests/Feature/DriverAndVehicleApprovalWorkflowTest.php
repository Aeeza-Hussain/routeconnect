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
use Carbon\Carbon;

class DriverAndVehicleApprovalWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Route $route;
    protected Stop $startStop;
    protected Stop $endStop;

    protected function setUp(): void
    {
        parent::setUp();

        // Admin User
        $this->admin = User::factory()->create([
            'name'          => 'Admin Boss',
            'email'         => 'admin@routeconnect.com',
            'user_type'     => 1,
            'driver_status' => null,
        ]);

        // Route & Stops
        $this->startStop = Stop::create(['name' => 'Gilgit Terminal', 'location' => 'Gilgit', 'status' => 'Active']);
        $this->endStop   = Stop::create(['name' => 'Hunza Aliabad',   'location' => 'Hunza',  'status' => 'Active']);

        $this->route = Route::create([
            'name'           => 'Gilgit-Hunza Express',
            'start_location' => 'Gilgit Terminal',
            'end_location'   => 'Hunza Aliabad',
            'start_stop_id'  => $this->startStop->id,
            'end_stop_id'    => $this->endStop->id,
            'status'         => 'Active',
        ]);

        RouteStop::create(['route_id' => $this->route->id, 'stop_id' => $this->startStop->id, 'stop_order' => 1]);
        RouteStop::create(['route_id' => $this->route->id, 'stop_id' => $this->endStop->id,   'stop_order' => 2]);
    }

    /**
     * Requirement 1: Driver registers and their status is set to pending.
     */
    public function test_driver_registration_sets_status_to_pending_and_stores_license(): void
    {
        $response = $this->post('/driver/register', [
            'name'                  => 'Tariq Driver',
            'email'                 => 'tariq@routeconnect.com',
            'phone'                 => '03119876543',
            'cnic'                  => '71501-9988776-1',
            'license_no'            => 'DL-GB-8877',
            'dob'                   => '1992-05-15',
            'gender'                => 'male',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
            'role'                  => 'driver',
        ]);

        $response->assertRedirect(route('driver.pending'));

        $driver = User::where('email', 'tariq@routeconnect.com')->firstOrFail();
        $this->assertEquals(2, $driver->user_type);
        $this->assertEquals('pending', $driver->driver_status);
        $this->assertEquals('DL-GB-8877', $driver->license_no);
        $this->assertNull($driver->rejection_reason);
    }

    /**
     * Requirement 2: Admin can view driver details and approve or reject with optional reason.
     */
    public function test_admin_can_view_driver_details_and_approve_or_reject_with_optional_reason(): void
    {
        $driver = User::factory()->create([
            'name'          => 'Zubair Driver',
            'email'         => 'zubair@routeconnect.com',
            'user_type'     => 2,
            'driver_status' => 'pending',
            'license_no'    => 'DL-ZUB-101',
        ]);

        // 1. Admin views driver details
        $showRes = $this->actingAs($this->admin)->get(route('admin.drivers.show', $driver->id));
        $showRes->assertStatus(200);
        $showRes->assertSee('Zubair Driver');
        $showRes->assertSee('DL-ZUB-101');
        $showRes->assertSee('Pending Review');

        // 2. Admin rejects with an optional reason
        $rejectRes = $this->actingAs($this->admin)->post(route('admin.drivers.reject', $driver->id), [
            'rejection_reason' => 'License image blurry, re-upload needed.',
        ]);
        $rejectRes->assertRedirect(route('admin.drivers.applications'));
        $driver->refresh();
        $this->assertEquals('rejected', $driver->driver_status);
        $this->assertEquals('License image blurry, re-upload needed.', $driver->rejection_reason);

        // 3. Driver sees rejection reason on pending/status page
        $pendingPage = $this->actingAs($driver)->get(route('driver.pending'));
        $pendingPage->assertStatus(200);
        $pendingPage->assertSee('Application Not Approved');
        $pendingPage->assertSee('License image blurry, re-upload needed.');

        // 4. Admin re-approves driver, clearing rejection reason
        $approveRes = $this->actingAs($this->admin)->post(route('admin.drivers.approve', $driver->id));
        $approveRes->assertRedirect(route('admin.drivers.applications'));
        $driver->refresh();
        $this->assertEquals('approved', $driver->driver_status);
        $this->assertNull($driver->rejection_reason);
    }

    /**
     * Requirement 3: Only approved drivers can register vehicles and schedule trips.
     */
    public function test_unapproved_drivers_cannot_register_vehicles_or_schedule_trips(): void
    {
        $pendingDriver = User::factory()->create([
            'user_type'     => 2,
            'driver_status' => 'pending',
        ]);

        $rejectedDriver = User::factory()->create([
            'user_type'     => 2,
            'driver_status' => 'rejected',
        ]);

        // Pending driver attempt to view vehicle registration form
        $res1 = $this->actingAs($pendingDriver)->get(route('driver.vehicles.create'));
        $res1->assertRedirect(route('driver.pending'));

        // Pending driver attempt to store a vehicle
        $res2 = $this->actingAs($pendingDriver)->post(route('driver.vehicles.store'), [
            'registration_no' => 'PND-VEH-1',
            'type'            => 'Van',
            'model'           => 'HiAce',
            'total_seats'     => 14,
        ]);
        $res2->assertRedirect(route('driver.pending'));
        $this->assertDatabaseMissing('vehicles', ['registration_no' => 'PND-VEH-1']);

        // Rejected driver attempt to schedule trip
        $res3 = $this->actingAs($rejectedDriver)->get(route('driver.trips.create'));
        $res3->assertRedirect(route('driver.pending'));

        $res4 = $this->actingAs($rejectedDriver)->post(route('driver.trips.store'), [
            'vehicle_id'      => 1,
            'route_id'        => $this->route->id,
            'trip_date'       => Carbon::now()->addDays(2)->format('Y-m-d'),
            'departure_time'  => '10:00',
            'available_seats' => 10,
            'fare'            => 500,
        ]);
        $res4->assertRedirect(route('driver.pending'));
    }

    /**
     * Requirements 4, 5, 6: One approved driver can register multiple vehicles; each has its own status; admin approves each separately.
     */
    public function test_approved_driver_can_register_multiple_vehicles_each_with_independent_approval(): void
    {
        $driver = User::factory()->create([
            'user_type'     => 2,
            'driver_status' => 'approved',
        ]);

        // Register Vehicle 1 (Van)
        $res1 = $this->actingAs($driver)->post(route('driver.vehicles.store'), [
            'registration_no' => 'GB-VAN-11',
            'type'            => 'Van',
            'model'           => 'Toyota HiAce GL',
            'total_seats'     => 14,
        ]);
        $res1->assertRedirect(route('driver.vehicles.index'));

        // Register Vehicle 2 (Bus)
        $res2 = $this->actingAs($driver)->post(route('driver.vehicles.store'), [
            'registration_no' => 'GB-BUS-22',
            'type'            => 'Bus',
            'model'           => 'Toyota Coaster Saloon',
            'total_seats'     => 28,
        ]);
        $res2->assertRedirect(route('driver.vehicles.index'));

        // Register Vehicle 3 (Car)
        $res3 = $this->actingAs($driver)->post(route('driver.vehicles.store'), [
            'registration_no' => 'GB-CAR-33',
            'type'            => 'Car',
            'model'           => 'Honda BR-V',
            'total_seats'     => 6,
        ]);
        $res3->assertRedirect(route('driver.vehicles.index'));

        $v1 = Vehicle::where('registration_no', 'GB-VAN-11')->firstOrFail();
        $v2 = Vehicle::where('registration_no', 'GB-BUS-22')->firstOrFail();
        $v3 = Vehicle::where('registration_no', 'GB-CAR-33')->firstOrFail();

        // All 3 belong to the same driver and start as pending
        $this->assertEquals($driver->id, $v1->user_id);
        $this->assertEquals($driver->id, $v2->user_id);
        $this->assertEquals($driver->id, $v3->user_id);
        $this->assertEquals('pending', $v1->status);
        $this->assertEquals('pending', $v2->status);
        $this->assertEquals('pending', $v3->status);

        // Admin approves Vehicle 1
        $apprV1 = $this->actingAs($this->admin)->post(route('admin.vehicles.approve', $v1->id));
        $apprV1->assertSessionHas('success');
        $this->assertTrue($v1->fresh()->isApproved());

        // Admin rejects Vehicle 2 with optional rejection reason
        $rejV2 = $this->actingAs($this->admin)->post(route('admin.vehicles.reject', $v2->id), [
            'rejection_reason' => 'Vehicle inspection document expired.',
        ]);
        $rejV2->assertSessionHas('success');
        $this->assertEquals('rejected', $v2->fresh()->status);
        $this->assertEquals('Vehicle inspection document expired.', $v2->fresh()->rejection_reason);

        // Vehicle 3 remains pending
        $this->assertEquals('pending', $v3->fresh()->status);

        // Driver visits vehicle show page for Vehicle 2 and sees rejection reason
        $vehShow = $this->actingAs($driver)->get(route('driver.vehicles.show', $v2->id));
        $vehShow->assertStatus(200);
        $vehShow->assertSee('Vehicle inspection document expired.');
        $vehShow->assertSee('Rejected');
    }

    /**
     * Requirement 7: Only vehicles belonging to approved drivers and having approved status can be used for public trips.
     */
    public function test_only_approved_vehicles_of_approved_drivers_can_schedule_trips_and_appear_in_public_search(): void
    {
        $driver = User::factory()->create([
            'user_type'     => 2,
            'driver_status' => 'approved',
        ]);

        $approvedVehicle = Vehicle::create([
            'user_id'         => $driver->id,
            'registration_no' => 'GB-APPROVED-99',
            'type'            => 'Van',
            'model'           => 'HiAce Grand Cabin',
            'total_seats'     => 14,
            'status'          => 'Active',
        ]);

        $pendingVehicle = Vehicle::create([
            'user_id'         => $driver->id,
            'registration_no' => 'GB-PENDING-88',
            'type'            => 'Van',
            'model'           => 'HiAce Standard',
            'total_seats'     => 14,
            'status'          => 'pending',
        ]);

        $rejectedVehicle = Vehicle::create([
            'user_id'         => $driver->id,
            'registration_no' => 'GB-REJECTED-77',
            'type'            => 'Van',
            'model'           => 'HiAce Old',
            'total_seats'     => 14,
            'status'          => 'rejected',
        ]);

        $tripDate = Carbon::now()->addDays(4)->format('Y-m-d');

        // Cannot schedule with pending vehicle
        $failRes1 = $this->actingAs($driver)->post(route('driver.trips.store'), [
            'vehicle_id'      => $pendingVehicle->id,
            'route_id'        => $this->route->id,
            'trip_date'       => $tripDate,
            'departure_time'  => '08:00',
            'available_seats' => 10,
            'fare'            => 600,
        ]);
        $failRes1->assertSessionHasErrors(['vehicle_id']);

        // Cannot schedule with rejected vehicle
        $failRes2 = $this->actingAs($driver)->post(route('driver.trips.store'), [
            'vehicle_id'      => $rejectedVehicle->id,
            'route_id'        => $this->route->id,
            'trip_date'       => $tripDate,
            'departure_time'  => '08:00',
            'available_seats' => 10,
            'fare'            => 600,
        ]);
        $failRes2->assertSessionHasErrors(['vehicle_id']);

        // Scheduling with approved vehicle succeeds
        $successRes = $this->actingAs($driver)->post(route('driver.trips.store'), [
            'vehicle_id'            => $approvedVehicle->id,
            'route_id'              => $this->route->id,
            'trip_date'             => $tripDate,
            'departure_time'        => '08:00',
            'expected_arrival_time' => '12:00',
            'available_seats'       => 10,
            'fare'                  => 600,
        ]);
        $successRes->assertSessionHasNoErrors();

        // Appears in public trips search
        $publicSearch = $this->get("/trips?from=Gilgit&to=Hunza&date={$tripDate}");
        $publicSearch->assertStatus(200);
        $publicSearch->assertSee('GB-APPROVED-99');
    }

    /**
     * Requirement 8: Show clear approval statuses in driver dashboard and admin dashboard.
     */
    public function test_driver_dashboard_and_admin_dashboard_show_clear_approval_statuses(): void
    {
        $driver = User::factory()->create([
            'user_type'     => 2,
            'driver_status' => 'approved',
        ]);

        // 1 approved, 1 pending, 1 rejected
        Vehicle::create([
            'user_id'         => $driver->id,
            'registration_no' => 'STATUS-APP-1',
            'type'            => 'Van',
            'model'           => 'HiAce 2022',
            'total_seats'     => 14,
            'status'          => 'Active',
        ]);
        Vehicle::create([
            'user_id'         => $driver->id,
            'registration_no' => 'STATUS-PND-2',
            'type'            => 'Bus',
            'model'           => 'Coaster 2021',
            'total_seats'     => 28,
            'status'          => 'pending',
        ]);
        Vehicle::create([
            'user_id'          => $driver->id,
            'registration_no'  => 'STATUS-REJ-3',
            'type'             => 'Car',
            'model'            => 'Corolla',
            'total_seats'      => 4,
            'status'           => 'rejected',
            'rejection_reason' => 'Documentation missing',
        ]);

        // Driver Dashboard verifies counters and badges
        $driverDash = $this->actingAs($driver)->get(route('driver.dashboard'));
        $driverDash->assertStatus(200);
        $driverDash->assertSee('Driver Status: Approved');
        $driverDash->assertSee('Fleet Approval Status');
        $driverDash->assertSee('Pending');
        $driverDash->assertSee('Rejected');

        // Admin Dashboard verifies counters and vehicle approvals section
        $adminDash = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $adminDash->assertStatus(200);
        $adminDash->assertSee('Recent Pending Vehicle Approvals');
        $adminDash->assertSee('STATUS-PND-2');
    }

    /**
     * Requirement 10: Drivers cannot view or modify another driver's vehicles or applications.
     */
    public function test_drivers_cannot_view_or_modify_another_drivers_vehicles_or_applications(): void
    {
        $driverA = User::factory()->create(['name' => 'Driver A', 'user_type' => 2, 'driver_status' => 'approved']);
        $driverB = User::factory()->create(['name' => 'Driver B', 'user_type' => 2, 'driver_status' => 'approved']);

        $vehA = Vehicle::create([
            'user_id'         => $driverA->id,
            'registration_no' => 'DRA-VEH-1',
            'type'            => 'Van',
            'model'           => 'HiAce A',
            'total_seats'     => 14,
            'status'          => 'Active',
        ]);

        $vehB = Vehicle::create([
            'user_id'         => $driverB->id,
            'registration_no' => 'DRB-VEH-2',
            'type'            => 'Van',
            'model'           => 'HiAce B',
            'total_seats'     => 14,
            'status'          => 'Active',
        ]);

        // Driver A cannot see Driver B's vehicle in vehicle show page
        $res1 = $this->actingAs($driverA)->get(route('driver.vehicles.show', $vehB->id));
        $res1->assertStatus(404); // Scoped to auth driver, so 404

        // Driver A cannot see Driver B's vehicle in vehicles index list
        $res2 = $this->actingAs($driverA)->get(route('driver.vehicles.index'));
        $res2->assertStatus(200);
        $res2->assertSee('DRA-VEH-1');
        $res2->assertDontSee('DRB-VEH-2');

        // Driver A cannot schedule a trip using Driver B's vehicle
        $res3 = $this->actingAs($driverA)->post(route('driver.trips.store'), [
            'vehicle_id'      => $vehB->id,
            'route_id'        => $this->route->id,
            'trip_date'       => Carbon::now()->addDays(3)->format('Y-m-d'),
            'departure_time'  => '11:00',
            'available_seats' => 10,
            'fare'            => 500,
        ]);
        $res3->assertSessionHasErrors(['vehicle_id']);

        // Driver A cannot access admin driver application show page
        $res4 = $this->actingAs($driverA)->get(route('admin.drivers.show', $driverB->id));
        $res4->assertStatus(403);
    }
}
