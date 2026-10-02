<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VehicleCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $passenger;
    private User $pendingDriver;
    private User $approvedDriver;

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
            'email'         => 'approved@test.com',
            'user_type'     => 2,
            'driver_status' => 'approved',
        ]);
    }

    public function test_guests_cannot_access_vehicles(): void
    {
        $response = $this->get('/admin/vehicles');
        $response->assertRedirect('/login');
    }

    public function test_passengers_cannot_access_vehicles(): void
    {
        $response = $this->actingAs($this->passenger)->get('/admin/vehicles');
        $response->assertStatus(403);
    }

    public function test_drivers_cannot_access_vehicles(): void
    {
        $response = $this->actingAs($this->approvedDriver)->get('/admin/vehicles');
        $response->assertStatus(403);
    }

    public function test_admin_can_view_vehicles_index(): void
    {
        Vehicle::create([
            'user_id'         => $this->approvedDriver->id,
            'registration_no' => 'ABC-1234',
            'type'            => 'Van',
            'model'           => 'Toyota HiAce 2022',
            'total_seats'     => 14,
            'status'          => 'Active',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/vehicles');
        $response->assertStatus(200);
        $response->assertSee('ABC-1234');
        $response->assertSee('Toyota HiAce 2022');
        $response->assertSee('Approved Driver Charlie');
    }

    public function test_admin_can_view_create_vehicle_form(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/vehicles/create');
        $response->assertStatus(200);
        $response->assertSee('Add Vehicle');
        $response->assertSee('Approved Driver Charlie');
        // Pending driver and passenger should NOT appear as selectable options
        $response->assertDontSee('Pending Driver Bob');
    }

    public function test_admin_can_store_a_valid_vehicle(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/vehicles', [
            'user_id'         => $this->approvedDriver->id,
            'registration_no' => 'lee-5566',
            'type'            => 'Van',
            'model'           => 'Toyota HiAce Grand Cabin',
            'total_seats'     => 15,
            'status'          => 'Active',
        ]);

        $response->assertRedirect('/admin/vehicles');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('vehicles', [
            'user_id'         => $this->approvedDriver->id,
            'registration_no' => 'LEE-5566',
            'type'            => 'Van',
            'model'           => 'Toyota HiAce Grand Cabin',
            'total_seats'     => 15,
            'status'          => 'Active',
        ]);
    }

    public function test_cannot_assign_vehicle_to_unapproved_or_passenger_user(): void
    {
        // Try assigning to passenger
        $response1 = $this->actingAs($this->admin)->post('/admin/vehicles', [
            'user_id'         => $this->passenger->id,
            'registration_no' => 'XYZ-1111',
            'type'            => 'Car',
            'model'           => 'Honda Civic',
            'total_seats'     => 4,
            'status'          => 'Active',
        ]);
        $response1->assertSessionHasErrors('user_id');

        // Try assigning to pending driver
        $response2 = $this->actingAs($this->admin)->post('/admin/vehicles', [
            'user_id'         => $this->pendingDriver->id,
            'registration_no' => 'XYZ-2222',
            'type'            => 'Car',
            'model'           => 'Toyota Corolla',
            'total_seats'     => 4,
            'status'          => 'Active',
        ]);
        $response2->assertSessionHasErrors('user_id');
    }

    public function test_vehicle_registration_number_must_be_unique(): void
    {
        Vehicle::create([
            'user_id'         => $this->approvedDriver->id,
            'registration_no' => 'DUP-9999',
            'type'            => 'Van',
            'model'           => 'Suzuki Bolan',
            'total_seats'     => 7,
            'status'          => 'Active',
        ]);

        $response = $this->actingAs($this->admin)->post('/admin/vehicles', [
            'user_id'         => $this->approvedDriver->id,
            'registration_no' => 'DUP-9999',
            'type'            => 'Daba',
            'model'           => 'Suzuki Bolan 2020',
            'total_seats'     => 7,
            'status'          => 'Active',
        ]);

        $response->assertSessionHasErrors('registration_no');
    }

    public function test_admin_can_view_vehicle_show_page(): void
    {
        $vehicle = Vehicle::create([
            'user_id'         => $this->approvedDriver->id,
            'registration_no' => 'SHOW-777',
            'type'            => 'Bus',
            'model'           => 'Toyota Coaster',
            'total_seats'     => 29,
            'status'          => 'Active',
        ]);

        $response = $this->actingAs($this->admin)->get("/admin/vehicles/{$vehicle->id}");
        $response->assertStatus(200);
        $response->assertSee('SHOW-777');
        $response->assertSee('Toyota Coaster');
        $response->assertSee('Approved Driver Charlie');
    }

    public function test_admin_can_view_edit_vehicle_form(): void
    {
        $vehicle = Vehicle::create([
            'user_id'         => $this->approvedDriver->id,
            'registration_no' => 'EDIT-123',
            'type'            => 'Car',
            'model'           => 'Toyota Corolla 2021',
            'total_seats'     => 4,
            'status'          => 'Active',
        ]);

        $response = $this->actingAs($this->admin)->get("/admin/vehicles/{$vehicle->id}/edit");
        $response->assertStatus(200);
        $response->assertSee('EDIT-123');
        $response->assertSee('Toyota Corolla 2021');
    }

    public function test_admin_can_update_vehicle(): void
    {
        $vehicle = Vehicle::create([
            'user_id'         => $this->approvedDriver->id,
            'registration_no' => 'UPD-001',
            'type'            => 'Van',
            'model'           => 'Old Model',
            'total_seats'     => 12,
            'status'          => 'Active',
        ]);

        $response = $this->actingAs($this->admin)->put("/admin/vehicles/{$vehicle->id}", [
            'user_id'         => $this->approvedDriver->id,
            'registration_no' => 'UPD-001',
            'type'            => 'Van',
            'model'           => 'Updated Model 2024',
            'total_seats'     => 14,
            'status'          => 'Inactive',
        ]);

        $response->assertRedirect('/admin/vehicles');
        $response->assertSessionHas('success');

        $vehicle->refresh();
        $this->assertEquals('Updated Model 2024', $vehicle->model);
        $this->assertEquals(14, $vehicle->total_seats);
        $this->assertEquals('Inactive', $vehicle->status);
    }

    public function test_admin_can_delete_vehicle(): void
    {
        $vehicle = Vehicle::create([
            'user_id'         => $this->approvedDriver->id,
            'registration_no' => 'DEL-999',
            'type'            => 'Car',
            'model'           => 'Delete Me',
            'total_seats'     => 4,
            'status'          => 'Active',
        ]);

        $response = $this->actingAs($this->admin)->delete("/admin/vehicles/{$vehicle->id}");
        $response->assertRedirect('/admin/vehicles');
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('vehicles', ['id' => $vehicle->id]);
    }

    public function test_admin_can_search_vehicles_by_number(): void
    {
        Vehicle::create([
            'user_id'         => $this->approvedDriver->id,
            'registration_no' => 'MATCH-111',
            'type'            => 'Van',
            'model'           => 'Target Van',
            'total_seats'     => 14,
            'status'          => 'Active',
        ]);

        Vehicle::create([
            'user_id'         => $this->approvedDriver->id,
            'registration_no' => 'OTHER-222',
            'type'            => 'Car',
            'model'           => 'Ignored Car',
            'total_seats'     => 4,
            'status'          => 'Active',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/vehicles?search=MATCH');
        $response->assertStatus(200);
        $response->assertSee('MATCH-111');
        $response->assertDontSee('OTHER-222');
    }

    public function test_admin_can_filter_vehicles_by_type_and_status(): void
    {
        Vehicle::create([
            'user_id'         => $this->approvedDriver->id,
            'registration_no' => 'VAN-ACTIVE',
            'type'            => 'Van',
            'model'           => 'Van One',
            'total_seats'     => 14,
            'status'          => 'Active',
        ]);

        Vehicle::create([
            'user_id'         => $this->approvedDriver->id,
            'registration_no' => 'BUS-INACTIVE',
            'type'            => 'Bus',
            'model'           => 'Bus One',
            'total_seats'     => 30,
            'status'          => 'Inactive',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/vehicles?type=Van&status=active');
        $response->assertStatus(200);
        $response->assertSee('VAN-ACTIVE');
        $response->assertDontSee('BUS-INACTIVE');
    }
}
