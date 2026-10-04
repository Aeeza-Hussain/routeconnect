<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Route;
use App\Models\Stop;
use App\Models\Trip;
use App\Models\TripStop;
use App\Models\Booking;
use App\Models\TripMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class DriverDashboardCompleteTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $passenger;
    private User $pendingDriver;
    private User $rejectedDriver;
    private User $approvedDriver;
    private User $otherApprovedDriver;
    private Vehicle $driverVehicle;
    private Route $route;
    private Stop $stopA;
    private Stop $stopB;
    private Stop $stopWaypoint;
    private Trip $driverTrip1;
    private Trip $driverTrip2;
    private Trip $otherDriverTrip;
    private Booking $driverBooking;
    private Booking $otherBooking;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Admin (user_type = 1)
        $this->admin = User::factory()->create([
            'name'          => 'Admin Boss',
            'email'         => 'admin@routeconnect.pk',
            'user_type'     => 1,
            'driver_status' => null,
        ]);

        // 2. Passenger (user_type = 0)
        $this->passenger = User::factory()->create([
            'name'          => 'Passenger Alice',
            'email'         => 'alice@passenger.pk',
            'phone'         => '0300-1111111',
            'user_type'     => 0,
            'driver_status' => null,
        ]);

        // 3. Pending Driver (user_type = 2, driver_status = 'pending')
        $this->pendingDriver = User::factory()->create([
            'name'          => 'Pending Bob',
            'email'         => 'bob@pending.pk',
            'user_type'     => 2,
            'driver_status' => 'pending',
        ]);

        // 4. Rejected Driver (user_type = 2, driver_status = 'rejected')
        $this->rejectedDriver = User::factory()->create([
            'name'          => 'Rejected Charlie',
            'email'         => 'charlie@rejected.pk',
            'user_type'     => 2,
            'driver_status' => 'rejected',
        ]);

        // 5. Approved Driver (user_type = 2, driver_status = 'approved')
        $this->approvedDriver = User::factory()->create([
            'name'          => 'Approved Dan',
            'email'         => 'dan@driver.pk',
            'password'      => Hash::make('secret123'),
            'phone'         => '0300-2222222',
            'cnic'          => '71501-1111111-1',
            'license_no'    => 'DL-GILGIT-101',
            'bio'           => '10 years navigating KKH highways.',
            'user_type'     => 2,
            'driver_status' => 'approved',
        ]);

        // 6. Other Approved Driver (for cross-driver security tests)
        $this->otherApprovedDriver = User::factory()->create([
            'name'          => 'Other Driver Frank',
            'email'         => 'frank@driver.pk',
            'user_type'     => 2,
            'driver_status' => 'approved',
        ]);

        // Stops
        $this->stopA = Stop::create(['name' => 'Gilgit Terminal', 'location' => 'Gilgit City', 'status' => 'Active']);
        $this->stopWaypoint = Stop::create(['name' => 'Nagar Stop', 'location' => 'Nagar Valley', 'status' => 'Active']);
        $this->stopB = Stop::create(['name' => 'Hunza Terminal', 'location' => 'Karimabad', 'status' => 'Active']);

        // Route
        $this->route = Route::create([
            'name'           => 'Gilgit - Hunza Express',
            'start_location' => 'Gilgit Terminal',
            'end_location'   => 'Hunza Terminal',
            'status'         => 'Active',
            'start_stop_id'  => $this->stopA->id,
            'end_stop_id'    => $this->stopB->id,
        ]);

        // Vehicle assigned to $this->approvedDriver
        $this->driverVehicle = Vehicle::create([
            'user_id'         => $this->approvedDriver->id,
            'type'            => 'HiAce Van',
            'registration_no' => 'GLT-8899',
            'model'           => 'Toyota Grand Cabin 2022',
            'total_seats'     => 14,
            'status'          => 'Active',
        ]);

        // Vehicle for other driver
        $otherVehicle = Vehicle::create([
            'user_id'         => $this->otherApprovedDriver->id,
            'type'            => 'Coaster Bus',
            'registration_no' => 'ISB-4411',
            'model'           => 'Toyota Coaster 2023',
            'total_seats'     => 28,
            'status'          => 'Active',
        ]);

        // Trip 1 for $this->approvedDriver (scheduled)
        $this->driverTrip1 = Trip::create([
            'user_id'         => $this->approvedDriver->id,
            'vehicle_id'      => $this->driverVehicle->id,
            'route_id'        => $this->route->id,
            'trip_date'       => now()->addDays(2)->format('Y-m-d'),
            'departure_time'  => '08:00:00',
            'available_seats' => 12,
            'fare'            => 1500.00,
            'pickup_point'    => 'Bay #3, Gilgit General Stand',
            'status'          => 'scheduled',
        ]);

        // Add stops to Trip 1
        TripStop::create([
            'trip_id'       => $this->driverTrip1->id,
            'stop_id'       => $this->stopA->id,
            'stop_order'    => 1,
            'expected_time' => '08:00:00',
        ]);
        TripStop::create([
            'trip_id'       => $this->driverTrip1->id,
            'stop_id'       => $this->stopWaypoint->id,
            'stop_order'    => 2,
            'expected_time' => '09:30:00',
        ]);
        TripStop::create([
            'trip_id'       => $this->driverTrip1->id,
            'stop_id'       => $this->stopB->id,
            'stop_order'    => 3,
            'expected_time' => '11:00:00',
        ]);

        // Trip 2 for $this->approvedDriver (completed)
        $this->driverTrip2 = Trip::create([
            'user_id'         => $this->approvedDriver->id,
            'vehicle_id'      => $this->driverVehicle->id,
            'route_id'        => $this->route->id,
            'trip_date'       => now()->subDays(1)->format('Y-m-d'),
            'departure_time'  => '14:00:00',
            'available_seats' => 0,
            'fare'            => 1500.00,
            'pickup_point'    => 'Bay #3',
            'status'          => 'completed',
        ]);

        // Trip for other driver
        $this->otherDriverTrip = Trip::create([
            'user_id'         => $this->otherApprovedDriver->id,
            'vehicle_id'      => $otherVehicle->id,
            'route_id'        => $this->route->id,
            'trip_date'       => now()->addDays(3)->format('Y-m-d'),
            'departure_time'  => '10:00:00',
            'available_seats' => 25,
            'fare'            => 2000.00,
            'pickup_point'    => 'Islamabad Terminal',
            'status'          => 'scheduled',
        ]);

        // Booking on $this->approvedDriver's trip
        $this->driverBooking = Booking::create([
            'user_id'           => $this->passenger->id,
            'trip_id'           => $this->driverTrip1->id,
            'from_stop_id'      => $this->stopA->id,
            'to_stop_id'        => $this->stopB->id,
            'seats'             => 2,
            'seat_numbers'      => '1, 2',
            'total_fare'        => 3000.00,
            'booking_reference' => 'BK-DAN-001',
            'status'            => 'confirmed',
        ]);

        // Booking on other driver's trip
        $this->otherBooking = Booking::create([
            'user_id'           => $this->passenger->id,
            'trip_id'           => $this->otherDriverTrip->id,
            'from_stop_id'      => $this->stopA->id,
            'to_stop_id'        => $this->stopB->id,
            'seats'             => 3,
            'seat_numbers'      => '5, 6, 7',
            'total_fare'        => 6000.00,
            'booking_reference' => 'BK-FRANK-002',
            'status'            => 'confirmed',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 1. SECURITY & ACCESS CONTROL
    // ─────────────────────────────────────────────────────────────────────────

    public function test_approved_driver_can_access_driver_dashboard(): void
    {
        $response = $this->actingAs($this->approvedDriver)->get('/driver/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Welcome back, Approved Dan');
    }

    public function test_pending_driver_cannot_access_driver_dashboard(): void
    {
        $response = $this->actingAs($this->pendingDriver)->get('/driver/dashboard');
        $response->assertRedirect(route('driver.pending'));
    }

    public function test_rejected_driver_cannot_access_driver_dashboard(): void
    {
        $response = $this->actingAs($this->rejectedDriver)->get('/driver/dashboard');
        $response->assertRedirect(route('driver.pending'));
    }

    public function test_passenger_cannot_access_driver_dashboard(): void
    {
        $response = $this->actingAs($this->passenger)->get('/driver/dashboard');
        $response->assertStatus(403);
    }

    public function test_admin_cannot_access_driver_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get('/driver/dashboard');
        $response->assertStatus(403);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/driver/dashboard');
        $response->assertRedirect(route('login'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 2. DASHBOARD COUNTS & DATA INTEGRITY
    // ─────────────────────────────────────────────────────────────────────────

    public function test_driver_dashboard_displays_real_database_counts(): void
    {
        $response = $this->actingAs($this->approvedDriver)->get('/driver/dashboard');
        $response->assertStatus(200);

        // Required 5 KPI headers
        $response->assertSee('Total Trips');
        $response->assertSee('Scheduled Trips');
        $response->assertSee('Completed Trips');
        $response->assertSee('Total Bookings');
        $response->assertSee('Available Vehicle Seats');

        // Total Trips: 2 (Trip1 and Trip2 belong to Dan)
        $response->assertSee('2');

        // Total Bookings: 1 (only driverBooking belongs to Dan)
        $response->assertSee('1');

        // Available Vehicle Seats: 14 (driverVehicle total_seats)
        $response->assertSee('14');

        // Vehicle registration
        $response->assertSee('GLT-8899');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 3. SIDEBAR NAVIGATION
    // ─────────────────────────────────────────────────────────────────────────

    public function test_driver_sidebar_contains_all_required_links(): void
    {
        $response = $this->actingAs($this->approvedDriver)->get('/driver/dashboard');
        $response->assertStatus(200);

        $response->assertSee(route('driver.dashboard'));
        $response->assertSee(route('driver.profile'));
        $response->assertSee(route('driver.vehicle'));
        $response->assertSee(route('driver.trips'));
        $response->assertSee(route('driver.bookings'));
        $response->assertSee(route('driver.messages'));
        $response->assertSee(route('driver.settings'));
        $response->assertSee(route('logout'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 4. MY VEHICLE
    // ─────────────────────────────────────────────────────────────────────────

    public function test_driver_can_view_assigned_vehicle_details(): void
    {
        $response = $this->actingAs($this->approvedDriver)->get('/driver/vehicle');
        $response->assertStatus(200);

        // Required fields: Vehicle Number, Type, Model, Total Seats, Status
        $response->assertSee('GLT-8899');
        $response->assertSee('HiAce Van');
        $response->assertSee('Toyota Grand Cabin 2022');
        $response->assertSee('14 Seats');
        $response->assertSee('Active');

        // Other driver's vehicle must NOT be displayed
        $response->assertDontSee('ISB-4411');
        $response->assertDontSee('Coaster Bus');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 5. MY TRIPS & ISOLATION
    // ─────────────────────────────────────────────────────────────────────────

    public function test_driver_can_only_view_their_own_trips_in_trips_index(): void
    {
        $response = $this->actingAs($this->approvedDriver)->get('/driver/trips');
        $response->assertStatus(200);

        // Own trips
        $response->assertSee('GLT-8899');
        $response->assertSee('Gilgit - Hunza Express');

        // Other driver's trip must NOT appear
        $response->assertDontSee('ISB-4411');
        $response->assertDontSee('Islamabad Terminal');
    }

    public function test_driver_can_view_their_own_trip_details_with_stops_and_timings(): void
    {
        $response = $this->actingAs($this->approvedDriver)->get("/driver/trips/{$this->driverTrip1->id}");
        $response->assertStatus(200);

        $response->assertSee('Gilgit Terminal');
        $response->assertSee('Nagar Stop');
        $response->assertSee('Hunza Terminal');
        $response->assertSee('08:00 AM');
        $response->assertSee('09:30 AM');
        $response->assertSee('11:00 AM');

        // Booked passenger reference
        $response->assertSee('BK-DAN-001');
        $response->assertSee('Passenger Alice');
    }

    public function test_driver_cannot_view_another_drivers_trip_details(): void
    {
        $response = $this->actingAs($this->approvedDriver)->get("/driver/trips/{$this->otherDriverTrip->id}");
        $response->assertStatus(403);
    }

    public function test_driver_can_edit_their_own_trip(): void
    {
        // 1. View edit form
        $response = $this->actingAs($this->approvedDriver)->get("/driver/trips/{$this->driverTrip1->id}/edit");
        $response->assertStatus(200);
        $response->assertSee('Edit Trip #' . $this->driverTrip1->id);

        // 2. Submit update
        $updateResponse = $this->actingAs($this->approvedDriver)->put("/driver/trips/{$this->driverTrip1->id}", [
            'status'          => 'boarding',
            'departure_time'  => '08:30:00',
            'available_seats' => 8,
            'pickup_point'    => 'Bay #5 Updated Terminal',
        ]);

        $updateResponse->assertRedirect(route('driver.trips.show', $this->driverTrip1->id));
        $updateResponse->assertSessionHas('success');

        $this->assertDatabaseHas('trips', [
            'id'              => $this->driverTrip1->id,
            'status'          => 'boarding',
            'departure_time'  => '08:30:00',
            'available_seats' => 8,
            'pickup_point'    => 'Bay #5 Updated Terminal',
        ]);
    }

    public function test_driver_cannot_edit_another_drivers_trip(): void
    {
        $response = $this->actingAs($this->approvedDriver)->get("/driver/trips/{$this->otherDriverTrip->id}/edit");
        $response->assertStatus(403);

        $updateResponse = $this->actingAs($this->approvedDriver)->put("/driver/trips/{$this->otherDriverTrip->id}", [
            'status'          => 'cancelled',
            'departure_time'  => '12:00:00',
            'available_seats' => 0,
        ]);
        $updateResponse->assertStatus(403);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 6. BOOKINGS & ISOLATION
    // ─────────────────────────────────────────────────────────────────────────

    public function test_driver_can_view_bookings_for_their_own_trips(): void
    {
        $response = $this->actingAs($this->approvedDriver)->get('/driver/bookings');
        $response->assertStatus(200);

        // Own trip booking
        $response->assertSee('BK-DAN-001');
        $response->assertSee('Passenger Alice');
        $response->assertSee('2 seats');
        $response->assertSee('confirmed');

        // Other driver's trip booking must NOT appear
        $response->assertDontSee('BK-FRANK-002');
    }

    public function test_driver_can_view_single_booking_details(): void
    {
        $response = $this->actingAs($this->approvedDriver)->get("/driver/bookings/{$this->driverBooking->id}");
        $response->assertStatus(200);
        $response->assertSee('BK-DAN-001');
        $response->assertSee('Passenger Alice');
    }

    public function test_driver_cannot_view_booking_for_another_drivers_trip(): void
    {
        $response = $this->actingAs($this->approvedDriver)->get("/driver/bookings/{$this->otherBooking->id}");
        $response->assertStatus(403);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 7. TRIP MESSAGES
    // ─────────────────────────────────────────────────────────────────────────

    public function test_driver_can_post_message_for_their_own_trip(): void
    {
        $response = $this->actingAs($this->approvedDriver)->post('/driver/messages', [
            'trip_id' => $this->driverTrip1->id,
            'message' => 'Leaving Gilgit at 8:00 AM.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('trip_messages', [
            'trip_id' => $this->driverTrip1->id,
            'user_id' => $this->approvedDriver->id,
            'message' => 'Leaving Gilgit at 8:00 AM.',
        ]);
    }

    public function test_driver_cannot_post_message_for_another_drivers_trip(): void
    {
        $response = $this->actingAs($this->approvedDriver)->post('/driver/messages', [
            'trip_id' => $this->otherDriverTrip->id,
            'message' => 'Malicious message on another trip.',
        ]);

        $response->assertStatus(403);

        $this->assertDatabaseMissing('trip_messages', [
            'message' => 'Malicious message on another trip.',
        ]);
    }

    public function test_driver_can_delete_their_own_message(): void
    {
        $msg = TripMessage::create([
            'trip_id' => $this->driverTrip1->id,
            'user_id' => $this->approvedDriver->id,
            'message' => 'Trip delayed by 15 minutes.',
        ]);

        $response = $this->actingAs($this->approvedDriver)->delete("/driver/messages/{$msg->id}");
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('trip_messages', ['id' => $msg->id]);
    }

    public function test_driver_cannot_delete_another_drivers_message(): void
    {
        $msg = TripMessage::create([
            'trip_id' => $this->otherDriverTrip->id,
            'user_id' => $this->otherApprovedDriver->id,
            'message' => 'Other driver update.',
        ]);

        $response = $this->actingAs($this->approvedDriver)->delete("/driver/messages/{$msg->id}");
        $response->assertStatus(403);

        $this->assertDatabaseHas('trip_messages', ['id' => $msg->id]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 8. PROFILE & SETTINGS
    // ─────────────────────────────────────────────────────────────────────────

    public function test_driver_can_view_profile_page(): void
    {
        $response = $this->actingAs($this->approvedDriver)->get('/driver/profile');
        $response->assertStatus(200);
        $response->assertSee('Approved Dan');
        $response->assertSee('dan@driver.pk');
        $response->assertSee('DL-GILGIT-101');
    }

    public function test_driver_can_update_profile_information(): void
    {
        $response = $this->actingAs($this->approvedDriver)->put('/driver/profile', [
            'name'       => 'Approved Dan Updated',
            'phone'      => '0311-9999999',
            'cnic'       => '71501-9999999-9',
            'license_no' => 'DL-HUNZA-202',
            'bio'        => 'Updated bio details.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id'         => $this->approvedDriver->id,
            'name'       => 'Approved Dan Updated',
            'phone'      => '0311-9999999',
            'cnic'       => '71501-9999999-9',
            'license_no' => 'DL-HUNZA-202',
            'bio'        => 'Updated bio details.',
        ]);
    }

    public function test_driver_can_view_settings_and_update_password(): void
    {
        $viewResponse = $this->actingAs($this->approvedDriver)->get('/driver/settings');
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Driver Account Settings');

        $updateResponse = $this->actingAs($this->approvedDriver)->put('/driver/settings/password', [
            'current_password'      => 'secret123',
            'new_password'          => 'new-strong-password',
            'new_password_confirmation' => 'new-strong-password',
        ]);

        $updateResponse->assertRedirect();
        $updateResponse->assertSessionHas('success');

        $this->approvedDriver->refresh();
        $this->assertTrue(Hash::check('new-strong-password', $this->approvedDriver->password));
    }
}
