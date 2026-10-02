<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Route as RouteModel;
use App\Models\Trip;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminDashboardCompleteTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $passenger;
    private User $driver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name'          => 'Admin Boss',
            'email'         => 'admin@test.com',
            'user_type'     => 1,
            'driver_status' => null,
        ]);

        $this->passenger = User::factory()->create([
            'name'          => 'Passenger Alice',
            'email'         => 'alice@test.com',
            'user_type'     => 0,
            'driver_status' => null,
        ]);

        $this->driver = User::factory()->create([
            'name'          => 'Driver Bob',
            'email'         => 'bob@test.com',
            'user_type'     => 2,
            'driver_status' => 'pending',
            'license_no'    => 'DL-998877',
        ]);
    }

    public function test_guests_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_passengers_cannot_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->passenger)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_drivers_cannot_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->driver)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_dashboard_with_all_statistics_and_actions(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');
        $response->assertStatus(200);

        // Header
        $response->assertSee('Admin Boss');
        $response->assertSee('Operations Control Panel');

        // Statistics cards
        $response->assertSee('Total Users');
        $response->assertSee('Pending Drivers');
        $response->assertSee('Approved Drivers');
        $response->assertSee('Total Vehicles');
        $response->assertSee('Total Routes');
        $response->assertSee('Total Trips');
        $response->assertSee('Total Bookings');

        // Quick Actions
        $response->assertSee('Quick Actions');
        $response->assertSee('Driver Apps');
        $response->assertSee('Users');
        $response->assertSee('Vehicles');
        $response->assertSee('Routes');
        $response->assertSee('Stops');
        $response->assertSee('Trips');
        $response->assertSee('Bookings');

        // Recent Applications Table
        $response->assertSee('Recent Pending Driver Applications');
        $response->assertSee('Driver Bob');
        $response->assertSee('bob@test.com');
        $response->assertSee('DL-998877');

        // Recent Bookings Table
        $response->assertSee('Recent Bookings');
    }

    public function test_admin_can_access_platform_module_routes(): void
    {
        $modules = [
            '/admin/vehicles' => 'Vehicles Management',
            '/admin/routes'   => 'Routes Management',
            '/admin/stops'    => 'Stops Management',
            '/admin/trips'    => 'Trips Management',
            '/admin/bookings' => 'Bookings Management',
        ];

        foreach ($modules as $uri => $title) {
            $response = $this->actingAs($this->admin)->get($uri);
            $response->assertStatus(200);
            $response->assertSee($title);
        }
    }

    public function test_non_admins_cannot_access_platform_module_routes(): void
    {
        $modules = [
            '/admin/vehicles',
            '/admin/routes',
            '/admin/stops',
            '/admin/trips',
            '/admin/bookings',
        ];

        foreach ($modules as $uri) {
            $response = $this->actingAs($this->passenger)->get($uri);
            $response->assertStatus(403);

            $response = $this->actingAs($this->driver)->get($uri);
            $response->assertStatus(403);
        }
    }
}
