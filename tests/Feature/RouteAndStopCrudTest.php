<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Route as RouteModel;
use App\Models\Stop;
use App\Models\RouteStop;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RouteAndStopCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $passenger;
    private User $driver;

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

        $this->driver = User::factory()->create([
            'name'          => 'Driver Bob',
            'email'         => 'driver@test.com',
            'user_type'     => 2,
            'driver_status' => 'approved',
        ]);
    }

    // ==========================================
    // 1. ACCESS CONTROL TESTS
    // ==========================================

    public function test_guests_cannot_access_routes_or_stops(): void
    {
        $this->get('/admin/routes')->assertRedirect('/login');
        $this->get('/admin/routes/create')->assertRedirect('/login');
        $this->get('/admin/stops')->assertRedirect('/login');
        $this->get('/admin/stops/create')->assertRedirect('/login');
    }

    public function test_passengers_cannot_access_routes_or_stops(): void
    {
        $this->actingAs($this->passenger)->get('/admin/routes')->assertForbidden();
        $this->actingAs($this->passenger)->get('/admin/stops')->assertForbidden();
    }

    public function test_drivers_cannot_access_routes_or_stops(): void
    {
        $this->actingAs($this->driver)->get('/admin/routes')->assertForbidden();
        $this->actingAs($this->driver)->get('/admin/stops')->assertForbidden();
    }

    public function test_admin_can_access_routes_and_stops_index(): void
    {
        $this->actingAs($this->admin)->get('/admin/routes')->assertOk();
        $this->actingAs($this->admin)->get('/admin/stops')->assertOk();
    }

    // ==========================================
    // 2. ROUTES CRUD TESTS
    // ==========================================

    public function test_admin_can_view_routes_create_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/routes/create');
        $response->assertOk();
        $response->assertSee('Add New Route');
    }

    public function test_admin_can_store_a_valid_route(): void
    {
        $payload = [
            'name'           => 'Gilgit - Hunza Express',
            'start_location' => 'Gilgit Terminal',
            'end_location'   => 'Aliabad Hunza',
            'status'         => 'Active',
        ];

        $response = $this->actingAs($this->admin)->post('/admin/routes', $payload);

        $response->assertRedirect('/admin/routes');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('routes', [
            'name'           => 'Gilgit - Hunza Express',
            'start_location' => 'Gilgit Terminal',
            'end_location'   => 'Aliabad Hunza',
            'status'         => 'Active',
        ]);
    }

    public function test_route_creation_validation(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/routes', [
            'name'           => '',
            'start_location' => '',
            'end_location'   => '',
            'status'         => 'InvalidStatus',
        ]);

        $response->assertSessionHasErrors(['name', 'start_location', 'end_location', 'status']);
    }

    public function test_admin_can_view_route_show_page(): void
    {
        $route = RouteModel::create([
            'name'           => 'Skardu - Gilgit Highway',
            'start_location' => 'Skardu City',
            'end_location'   => 'Gilgit Bus Stand',
            'status'         => 'Active',
        ]);

        $response = $this->actingAs($this->admin)->get("/admin/routes/{$route->id}");
        $response->assertOk();
        $response->assertSee('Skardu - Gilgit Highway');
        $response->assertSee('Skardu City');
        $response->assertSee('Gilgit Bus Stand');
    }

    public function test_admin_can_view_route_edit_page(): void
    {
        $route = RouteModel::create([
            'name'           => 'Test Route',
            'start_location' => 'Origin',
            'end_location'   => 'Destination',
            'status'         => 'Active',
        ]);

        $response = $this->actingAs($this->admin)->get("/admin/routes/{$route->id}/edit");
        $response->assertOk();
        $response->assertSee('Edit: Test Route');
    }

    public function test_admin_can_update_a_route(): void
    {
        $route = RouteModel::create([
            'name'           => 'Old Route Name',
            'start_location' => 'Old Origin',
            'end_location'   => 'Old Dest',
            'status'         => 'Active',
        ]);

        $response = $this->actingAs($this->admin)->put("/admin/routes/{$route->id}", [
            'name'           => 'Updated Route Name',
            'start_location' => 'New Origin',
            'end_location'   => 'New Dest',
            'status'         => 'Inactive',
        ]);

        $response->assertRedirect('/admin/routes');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('routes', [
            'id'             => $route->id,
            'name'           => 'Updated Route Name',
            'start_location' => 'New Origin',
            'end_location'   => 'New Dest',
            'status'         => 'Inactive',
        ]);
    }

    public function test_admin_can_delete_a_route(): void
    {
        $route = RouteModel::create([
            'name'           => 'Route To Delete',
            'start_location' => 'City A',
            'end_location'   => 'City B',
            'status'         => 'Active',
        ]);

        $response = $this->actingAs($this->admin)->delete("/admin/routes/{$route->id}");

        $response->assertRedirect('/admin/routes');
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('routes', ['id' => $route->id]);
    }

    public function test_route_search_and_status_filter(): void
    {
        RouteModel::create([
            'name'           => 'Karakoram Highway South',
            'start_location' => 'Islamabad',
            'end_location'   => 'Gilgit',
            'status'         => 'Active',
        ]);

        RouteModel::create([
            'name'           => 'Baltistan Road Local',
            'start_location' => 'Skardu',
            'end_location'   => 'Khaplu',
            'status'         => 'Inactive',
        ]);

        // Search match
        $searchRes = $this->actingAs($this->admin)->get('/admin/routes?search=Karakoram');
        $searchRes->assertSee('Karakoram Highway South');
        $searchRes->assertDontSee('Baltistan Road Local');

        // Status filter match
        $statusRes = $this->actingAs($this->admin)->get('/admin/routes?status=Inactive');
        $statusRes->assertSee('Baltistan Road Local');
        $statusRes->assertDontSee('Karakoram Highway South');
    }

    // ==========================================
    // 3. STOPS CRUD TESTS
    // ==========================================

    public function test_admin_can_view_stops_create_page(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/stops/create');
        $response->assertOk();
        $response->assertSee('Add New Stop');
    }

    public function test_admin_can_store_a_valid_stop(): void
    {
        $payload = [
            'name'     => 'Danyor Chowk',
            'location' => 'Gilgit Karakoram Crossing',
            'status'   => 'Active',
        ];

        $response = $this->actingAs($this->admin)->post('/admin/stops', $payload);

        $response->assertRedirect('/admin/stops');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('stops', [
            'name'     => 'Danyor Chowk',
            'location' => 'Gilgit Karakoram Crossing',
            'status'   => 'Active',
        ]);
    }

    public function test_stop_creation_validation(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/stops', [
            'name'     => '',
            'location' => 'Some location',
            'status'   => 'InvalidStatus',
        ]);

        $response->assertSessionHasErrors(['name', 'status']);
    }

    public function test_admin_can_view_stop_show_page(): void
    {
        $stop = Stop::create([
            'name'     => 'Aliabad Center',
            'location' => 'Hunza Main Bazaar',
            'status'   => 'Active',
        ]);

        $response = $this->actingAs($this->admin)->get("/admin/stops/{$stop->id}");
        $response->assertOk();
        $response->assertSee('Aliabad Center');
        $response->assertSee('Hunza Main Bazaar');
    }

    public function test_admin_can_update_a_stop(): void
    {
        $stop = Stop::create([
            'name'     => 'Old Stop',
            'location' => 'Old Loc',
            'status'   => 'Active',
        ]);

        $response = $this->actingAs($this->admin)->put("/admin/stops/{$stop->id}", [
            'name'     => 'Renamed Stop',
            'location' => 'New Area Description',
            'status'   => 'Inactive',
        ]);

        $response->assertRedirect('/admin/stops');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('stops', [
            'id'       => $stop->id,
            'name'     => 'Renamed Stop',
            'location' => 'New Area Description',
            'status'   => 'Inactive',
        ]);
    }

    public function test_admin_can_delete_a_stop(): void
    {
        $stop = Stop::create([
            'name'     => 'Stop to delete',
            'location' => 'Address',
            'status'   => 'Active',
        ]);

        $response = $this->actingAs($this->admin)->delete("/admin/stops/{$stop->id}");

        $response->assertRedirect('/admin/stops');
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('stops', ['id' => $stop->id]);
    }

    public function test_stop_search_and_status_filter(): void
    {
        Stop::create(['name' => 'Nomal Bridge', 'location' => 'Nomal Valley', 'status' => 'Active']);
        Stop::create(['name' => 'Nagar Mor', 'location' => 'Nagar Road', 'status' => 'Inactive']);

        $searchRes = $this->actingAs($this->admin)->get('/admin/stops?search=Nomal');
        $searchRes->assertOk();
        $searchRes->assertSee('Nomal Bridge');
        $searchRes->assertDontSee('Nagar Mor');

        $statusRes = $this->actingAs($this->admin)->get('/admin/stops?status=Inactive');
        $statusRes->assertSee('Nagar Mor');
        $statusRes->assertDontSee('Nomal Bridge');
    }

    // ==========================================
    // 4. ROUTE STOPS ASSIGNMENT & ORDER TESTS
    // ==========================================

    public function test_admin_can_assign_stop_to_route(): void
    {
        $route = RouteModel::create([
            'name'           => 'Gilgit - Hunza Route',
            'start_location' => 'Gilgit',
            'end_location'   => 'Hunza',
            'status'         => 'Active',
        ]);

        $stop = Stop::create([
            'name'     => 'Danyor',
            'location' => 'Danyor Chowk',
            'status'   => 'Active',
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/routes/{$route->id}/stops", [
            'stop_id'    => $stop->id,
            'stop_order' => 1,
        ]);

        $response->assertRedirect("/admin/routes/{$route->id}");
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('route_stops', [
            'route_id'   => $route->id,
            'stop_id'    => $stop->id,
            'stop_order' => 1,
        ]);
    }

    public function test_prevent_duplicate_stops_on_same_route(): void
    {
        $route = RouteModel::create([
            'name'           => 'Gilgit - Hunza Route',
            'start_location' => 'Gilgit',
            'end_location'   => 'Hunza',
            'status'         => 'Active',
        ]);

        $stop = Stop::create([
            'name'     => 'Danyor',
            'location' => 'Danyor Chowk',
            'status'   => 'Active',
        ]);

        // Assign stop first time
        RouteStop::create([
            'route_id'   => $route->id,
            'stop_id'    => $stop->id,
            'stop_order' => 1,
        ]);

        // Attempt duplicate assignment
        $response = $this->actingAs($this->admin)->post("/admin/routes/{$route->id}/stops", [
            'stop_id'    => $stop->id,
            'stop_order' => 2,
        ]);

        $response->assertRedirect("/admin/routes/{$route->id}");
        $response->assertSessionHas('error');

        // Only 1 record should exist in route_stops
        $this->assertEquals(1, RouteStop::where('route_id', $route->id)->where('stop_id', $stop->id)->count());
    }

    public function test_admin_can_update_stop_order_on_route(): void
    {
        $route = RouteModel::create([
            'name'           => 'Gilgit - Hunza Route',
            'start_location' => 'Gilgit',
            'end_location'   => 'Hunza',
            'status'         => 'Active',
        ]);

        $stop = Stop::create(['name' => 'Danyor', 'location' => 'Danyor Chowk', 'status' => 'Active']);

        $routeStop = RouteStop::create([
            'route_id'   => $route->id,
            'stop_id'    => $stop->id,
            'stop_order' => 1,
        ]);

        $response = $this->actingAs($this->admin)->put("/admin/routes/{$route->id}/stops/{$routeStop->id}", [
            'stop_order' => 5,
        ]);

        $response->assertRedirect("/admin/routes/{$route->id}");
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('route_stops', [
            'id'         => $routeStop->id,
            'stop_order' => 5,
        ]);
    }

    public function test_admin_can_remove_stop_from_route(): void
    {
        $route = RouteModel::create([
            'name'           => 'Gilgit - Hunza Route',
            'start_location' => 'Gilgit',
            'end_location'   => 'Hunza',
            'status'         => 'Active',
        ]);

        $stop = Stop::create(['name' => 'Nomal', 'location' => 'Nomal Bridge', 'status' => 'Active']);

        $routeStop = RouteStop::create([
            'route_id'   => $route->id,
            'stop_id'    => $stop->id,
            'stop_order' => 2,
        ]);

        $response = $this->actingAs($this->admin)->delete("/admin/routes/{$route->id}/stops/{$routeStop->id}");

        $response->assertRedirect("/admin/routes/{$route->id}");
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('route_stops', ['id' => $routeStop->id]);
        // The stop itself must not be deleted
        $this->assertDatabaseHas('stops', ['id' => $stop->id]);
    }

    public function test_full_gilgit_to_hunza_ordered_stops_sequence(): void
    {
        $route = RouteModel::create([
            'name'           => 'Gilgit to Hunza Express',
            'start_location' => 'Gilgit',
            'end_location'   => 'Hunza',
            'status'         => 'Active',
        ]);

        // Sequence: Danyor -> Nomal -> Nagar -> Aliabad
        $stops = [
            'Danyor'  => Stop::create(['name' => 'Danyor', 'location' => 'Danyor Chowk', 'status' => 'Active']),
            'Nomal'   => Stop::create(['name' => 'Nomal', 'location' => 'Nomal Valley', 'status' => 'Active']),
            'Nagar'   => Stop::create(['name' => 'Nagar', 'location' => 'Nagar Junction', 'status' => 'Active']),
            'Aliabad' => Stop::create(['name' => 'Aliabad', 'location' => 'Aliabad Terminal', 'status' => 'Active']),
        ];

        $order = 1;
        foreach ($stops as $s) {
            RouteStop::create([
                'route_id'   => $route->id,
                'stop_id'    => $s->id,
                'stop_order' => $order++,
            ]);
        }

        $response = $this->actingAs($this->admin)->get("/admin/routes/{$route->id}");
        $response->assertOk();
        $response->assertSee('Danyor');
        $response->assertSee('Nomal');
        $response->assertSee('Nagar');
        $response->assertSee('Aliabad');

        $orderedStops = $route->routeStops()->with('stop')->get()->pluck('stop.name')->toArray();
        $this->assertEquals(['Danyor', 'Nomal', 'Nagar', 'Aliabad'], $orderedStops);
    }
}
