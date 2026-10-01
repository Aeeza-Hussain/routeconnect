<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class Step5AdminTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test 1: Admin (user_type=1) can access dashboard
     */
    public function test_admin_can_access_dashboard()
    {
        $admin = User::create([
            'name'      => 'System Admin',
            'email'     => 'admin@routeconnect.com',
            'password'  => bcrypt('admin123456'),
            'user_type' => 1,
        ]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Welcome, System Admin');
        $response->assertSee('Total Users');
        $response->assertSee('Pending Applications');
    }

    /**
     * Test 2: Passenger & Driver cannot access admin dashboard
     */
    public function test_passenger_and_driver_cannot_access_admin_dashboard()
    {
        $passenger = User::create([
            'name'      => 'Passenger',
            'email'     => 'passenger@example.com',
            'password'  => bcrypt('password123'),
            'user_type' => 0,
        ]);

        $driver = User::create([
            'name'          => 'Approved Driver',
            'email'         => 'driver@example.com',
            'password'      => bcrypt('password123'),
            'user_type'     => 2,
            'driver_status' => 'approved',
        ]);

        $this->actingAs($passenger)->get('/admin/dashboard')->assertStatus(403);
        $this->actingAs($driver)->get('/admin/dashboard')->assertStatus(403);
    }

    /**
     * Test 3: Admin can view pending driver applications list
     */
    public function test_admin_can_view_pending_driver_applications()
    {
        $admin = User::create([
            'name'      => 'System Admin',
            'email'     => 'admin@routeconnect.com',
            'password'  => bcrypt('admin123456'),
            'user_type' => 1,
        ]);

        $driverApplicant = User::create([
            'name'          => 'Applicant Driver',
            'email'         => 'applicant@example.com',
            'phone'         => '03001234567',
            'password'      => bcrypt('password123'),
            'user_type'     => 2,
            'driver_status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->get('/admin/drivers/applications');

        $response->assertStatus(200);
        $response->assertSee('Applicant Driver');
        $response->assertSee('applicant@example.com');
    }

    /**
     * Test 4: Admin can approve pending driver and driver gets dashboard access
     */
    public function test_admin_approval_grants_driver_dashboard_access()
    {
        $admin = User::create([
            'name'      => 'System Admin',
            'email'     => 'admin@routeconnect.com',
            'password'  => bcrypt('admin123456'),
            'user_type' => 1,
        ]);

        $driver = User::create([
            'name'          => 'New Driver',
            'email'         => 'newdriver@example.com',
            'password'      => bcrypt('password123'),
            'user_type'     => 2,
            'driver_status' => 'pending',
        ]);

        // Before approval: driver cannot access driver dashboard
        $this->actingAs($driver)->get('/driver/dashboard')->assertRedirect(route('driver.pending'));

        // Admin approves
        $response = $this->actingAs($admin)->post("/admin/drivers/{$driver->id}/approve");
        $response->assertRedirect(route('admin.drivers.applications'));
        $response->assertSessionHas('success', 'Driver application approved successfully.');

        // After approval: driver can access driver dashboard
        $driver->refresh();
        $this->assertEquals('approved', $driver->driver_status);
        $this->assertEquals(2, $driver->user_type); // user_type stays = 2
        $this->actingAs($driver)->get('/driver/dashboard')->assertStatus(200);
    }

    /**
     * Test 5: Admin can reject driver application
     */
    public function test_admin_rejection_denies_driver_access()
    {
        $admin = User::create([
            'name'      => 'System Admin',
            'email'     => 'admin@routeconnect.com',
            'password'  => bcrypt('admin123456'),
            'user_type' => 1,
        ]);

        $driver = User::create([
            'name'          => 'Rejected Candidate',
            'email'         => 'rejectme@example.com',
            'password'      => bcrypt('password123'),
            'user_type'     => 2,
            'driver_status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post("/admin/drivers/{$driver->id}/reject");
        $response->assertSessionHas('success', 'Driver application rejected.');

        $driver->refresh();
        $this->assertEquals('rejected', $driver->driver_status);
        $this->assertEquals(2, $driver->user_type); // user_type stays = 2
        $this->actingAs($driver)->get('/driver/dashboard')->assertRedirect(route('driver.pending'));
    }
}
