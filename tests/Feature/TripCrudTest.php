<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Route as RouteModel;
use App\Models\Trip;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TripCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $passenger;
    private User $pendingDriver;
    private User $approvedDriver;
    private User $approvedDriver2;
    private Vehicle $vehicle1;
    private Vehicle $vehicle2;
    private RouteModel $activeRoute;
    private RouteModel $inactiveRoute;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name'          => 'Admin User',
            'email'         => 'admin@test.com',
            'user_type'     => 1,
            'driver_status' => null,
        ]);

        $this->passenger = User::factory()->create([
            'name'          => 'Passenger Alice',
            'email'         => 'passenger@test.com',
            'user_type'     => 0,
            'driver_status' => null,
        ]);

        $this->pendingDriver = User::factory()->create([
            'name'          => 'Pending Driver Bob',
            'email'         => 'pending@test.com',
            'user_type'     => 2,
            'driver_status' => 'pending',
        ]);

        $this->approvedDriver = User::factory()->create([
            'name'          => 'Approved Driver Charlie',
            'email'         => 'charlie@test.com',
            'user_type'     => 2,
            'driver_status' => 'approved',
        ]);

        $this->approvedDriver2 = User::factory()->create([
            'name'          => 'Approved Driver David',
            'email'         => 'david@test.com',
            'user_type'     => 2,
            'driver_status' => 'approved',
        ]);

        $this->vehicle1 = Vehicle::create([
            'user_id'         => $this->approvedDriver->id,
            'registration_no' => 'GLT-1001',
            'type'            => 'Van',
            'model'           => 'Hiace 2022',
            'total_seats'     => 14,
            'status'          => 'Active',
        ]);

        $this->vehicle2 = Vehicle::create([
            'user_id'         => $this->approvedDriver2->id,
            'registration_no' => 'GLT-2002',
            'type'            => 'Car',
            'model'           => 'Corolla',
            'total_seats'     => 4,
            'status'          => 'Active',
        ]);

        $this->activeRoute = RouteModel::create([
            'name'           => 'Gilgit - Hunza Express',
            'start_location' => 'Gilgit Terminal',
            'end_location'   => 'Aliabad Hunza',
            'status'         => 'Active',
        ]);

        $this->inactiveRoute = RouteModel::create([
            'name'           => 'Suspended Mountain Route',
            'start_location' => 'Gilgit',
            'end_location'   => 'Passu',
            'status'         => 'Inactive',
        ]);
    }

    // ==========================================
    // 1. ACCESS CONTROL TESTS
    // ==========================================

    public function test_guests_cannot_access_trips(): void
    {
        $this->get('/admin/trips')->assertRedirect('/login');
        $this->get('/admin/trips/create')->assertRedirect('/login');
    }

    public function test_passengers_cannot_access_trips(): void
    {
        $this->actingAs($this->passenger)->get('/admin/trips')->assertForbidden();
        $this->actingAs($this->passenger)->get('/admin/trips/create')->assertForbidden();
    }

    public function test_drivers_cannot_access_trips_admin(): void
    {
        $this->actingAs($this->approvedDriver)->get('/admin/trips')->assertForbidden();
        $this->actingAs($this->approvedDriver)->get('/admin/trips/create')->assertForbidden();
    }

    public function test_admin_can_access_trips_index(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/trips');
        $response->assertOk();
        $response->assertSee('Trips Management');
    }

    // ==========================================
    // 2. CREATE & VALIDATION TESTS
    // ==========================================

    public function test_admin_can_view_create_trip_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/trips/create');
        $response->assertOk();
        $response->assertSee('Schedule New Trip');
        // Approved driver Charlie must appear
        $response->assertSee('Approved Driver Charlie');
        // Active route must appear
        $response->assertSee('Gilgit - Hunza Express');
        // Pending driver Bob must not appear in driver dropdown
        $response->assertDontSee('Pending Driver Bob');
    }

    public function test_admin_can_store_a_valid_trip(): void
    {
        $payload = [
            'user_id'         => $this->approvedDriver->id,
            'vehicle_id'      => $this->vehicle1->id,
            'route_id'        => $this->activeRoute->id,
            'trip_date'       => '2026-10-15',
            'departure_time'  => '08:30',
            'available_seats' => 12,
            'status'          => 'Scheduled',
            'fare'            => 1500.00,
            'pickup_point'    => 'Gilgit Terminal Bay 2',
        ];

        $response = $this->actingAs($this->admin)->post('/admin/trips', $payload);

        $response->assertRedirect('/admin/trips');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('trips', [
            'user_id'         => $this->approvedDriver->id,
            'vehicle_id'      => $this->vehicle1->id,
            'route_id'        => $this->activeRoute->id,
            'departure_time'  => '08:30',
            'available_seats' => 12,
            'status'          => 'Scheduled',
            'fare'            => 1500.00,
            'pickup_point'    => 'Gilgit Terminal Bay 2',
        ]);

        $savedTrip = Trip::first();
        $this->assertEquals('2026-10-15', \Carbon\Carbon::parse($savedTrip->trip_date)->format('Y-m-d'));
    }

    public function test_cannot_assign_pending_or_non_driver(): void
    {
        $payload = [
            'user_id'         => $this->pendingDriver->id,
            'vehicle_id'      => $this->vehicle1->id,
            'route_id'        => $this->activeRoute->id,
            'trip_date'       => '2026-10-15',
            'departure_time'  => '09:00',
            'available_seats' => 5,
            'status'          => 'Scheduled',
        ];

        $response = $this->actingAs($this->admin)->post('/admin/trips', $payload);

        $response->assertSessionHasErrors(['user_id']);
        $this->assertDatabaseCount('trips', 0);
    }

    public function test_cannot_assign_vehicle_not_belonging_to_driver(): void
    {
        // vehicle2 belongs to approvedDriver2, NOT approvedDriver
        $payload = [
            'user_id'         => $this->approvedDriver->id,
            'vehicle_id'      => $this->vehicle2->id,
            'route_id'        => $this->activeRoute->id,
            'trip_date'       => '2026-10-15',
            'departure_time'  => '09:00',
            'available_seats' => 4,
            'status'          => 'Scheduled',
        ];

        $response = $this->actingAs($this->admin)->post('/admin/trips', $payload);

        $response->assertSessionHasErrors(['vehicle_id']);
        $this->assertDatabaseCount('trips', 0);
    }

    public function test_available_seats_cannot_exceed_vehicle_capacity(): void
    {
        // vehicle1 has total_seats = 14; we request 20 seats
        $payload = [
            'user_id'         => $this->approvedDriver->id,
            'vehicle_id'      => $this->vehicle1->id,
            'route_id'        => $this->activeRoute->id,
            'trip_date'       => '2026-10-15',
            'departure_time'  => '09:00',
            'available_seats' => 20,
            'status'          => 'Scheduled',
        ];

        $response = $this->actingAs($this->admin)->post('/admin/trips', $payload);

        $response->assertSessionHasErrors(['available_seats']);
        $this->assertDatabaseCount('trips', 0);
    }

    public function test_cannot_schedule_trip_on_inactive_route(): void
    {
        $payload = [
            'user_id'         => $this->approvedDriver->id,
            'vehicle_id'      => $this->vehicle1->id,
            'route_id'        => $this->inactiveRoute->id,
            'trip_date'       => '2026-10-15',
            'departure_time'  => '09:00',
            'available_seats' => 10,
            'status'          => 'Scheduled',
        ];

        $response = $this->actingAs($this->admin)->post('/admin/trips', $payload);

        $response->assertSessionHasErrors(['route_id']);
        $this->assertDatabaseCount('trips', 0);
    }

    // ==========================================
    // 3. SHOW, EDIT, UPDATE, DELETE TESTS
    // ==========================================

    public function test_admin_can_view_trip_show_page(): void
    {
        $trip = Trip::create([
            'user_id'         => $this->approvedDriver->id,
            'vehicle_id'      => $this->vehicle1->id,
            'route_id'        => $this->activeRoute->id,
            'trip_date'       => '2026-10-20',
            'departure_time'  => '10:00:00',
            'available_seats' => 14,
            'status'          => 'Scheduled',
            'fare'            => 1200,
        ]);

        $response = $this->actingAs($this->admin)->get("/admin/trips/{$trip->id}");

        $response->assertOk();
        $response->assertSee("Trip #{$trip->id}");
        $response->assertSee('Approved Driver Charlie');
        $response->assertSee('GLT-1001');
        $response->assertSee('Gilgit - Hunza Express');
    }

    public function test_admin_can_view_trip_edit_page(): void
    {
        $trip = Trip::create([
            'user_id'         => $this->approvedDriver->id,
            'vehicle_id'      => $this->vehicle1->id,
            'route_id'        => $this->activeRoute->id,
            'trip_date'       => '2026-10-20',
            'departure_time'  => '10:00:00',
            'available_seats' => 14,
            'status'          => 'Scheduled',
        ]);

        $response = $this->actingAs($this->admin)->get("/admin/trips/{$trip->id}/edit");

        $response->assertOk();
        $response->assertSee("Edit Trip #{$trip->id}");
    }

    public function test_admin_can_update_a_trip(): void
    {
        $trip = Trip::create([
            'user_id'         => $this->approvedDriver->id,
            'vehicle_id'      => $this->vehicle1->id,
            'route_id'        => $this->activeRoute->id,
            'trip_date'       => '2026-10-20',
            'departure_time'  => '10:00:00',
            'available_seats' => 14,
            'status'          => 'Scheduled',
        ]);

        $response = $this->actingAs($this->admin)->put("/admin/trips/{$trip->id}", [
            'user_id'         => $this->approvedDriver->id,
            'vehicle_id'      => $this->vehicle1->id,
            'route_id'        => $this->activeRoute->id,
            'trip_date'       => '2026-10-25',
            'departure_time'  => '14:00',
            'available_seats' => 8,
            'status'          => 'Completed',
            'fare'            => 1800,
            'pickup_point'    => 'New Terminal Gate 4',
        ]);

        $response->assertRedirect('/admin/trips');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('trips', [
            'id'              => $trip->id,
            'departure_time'  => '14:00',
            'available_seats' => 8,
            'status'          => 'Completed',
            'fare'            => 1800,
            'pickup_point'    => 'New Terminal Gate 4',
        ]);

        $this->assertEquals('2026-10-25', \Carbon\Carbon::parse($trip->fresh()->trip_date)->format('Y-m-d'));
    }

    public function test_admin_can_delete_a_trip(): void
    {
        $trip = Trip::create([
            'user_id'         => $this->approvedDriver->id,
            'vehicle_id'      => $this->vehicle1->id,
            'route_id'        => $this->activeRoute->id,
            'trip_date'       => '2026-10-20',
            'departure_time'  => '10:00:00',
            'available_seats' => 14,
            'status'          => 'Scheduled',
        ]);

        $response = $this->actingAs($this->admin)->delete("/admin/trips/{$trip->id}");

        $response->assertRedirect('/admin/trips');
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('trips', ['id' => $trip->id]);
    }

    // ==========================================
    // 4. FILTERING TESTS
    // ==========================================

    public function test_filter_trips_by_driver(): void
    {
        $trip1 = Trip::create([
            'user_id'         => $this->approvedDriver->id,
            'vehicle_id'      => $this->vehicle1->id,
            'route_id'        => $this->activeRoute->id,
            'trip_date'       => '2026-10-20',
            'departure_time'  => '08:00',
            'available_seats' => 10,
            'status'          => 'Scheduled',
        ]);

        $trip2 = Trip::create([
            'user_id'         => $this->approvedDriver2->id,
            'vehicle_id'      => $this->vehicle2->id,
            'route_id'        => $this->activeRoute->id,
            'trip_date'       => '2026-10-20',
            'departure_time'  => '09:00',
            'available_seats' => 4,
            'status'          => 'Scheduled',
        ]);

        $response = $this->actingAs($this->admin)->get("/admin/trips?driver_id={$this->approvedDriver->id}");
        $response->assertOk();
        $response->assertSee("#{$trip1->id}");
        $response->assertDontSee("#{$trip2->id}");
    }

    public function test_filter_trips_by_status(): void
    {
        $scheduledTrip = Trip::create([
            'user_id'         => $this->approvedDriver->id,
            'vehicle_id'      => $this->vehicle1->id,
            'route_id'        => $this->activeRoute->id,
            'trip_date'       => '2026-10-20',
            'departure_time'  => '08:00',
            'available_seats' => 10,
            'status'          => 'Scheduled',
        ]);

        $completedTrip = Trip::create([
            'user_id'         => $this->approvedDriver->id,
            'vehicle_id'      => $this->vehicle1->id,
            'route_id'        => $this->activeRoute->id,
            'trip_date'       => '2026-10-18',
            'departure_time'  => '08:00',
            'available_seats' => 10,
            'status'          => 'Completed',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/trips?status=Completed');
        $response->assertOk();
        $response->assertSee("#{$completedTrip->id}");
        $response->assertDontSee("#{$scheduledTrip->id}");
    }

    public function test_filter_trips_by_date(): void
    {
        $tripToday = Trip::create([
            'user_id'         => $this->approvedDriver->id,
            'vehicle_id'      => $this->vehicle1->id,
            'route_id'        => $this->activeRoute->id,
            'trip_date'       => '2026-10-15',
            'departure_time'  => '08:00',
            'available_seats' => 10,
            'status'          => 'Scheduled',
        ]);

        $tripNextWeek = Trip::create([
            'user_id'         => $this->approvedDriver->id,
            'vehicle_id'      => $this->vehicle1->id,
            'route_id'        => $this->activeRoute->id,
            'trip_date'       => '2026-10-22',
            'departure_time'  => '08:00',
            'available_seats' => 10,
            'status'          => 'Scheduled',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/trips?date=2026-10-15');
        $response->assertOk();
        $response->assertSee("#{$tripToday->id}");
        $response->assertDontSee("#{$tripNextWeek->id}");
    }
}
