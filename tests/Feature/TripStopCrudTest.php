<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Route as RouteModel;
use App\Models\Stop;
use App\Models\RouteStop;
use App\Models\Trip;
use App\Models\TripStop;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TripStopCrudTest extends TestCase
{
    use RefreshDatabase;

    private User    $admin;
    private User    $driver;
    private Vehicle $vehicle;
    private RouteModel $route;
    private Stop    $stop1;
    private Stop    $stop2;
    private Stop    $stop3;
    private Stop    $foreignStop; // belongs to a DIFFERENT route
    private Trip    $trip;

    protected function setUp(): void
    {
        parent::setUp();

        // ── Admin user ──────────────────────────────────────────────────────────
        $this->admin = User::factory()->create([
            'user_type'     => 1,
            'driver_status' => null,
        ]);

        // ── Approved driver ─────────────────────────────────────────────────────
        $this->driver = User::factory()->create([
            'user_type'     => 2,
            'driver_status' => 'approved',
        ]);

        // ── Vehicle belonging to driver ─────────────────────────────────────────
        $this->vehicle = Vehicle::factory()->create([
            'user_id'        => $this->driver->id,
            'status'         => 'Active',
            'total_seats'    => 30,
        ]);

        // ── Stops ───────────────────────────────────────────────────────────────
        $this->stop1 = Stop::factory()->create(['name' => 'Gilgit',  'status' => 'Active']);
        $this->stop2 = Stop::factory()->create(['name' => 'Nomal',   'status' => 'Active']);
        $this->stop3 = Stop::factory()->create(['name' => 'Hunza',   'status' => 'Active']);

        // ── Route ───────────────────────────────────────────────────────────────
        $this->route = RouteModel::factory()->create([
            'name'           => 'Gilgit–Hunza',
            'start_location' => 'Gilgit',
            'end_location'   => 'Hunza',
            'status'         => 'Active',
        ]);

        // Assign stops to route in order
        RouteStop::create(['route_id' => $this->route->id, 'stop_id' => $this->stop1->id, 'stop_order' => 1]);
        RouteStop::create(['route_id' => $this->route->id, 'stop_id' => $this->stop2->id, 'stop_order' => 2]);
        RouteStop::create(['route_id' => $this->route->id, 'stop_id' => $this->stop3->id, 'stop_order' => 3]);

        // ── Stop on a DIFFERENT route (for foreign stop validation tests) ───────
        $otherRoute      = RouteModel::factory()->create(['name' => 'Other', 'status' => 'Active']);
        $this->foreignStop = Stop::factory()->create(['name' => 'ForeignStop', 'status' => 'Active']);
        RouteStop::create(['route_id' => $otherRoute->id, 'stop_id' => $this->foreignStop->id, 'stop_order' => 1]);

        // ── Trip ────────────────────────────────────────────────────────────────
        $this->trip = Trip::factory()->create([
            'user_id'        => $this->driver->id,
            'vehicle_id'     => $this->vehicle->id,
            'route_id'       => $this->route->id,
            'trip_date'      => '2026-11-01',
            'departure_time' => '08:00:00',
            'available_seats'=> 20,
            'status'         => 'Scheduled',
        ]);
    }

    // ─── 1. Access Control ────────────────────────────────────────────────────

    public function test_guest_cannot_view_trip_stops_page(): void
    {
        $this->get(route('admin.trips.stops.index', $this->trip->id))
            ->assertRedirect('/login');
    }

    public function test_passenger_cannot_view_trip_stops_page(): void
    {
        $passenger = User::factory()->create(['user_type' => 0]);

        $this->actingAs($passenger)
            ->get(route('admin.trips.stops.index', $this->trip->id))
            ->assertForbidden();
    }

    public function test_admin_can_view_trip_stops_page(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.trips.stops.index', $this->trip->id));

        $response->assertOk();
        $response->assertSee('Manage Stops');
        $response->assertSee('Trip #' . $this->trip->id);
        $response->assertSee('Gilgit');
        $response->assertSee('Nomal');
        $response->assertSee('Hunza');
    }

    // ─── 2. Trip Show Page ────────────────────────────────────────────────────

    public function test_trip_show_page_has_manage_stops_button(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.trips.show', $this->trip->id));

        $response->assertOk();
        $response->assertSee('Manage Stops');
        $response->assertSee(route('admin.trips.stops.index', $this->trip->id));
    }

    public function test_trip_show_page_displays_stop_timings_when_configured(): void
    {
        TripStop::create([
            'trip_id'       => $this->trip->id,
            'stop_id'       => $this->stop1->id,
            'stop_order'    => 1,
            'expected_time' => '08:00:00',
        ]);
        TripStop::create([
            'trip_id'       => $this->trip->id,
            'stop_id'       => $this->stop2->id,
            'stop_order'    => 2,
            'expected_time' => '09:30:00',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.trips.show', $this->trip->id));

        $response->assertOk();
        $response->assertSee('Scheduled Stop Timings');
        $response->assertSee('Gilgit');
        $response->assertSee('Nomal');
        $response->assertSee('08:00 AM');
        $response->assertSee('09:30 AM');
    }

    public function test_trip_show_page_shows_empty_state_when_no_stops(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.trips.show', $this->trip->id));

        $response->assertOk();
        $response->assertSee('No stop timings configured yet');
    }

    // ─── 3. Add Stop (Store) ──────────────────────────────────────────────────

    public function test_admin_can_add_a_stop_to_trip(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.trips.stops.store', $this->trip->id), [
                'stop_id'       => $this->stop1->id,
                'expected_time' => '08:30',
            ]);

        $response->assertRedirect(route('admin.trips.stops.index', $this->trip->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('trip_stops', [
            'trip_id'    => $this->trip->id,
            'stop_id'    => $this->stop1->id,
            'stop_order' => 1, // inherited from route_stops
        ]);
    }

    public function test_trip_stop_inherits_stop_order_from_route_stops(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.trips.stops.store', $this->trip->id), [
                'stop_id'       => $this->stop3->id,
                'expected_time' => '11:00',
            ]);

        $tripStop = TripStop::where('trip_id', $this->trip->id)
            ->where('stop_id', $this->stop3->id)
            ->first();

        $this->assertNotNull($tripStop);
        $this->assertEquals(3, $tripStop->stop_order); // stop3 is order 3 in route
    }

    public function test_admin_cannot_add_duplicate_stop_to_trip(): void
    {
        // Add stop once
        TripStop::create([
            'trip_id'       => $this->trip->id,
            'stop_id'       => $this->stop1->id,
            'stop_order'    => 1,
            'expected_time' => '08:00:00',
        ]);

        // Try to add same stop again
        $response = $this->actingAs($this->admin)
            ->post(route('admin.trips.stops.store', $this->trip->id), [
                'stop_id'       => $this->stop1->id,
                'expected_time' => '09:00',
            ]);

        $response->assertSessionHasErrors('stop_id');
        $this->assertDatabaseCount('trip_stops', 1); // Still only one
    }

    public function test_admin_cannot_add_stop_from_different_route(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.trips.stops.store', $this->trip->id), [
                'stop_id'       => $this->foreignStop->id,
                'expected_time' => '10:00',
            ]);

        $response->assertSessionHasErrors('stop_id');
        $this->assertDatabaseCount('trip_stops', 0);
    }

    public function test_expected_time_is_required_when_adding_stop(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.trips.stops.store', $this->trip->id), [
                'stop_id'       => $this->stop1->id,
                'expected_time' => '',
            ]);

        $response->assertSessionHasErrors('expected_time');
        $this->assertDatabaseCount('trip_stops', 0);
    }

    public function test_stop_id_is_required_when_adding_stop(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.trips.stops.store', $this->trip->id), [
                'stop_id'       => '',
                'expected_time' => '08:00',
            ]);

        $response->assertSessionHasErrors('stop_id');
    }

    // ─── 4. Update Stop ───────────────────────────────────────────────────────

    public function test_admin_can_update_expected_time_and_order_of_trip_stop(): void
    {
        $tripStop = TripStop::create([
            'trip_id'       => $this->trip->id,
            'stop_id'       => $this->stop2->id,
            'stop_order'    => 2,
            'expected_time' => '09:00:00',
        ]);

        $response = $this->actingAs($this->admin)
            ->put(route('admin.trips.stops.update', [$this->trip->id, $tripStop->id]), [
                'expected_time' => '10:30',
                'stop_order'    => 2,
            ]);

        $response->assertRedirect(route('admin.trips.stops.index', $this->trip->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('trip_stops', [
            'id'            => $tripStop->id,
            'expected_time' => '10:30:00',
            'stop_order'    => 2,
        ]);
    }

    public function test_update_expected_time_is_required(): void
    {
        $tripStop = TripStop::create([
            'trip_id'       => $this->trip->id,
            'stop_id'       => $this->stop1->id,
            'stop_order'    => 1,
            'expected_time' => '08:00:00',
        ]);

        $response = $this->actingAs($this->admin)
            ->put(route('admin.trips.stops.update', [$this->trip->id, $tripStop->id]), [
                'expected_time' => '',
                'stop_order'    => 1,
            ]);

        $response->assertSessionHasErrors('expected_time');
    }

    // ─── 5. Delete Stop ───────────────────────────────────────────────────────

    public function test_admin_can_remove_a_stop_from_trip(): void
    {
        $tripStop = TripStop::create([
            'trip_id'       => $this->trip->id,
            'stop_id'       => $this->stop1->id,
            'stop_order'    => 1,
            'expected_time' => '08:00:00',
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.trips.stops.destroy', [$this->trip->id, $tripStop->id]));

        $response->assertRedirect(route('admin.trips.stops.index', $this->trip->id));
        $this->assertDatabaseMissing('trip_stops', ['id' => $tripStop->id]);
    }

    public function test_deleting_trip_stop_from_wrong_trip_returns_404(): void
    {
        $otherTrip = Trip::factory()->create([
            'user_id'    => $this->driver->id,
            'vehicle_id' => $this->vehicle->id,
            'route_id'   => $this->route->id,
            'trip_date'  => '2026-12-01',
            'departure_time' => '09:00:00',
            'available_seats' => 10,
            'status'     => 'Scheduled',
        ]);

        $tripStop = TripStop::create([
            'trip_id'       => $this->trip->id,
            'stop_id'       => $this->stop1->id,
            'stop_order'    => 1,
            'expected_time' => '08:00:00',
        ]);

        // Try to delete using wrong trip ID
        $response = $this->actingAs($this->admin)
            ->delete(route('admin.trips.stops.destroy', [$otherTrip->id, $tripStop->id]));

        $response->assertNotFound();
    }

    // ─── 6. Existing Trips CRUD Still Works ───────────────────────────────────

    public function test_existing_trips_index_still_works(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.trips.index'));

        $response->assertOk();
        $response->assertSee('Trips Management');
    }

    public function test_trip_stops_data_is_preserved_when_trip_is_updated(): void
    {
        TripStop::create([
            'trip_id'       => $this->trip->id,
            'stop_id'       => $this->stop1->id,
            'stop_order'    => 1,
            'expected_time' => '08:00:00',
        ]);

        // Update trip details (not stops)
        $this->actingAs($this->admin)
            ->put(route('admin.trips.update', $this->trip->id), [
                'user_id'         => $this->driver->id,
                'vehicle_id'      => $this->vehicle->id,
                'route_id'        => $this->route->id,
                'trip_date'       => '2026-11-15',
                'departure_time'  => '09:00',
                'available_seats' => 25,
                'status'          => 'Scheduled',
                'fare'            => null,
                'pickup_point'    => null,
            ]);

        // Trip stop should still exist
        $this->assertDatabaseHas('trip_stops', [
            'trip_id' => $this->trip->id,
            'stop_id' => $this->stop1->id,
        ]);
    }

    public function test_trip_stops_cascade_delete_when_trip_is_deleted(): void
    {
        TripStop::create([
            'trip_id'       => $this->trip->id,
            'stop_id'       => $this->stop1->id,
            'stop_order'    => 1,
            'expected_time' => '08:00:00',
        ]);

        $tripId = $this->trip->id;
        $this->actingAs($this->admin)
            ->delete(route('admin.trips.destroy', $tripId));

        $this->assertDatabaseMissing('trip_stops', ['trip_id' => $tripId]);
    }

    // ─── 7. Available stops on manage page ───────────────────────────────────

    public function test_stops_page_shows_only_unassigned_route_stops_in_add_dropdown(): void
    {
        // Assign stop1 already
        TripStop::create([
            'trip_id'       => $this->trip->id,
            'stop_id'       => $this->stop1->id,
            'stop_order'    => 1,
            'expected_time' => '08:00:00',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.trips.stops.index', $this->trip->id));

        $response->assertOk();
        // stop2 and stop3 should be available
        $response->assertSee('Nomal');
        $response->assertSee('Hunza');
        // foreignStop should NOT appear
        $response->assertDontSee('ForeignStop');
    }

    public function test_all_route_stops_shown_in_reference_panel(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.trips.stops.index', $this->trip->id));

        $response->assertOk();
        $response->assertSee('Full Route Stop Order Reference');
        $response->assertSee('Gilgit');
        $response->assertSee('Nomal');
        $response->assertSee('Hunza');
    }
}
