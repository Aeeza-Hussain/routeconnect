<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardLayoutAndSidebarTest extends TestCase
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

        $this->pendingDriver = User::factory()->create([
            'name'          => 'Pending Bob',
            'email'         => 'bob@test.com',
            'user_type'     => 2,
            'driver_status' => 'pending',
        ]);

        $this->approvedDriver = User::factory()->create([
            'name'          => 'Approved Driver Dan',
            'email'         => 'dan@test.com',
            'user_type'     => 2,
            'driver_status' => 'approved',
        ]);
    }

    public function test_admin_can_access_settings_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/settings');
        $response->assertStatus(200);
        $response->assertSee('Platform Settings');
        $response->assertSee('RouteConnect');
    }

    public function test_non_admin_cannot_access_admin_settings(): void
    {
        $response1 = $this->actingAs($this->passenger)->get('/admin/settings');
        $response1->assertStatus(403);

        $response2 = $this->actingAs($this->approvedDriver)->get('/admin/settings');
        $response2->assertStatus(403);
    }

    public function test_admin_sidebar_contains_all_required_links(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');
        $response->assertStatus(200);

        // Sidebar items
        $response->assertSee(route('admin.dashboard'));
        $response->assertSee(route('admin.drivers.applications'));
        $response->assertSee(route('admin.users.index'));
        $response->assertSee(route('admin.vehicles.index'));
        $response->assertSee(route('admin.routes.index'));
        $response->assertSee(route('admin.stops.index'));
        $response->assertSee(route('admin.trips.index'));
        $response->assertSee(route('admin.bookings.index'));
        $response->assertSee(route('admin.settings.index'));
        $response->assertSee(route('logout'));
    }

    public function test_approved_driver_can_access_driver_portal_pages(): void
    {
        $pages = [
            '/driver/dashboard' => 'Driver Console',
            '/driver/profile'   => 'Driver Profile',
            '/driver/vehicle'   => 'Assigned Vehicle',
            '/driver/trips'     => 'Scheduled Trips',
            '/driver/bookings'  => 'Passenger Bookings',
            '/driver/messages'  => 'Passenger Trip Messages',
            '/driver/settings'  => 'Driver Account Settings',
        ];

        foreach ($pages as $uri => $text) {
            $response = $this->actingAs($this->approvedDriver)->get($uri);
            $response->assertStatus(200);
            $response->assertSee($text);
        }
    }

    public function test_driver_sidebar_contains_all_required_links(): void
    {
        $response = $this->actingAs($this->approvedDriver)->get('/driver/dashboard');
        $response->assertStatus(200);

        // Sidebar items
        $response->assertSee(route('driver.dashboard'));
        $response->assertSee(route('driver.profile'));
        $response->assertSee(route('driver.vehicle'));
        $response->assertSee(route('driver.trips'));
        $response->assertSee(route('driver.bookings'));
        $response->assertSee(route('driver.messages'));
        $response->assertSee(route('driver.settings'));
        $response->assertSee(route('logout'));
    }

    public function test_unapproved_users_cannot_access_driver_portal(): void
    {
        $pages = [
            '/driver/dashboard',
            '/driver/profile',
            '/driver/vehicle',
            '/driver/trips',
            '/driver/bookings',
            '/driver/messages',
            '/driver/settings',
        ];

        foreach ($pages as $uri) {
            // Passenger
            $response1 = $this->actingAs($this->passenger)->get($uri);
            $response1->assertStatus(403);

            // Pending driver redirected to /driver/pending
            $response2 = $this->actingAs($this->pendingDriver)->get($uri);
            $response2->assertRedirect('/driver/pending');
        }
    }
}
