<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class Step5AdminTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper: make an admin user (user_type = 1)
     */
    private function makeAdmin(): User
    {
        return User::create([
            'name'      => 'System Admin',
            'email'     => 'admin@routeconnect.com',
            'password'  => bcrypt('admin123456'),
            'user_type' => 1,
        ]);
    }

    /**
     * Helper: make a driver user (user_type = 2)
     */
    private function makeDriver(string $status = 'pending'): User
    {
        return User::create([
            'name'          => 'Test Driver',
            'email'         => 'driver_' . uniqid() . '@example.com',
            'phone'         => '03001234567',
            'password'      => bcrypt('password123'),
            'user_type'     => 2,
            'driver_status' => $status,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Test 1: Admin (user_type=1) can access /admin/dashboard
    // ─────────────────────────────────────────────────────────────────────────
    public function test_admin_can_access_dashboard()
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Welcome back, System Admin');
        $response->assertSee('Total Users');
        $response->assertSee('Pending Applications');
        $response->assertSee('Approved Drivers');
        $response->assertSee('Total Vehicles');
        $response->assertSee('Total Routes');
        $response->assertSee('Total Trips');
        $response->assertSee('Total Bookings');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Test 2: Passenger (user_type=0) CANNOT access admin dashboard (403)
    // ─────────────────────────────────────────────────────────────────────────
    public function test_passenger_cannot_access_admin_dashboard()
    {
        $passenger = User::create([
            'name'      => 'Normal Passenger',
            'email'     => 'passenger@example.com',
            'password'  => bcrypt('password123'),
            'user_type' => 0,
        ]);

        $response = $this->actingAs($passenger)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Test 3: Driver (user_type=2) CANNOT access admin dashboard (403)
    // ─────────────────────────────────────────────────────────────────────────
    public function test_driver_cannot_access_admin_dashboard()
    {
        $driver = $this->makeDriver('approved');

        $response = $this->actingAs($driver)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Test 4: Guest (unauthenticated) is redirected to login
    // ─────────────────────────────────────────────────────────────────────────
    public function test_guest_is_redirected_from_admin_dashboard()
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect(route('login'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Test 5: Admin can view pending driver applications page
    // ─────────────────────────────────────────────────────────────────────────
    public function test_admin_can_view_pending_driver_applications()
    {
        $admin  = $this->makeAdmin();
        $driver = $this->makeDriver('pending');

        $response = $this->actingAs($admin)->get('/admin/drivers/applications?status=pending');

        $response->assertStatus(200);
        $response->assertSee($driver->name);
        $response->assertSee($driver->email);
        $response->assertSee('Pending');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Test 6: Applications page shows all drivers when filter = all
    // ─────────────────────────────────────────────────────────────────────────
    public function test_admin_can_view_all_drivers_with_all_filter()
    {
        $admin   = $this->makeAdmin();
        $pending = $this->makeDriver('pending');
        $approved= $this->makeDriver('approved');
        $rejected= $this->makeDriver('rejected');

        $response = $this->actingAs($admin)->get('/admin/drivers/applications?status=all');

        $response->assertStatus(200);
        $response->assertSee($pending->name);
        $response->assertSee($approved->name);
        $response->assertSee($rejected->name);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Test 7: Admin can view driver application details
    // ─────────────────────────────────────────────────────────────────────────
    public function test_admin_can_view_driver_detail_page()
    {
        $admin  = $this->makeAdmin();
        $driver = $this->makeDriver('pending');

        $response = $this->actingAs($admin)->get("/admin/drivers/applications/{$driver->id}");

        $response->assertStatus(200);
        $response->assertSee($driver->name);
        $response->assertSee($driver->email);
        $response->assertSee('Pending Review');
        $response->assertSee('Approve Driver Application');
        $response->assertSee('Reject Application');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Test 8: Admin can APPROVE a pending driver
    // ─────────────────────────────────────────────────────────────────────────
    public function test_admin_can_approve_pending_driver()
    {
        $admin  = $this->makeAdmin();
        $driver = $this->makeDriver('pending');

        // Before approval: driver is pending
        $this->assertEquals('pending', $driver->driver_status);
        $this->assertEquals(2, $driver->user_type);

        // Admin approves
        $response = $this->actingAs($admin)->post("/admin/drivers/{$driver->id}/approve");

        $response->assertRedirect(route('admin.drivers.applications'));
        $response->assertSessionHas('success');

        // After approval: driver_status = approved, user_type STILL = 2
        $driver->refresh();
        $this->assertEquals('approved', $driver->driver_status);
        $this->assertEquals(2, $driver->user_type); // NEVER changes
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Test 9: Admin can REJECT a pending driver
    // ─────────────────────────────────────────────────────────────────────────
    public function test_admin_can_reject_pending_driver()
    {
        $admin  = $this->makeAdmin();
        $driver = $this->makeDriver('pending');

        $response = $this->actingAs($admin)->post("/admin/drivers/{$driver->id}/reject");

        $response->assertRedirect(route('admin.drivers.applications'));
        $response->assertSessionHas('success');

        // After rejection: driver_status = rejected, user_type STILL = 2
        $driver->refresh();
        $this->assertEquals('rejected', $driver->driver_status);
        $this->assertEquals(2, $driver->user_type); // NEVER changes
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Test 10: Approved driver can access /driver/dashboard
    // ─────────────────────────────────────────────────────────────────────────
    public function test_approved_driver_can_access_driver_dashboard()
    {
        $driver = $this->makeDriver('approved');

        $response = $this->actingAs($driver)->get('/driver/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Driver Status: Approved');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Test 11: PENDING driver CANNOT access /driver/dashboard
    // ─────────────────────────────────────────────────────────────────────────
    public function test_pending_driver_cannot_access_driver_dashboard()
    {
        $driver = $this->makeDriver('pending');

        $response = $this->actingAs($driver)->get('/driver/dashboard');
        $response->assertRedirect(route('driver.pending'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Test 12: REJECTED driver CANNOT access /driver/dashboard
    // ─────────────────────────────────────────────────────────────────────────
    public function test_rejected_driver_cannot_access_driver_dashboard()
    {
        $driver = $this->makeDriver('rejected');

        $response = $this->actingAs($driver)->get('/driver/dashboard');
        $response->assertRedirect(route('driver.pending'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Test 13: After approval, driver dashboard is accessible
    // ─────────────────────────────────────────────────────────────────────────
    public function test_admin_approval_grants_driver_dashboard_access()
    {
        $admin  = $this->makeAdmin();
        $driver = $this->makeDriver('pending');

        // Before approval
        $this->actingAs($driver)->get('/driver/dashboard')->assertRedirect(route('driver.pending'));

        // Admin approves
        $this->actingAs($admin)->post("/admin/drivers/{$driver->id}/approve");

        // After approval
        $driver->refresh();
        $this->assertEquals('approved', $driver->driver_status);
        $this->assertEquals(2, $driver->user_type);

        $this->actingAs($driver)->get('/driver/dashboard')->assertStatus(200);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Test 14: Non-admin cannot approve/reject drivers
    // ─────────────────────────────────────────────────────────────────────────
    public function test_non_admin_cannot_approve_drivers()
    {
        $passenger = User::create([
            'name'      => 'Passenger',
            'email'     => 'pass2@example.com',
            'password'  => bcrypt('password123'),
            'user_type' => 0,
        ]);
        $driver = $this->makeDriver('pending');

        $response = $this->actingAs($passenger)->post("/admin/drivers/{$driver->id}/approve");
        $response->assertStatus(403);
    }
}
