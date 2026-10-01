<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class Step3AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test 1: Passenger Registration
     */
    public function test_passenger_registration_creates_passenger_user()
    {
        $response = $this->post('/register', [
            'name' => 'Passenger One',
            'email' => 'passenger1@example.com',
            'phone' => '03001112233',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('users', [
            'email' => 'passenger1@example.com',
            'role' => 'passenger',
            'driver_status' => null,
        ]);
    }

    /**
     * Test 2: Driver Registration Creates Pending Driver
     */
    public function test_driver_registration_creates_pending_driver()
    {
        $response = $this->post('/driver/register', [
            'name' => 'Driver Candidate',
            'email' => 'driver1@example.com',
            'phone' => '03004445566',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('driver.pending'));
        $this->assertDatabaseHas('users', [
            'email' => 'driver1@example.com',
            'role' => 'driver',
            'driver_status' => 'pending',
        ]);
    }

    /**
     * Test 3: Pending Driver Cannot Access Driver Dashboard
     */
    public function test_pending_driver_cannot_access_driver_dashboard()
    {
        $driver = User::create([
            'name' => 'Pending Driver',
            'email' => 'pending@example.com',
            'password' => bcrypt('password123'),
            'role' => 'driver',
            'driver_status' => 'pending',
        ]);

        $response = $this->actingAs($driver)->get('/driver/dashboard');
        $response->assertRedirect(route('driver.pending'));
    }

    /**
     * Test 4: Admin Can Approve Pending Driver
     */
    public function test_admin_can_approve_pending_driver()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $driver = User::create([
            'name' => 'Driver Candidate',
            'email' => 'candidate@example.com',
            'password' => bcrypt('password123'),
            'role' => 'driver',
            'driver_status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post("/admin/drivers/{$driver->id}/approve");

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $driver->id,
            'driver_status' => 'approved',
        ]);
    }

    /**
     * Test 5: Approved Driver Can Access Driver Dashboard
     */
    public function test_approved_driver_can_access_driver_dashboard()
    {
        $approvedDriver = User::create([
            'name' => 'Approved Driver',
            'email' => 'approved@example.com',
            'password' => bcrypt('password123'),
            'role' => 'driver',
            'driver_status' => 'approved',
        ]);

        $response = $this->actingAs($approvedDriver)->get('/driver/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Driver Status: Approved');
    }

    /**
     * Test 6: Admin Can Reject Driver & Rejected Driver Cannot Access Dashboard
     */
    public function test_rejected_driver_cannot_access_driver_dashboard()
    {
        $rejectedDriver = User::create([
            'name' => 'Rejected Driver',
            'email' => 'rejected@example.com',
            'password' => bcrypt('password123'),
            'role' => 'driver',
            'driver_status' => 'rejected',
        ]);

        $response = $this->actingAs($rejectedDriver)->get('/driver/dashboard');
        $response->assertRedirect(route('driver.pending'));
    }

    /**
     * Test 7: Passengers and Drivers Cannot Access Admin Dashboard
     */
    public function test_unauthorized_users_cannot_access_admin_dashboard()
    {
        $passenger = User::create([
            'name' => 'Passenger',
            'email' => 'pass@example.com',
            'password' => bcrypt('password123'),
            'role' => 'passenger',
        ]);

        $response = $this->actingAs($passenger)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    /**
     * Test 8: Unauthenticated Users Cannot Access Protected Dashboards
     */
    public function test_guest_cannot_access_protected_dashboards()
    {
        $responseAdmin = $this->get('/admin/dashboard');
        $responseAdmin->assertRedirect(route('login'));

        $responseDriver = $this->get('/driver/dashboard');
        $responseDriver->assertRedirect(route('login'));
    }
}
