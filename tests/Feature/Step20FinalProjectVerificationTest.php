<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Route;
use App\Models\Stop;
use App\Models\RouteStop;
use App\Models\Trip;
use App\Models\TripStop;
use App\Models\Booking;
use App\Models\TripMessage;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class Step20FinalProjectVerificationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $passenger;
    private User $approvedDriver;
    private User $pendingDriver;
    private User $rejectedDriver;
    private Vehicle $vehicle;
    private Route $route;
    private Stop $stopGilgit;
    private Stop $stopNagar;
    private Stop $stopHunza;
    private Trip $trip;

    protected function setUp(): void
    {
        parent::setUp();

        // Admin User
        $this->admin = User::create([
            'name'          => 'Super Admin',
            'email'         => 'admin@routeconnect.com',
            'password'      => bcrypt('password123'),
            'user_type'     => 1,
            'driver_status' => null,
        ]);

        // Passenger User
        $this->passenger = User::create([
            'name'          => 'Ali Passenger',
            'email'         => 'ali@passenger.com',
            'password'      => bcrypt('password123'),
            'user_type'     => 0,
            'driver_status' => null,
        ]);

        // Approved Driver
        $this->approvedDriver = User::create([
            'name'          => 'Dawood Driver',
            'email'         => 'dawood@driver.com',
            'password'      => bcrypt('password123'),
            'user_type'     => 2,
            'driver_status' => 'approved',
        ]);

        // Pending Driver
        $this->pendingDriver = User::create([
            'name'          => 'Pending Driver',
            'email'         => 'pending@driver.com',
            'password'      => bcrypt('password123'),
            'user_type'     => 2,
            'driver_status' => 'pending',
        ]);

        // Rejected Driver
        $this->rejectedDriver = User::create([
            'name'          => 'Rejected Driver',
            'email'         => 'rejected@driver.com',
            'password'      => bcrypt('password123'),
            'user_type'     => 2,
            'driver_status' => 'rejected',
        ]);

        // Vehicle assigned to approved driver
        $this->vehicle = Vehicle::create([
            'user_id'         => $this->approvedDriver->id,
            'registration_no' => 'GLT-9988',
            'model'           => 'Toyota Grand Cabin',
            'type'            => 'Van',
            'total_seats'     => 14,
            'status'          => 'Active',
        ]);

        // Stops
        $this->stopGilgit = Stop::create(['name' => 'Gilgit Terminal', 'location' => 'Gilgit City']);
        $this->stopNagar  = Stop::create(['name' => 'Nagar Junction', 'location' => 'Nagar Valley']);
        $this->stopHunza  = Stop::create(['name' => 'Hunza Aliabad', 'location' => 'Hunza Valley']);

        // Route: Gilgit -> Hunza
        $this->route = Route::create([
            'name'        => 'Gilgit - Hunza Express',
            'origin'      => 'Gilgit Terminal',
            'destination' => 'Hunza Aliabad',
            'distance_km' => 100,
            'status'      => 'Active',
        ]);

        // Route Stops (Gilgit=1, Nagar=2, Hunza=3)
        RouteStop::create(['route_id' => $this->route->id, 'stop_id' => $this->stopGilgit->id, 'stop_order' => 1]);
        RouteStop::create(['route_id' => $this->route->id, 'stop_id' => $this->stopNagar->id, 'stop_order' => 2]);
        RouteStop::create(['route_id' => $this->route->id, 'stop_id' => $this->stopHunza->id, 'stop_order' => 3]);

        // Trip
        $this->trip = Trip::create([
            'user_id'         => $this->approvedDriver->id,
            'vehicle_id'      => $this->vehicle->id,
            'route_id'        => $this->route->id,
            'trip_date'       => now()->addDays(2)->format('Y-m-d'),
            'departure_time'  => '08:30:00',
            'available_seats' => 14,
            'fare'            => 1200.00,
            'pickup_point'    => 'Gilgit Terminal Platform 2',
            'status'          => 'Scheduled',
        ]);

        // Trip Stops
        TripStop::create([
            'trip_id'       => $this->trip->id,
            'stop_id'       => $this->stopGilgit->id,
            'stop_order'    => 1,
            'expected_time' => '08:30:00',
        ]);
        TripStop::create([
            'trip_id'       => $this->trip->id,
            'stop_id'       => $this->stopNagar->id,
            'stop_order'    => 2,
            'expected_time' => '10:00:00',
        ]);
        TripStop::create([
            'trip_id'       => $this->trip->id,
            'stop_id'       => $this->stopHunza->id,
            'stop_order'    => 3,
            'expected_time' => '11:30:00',
        ]);
    }

    /** 1. Public Pages & UI */
    public function test_public_pages_load_cleanly_with_routeconnect_branding(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Route');
        $response->assertSee('Connect');

        $searchResponse = $this->get('/trips');
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Find Scheduled Trips');
    }

    /** 2. Authentication & Role Segregation */
    public function test_auth_flows_and_role_access_control(): void
    {
        // Passenger registration
        $regResponse = $this->post('/register', [
            'name'                  => 'New Passenger',
            'email'                 => 'newpass@example.com',
            'phone'                 => '03112223344',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $regResponse->assertRedirect('/');
        $this->assertDatabaseHas('users', ['email' => 'newpass@example.com', 'user_type' => 0]);

        // Driver registration creates pending driver
        $driverReg = $this->post('/driver/register', [
            'name'                  => 'New Candidate',
            'email'                 => 'newcand@driver.com',
            'phone'                 => '03225556677',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $driverReg->assertRedirect(route('driver.pending'));
        $this->assertDatabaseHas('users', ['email' => 'newcand@driver.com', 'user_type' => 2, 'driver_status' => 'pending']);

        // Passenger cannot access Admin or Driver Dashboard
        $this->actingAs($this->passenger)->get('/admin/dashboard')->assertStatus(403);
        $this->actingAs($this->passenger)->get('/driver/dashboard')->assertStatus(403);

        // Pending and Rejected drivers cannot access Driver Dashboard
        $this->actingAs($this->pendingDriver)->get('/driver/dashboard')->assertRedirect(route('driver.pending'));
        $this->actingAs($this->rejectedDriver)->get('/driver/dashboard')->assertRedirect(route('driver.pending'));

        // Approved driver can access Driver Dashboard
        $this->actingAs($this->approvedDriver)->get('/driver/dashboard')->assertStatus(200);

        // Admin can access Admin Dashboard
        $this->actingAs($this->admin)->get('/admin/dashboard')->assertStatus(200);
    }

    /** 3. Admin Driver Approval / Rejection */
    public function test_admin_driver_approval_and_rejection(): void
    {
        $candidate = User::create([
            'name'          => 'Test Applicant',
            'email'         => 'applicant@test.com',
            'password'      => bcrypt('password123'),
            'user_type'     => 2,
            'driver_status' => 'pending',
        ]);

        // Admin approves candidate
        $approveRes = $this->actingAs($this->admin)->post("/admin/drivers/{$candidate->id}/approve");
        $approveRes->assertSessionHas('success');
        $this->assertEquals('approved', $candidate->fresh()->driver_status);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $candidate->id,
            'message' => 'Congratulations! Your driver application has been approved. You now have full access to the Driver Dashboard.',
        ]);

        // Admin rejects candidate
        $rejectRes = $this->actingAs($this->admin)->post("/admin/drivers/{$candidate->id}/reject");
        $rejectRes->assertSessionHas('success');
        $this->assertEquals('rejected', $candidate->fresh()->driver_status);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $candidate->id,
            'message' => 'Your driver application has been reviewed and rejected. Please review your credentials or contact support.',
        ]);
    }

    /** 4. Driver Dashboard and Messages Flow */
    public function test_driver_dashboard_and_trip_messages(): void
    {
        // Driver dashboard KPI and view
        $dashRes = $this->actingAs($this->approvedDriver)->get('/driver/dashboard');
        $dashRes->assertStatus(200);
        $dashRes->assertSee('Toyota Grand Cabin');
        $dashRes->assertSee('GLT-9988');

        // Driver posts message for trip
        $msgRes = $this->actingAs($this->approvedDriver)->post('/driver/messages', [
            'trip_id' => $this->trip->id,
            'message' => 'Departure delayed by 15 mins due to road clearing.',
        ]);
        $msgRes->assertSessionHas('success');
        $this->assertDatabaseHas('trip_messages', [
            'trip_id' => $this->trip->id,
            'user_id' => $this->approvedDriver->id,
            'message' => 'Departure delayed by 15 mins due to road clearing.',
        ]);

        // Drivers cannot post messages for other drivers' trips
        $otherDriver = User::create([
            'name'          => 'Other Driver',
            'email'         => 'other@driver.com',
            'password'      => bcrypt('password123'),
            'user_type'     => 2,
            'driver_status' => 'approved',
        ]);
        $unauthMsg = $this->actingAs($otherDriver)->post('/driver/messages', [
            'trip_id' => $this->trip->id,
            'message' => 'Unauthorized driver message',
        ]);
        $unauthMsg->assertStatus(403);
    }

    /** 5. Public Search & Details Flow */
    public function test_public_trip_search_and_trip_details(): void
    {
        // Trip search where Nagar comes before Hunza
        $searchRes = $this->get('/trips?from=Nagar&to=Hunza&date=' . $this->trip->trip_date->format('Y-m-d'));
        $searchRes->assertStatus(200);
        $searchRes->assertSee('Gilgit - Hunza Express');
        $searchRes->assertSee('Dawood Driver');

        // Trip details page shows driver messages and ordered stops
        $detailsRes = $this->get('/trips/' . $this->trip->id);
        $detailsRes->assertStatus(200);
        $detailsRes->assertSee('Gilgit Terminal');
        $detailsRes->assertSee('Nagar Junction');
        $detailsRes->assertSee('Hunza Aliabad');
        $detailsRes->assertSee('Book Seats');
    }

    /** 6. Seat Booking, My Bookings, Cancellation and Seat Restoration Flow */
    public function test_complete_seat_booking_and_cancellation_flow(): void
    {
        // Passenger books 3 seats
        $bookRes = $this->actingAs($this->passenger)->post('/booking/' . $this->trip->id, [
            'seats' => 3,
        ]);

        $this->assertEquals(11, $this->trip->fresh()->available_seats);
        $this->assertDatabaseHas('bookings', [
            'user_id'        => $this->passenger->id,
            'trip_id'        => $this->trip->id,
            'seats'          => 3,
            'booking_status' => 'confirmed',
        ]);

        $booking = Booking::where('user_id', $this->passenger->id)->first();
        $this->assertNotNull($booking);
        $this->assertNotEmpty($booking->booking_reference);

        // Confirmation page accessible to owner
        $confRes = $this->actingAs($this->passenger)->get('/booking/confirmation/' . $booking->id);
        $confRes->assertStatus(200);
        $confRes->assertSee($booking->booking_reference);

        // My Bookings list
        $myBookingsRes = $this->actingAs($this->passenger)->get('/my-bookings');
        $myBookingsRes->assertStatus(200);
        $myBookingsRes->assertSee($booking->booking_reference);

        // Booking details page
        $detailsRes = $this->actingAs($this->passenger)->get('/my-bookings/' . $booking->id);
        $detailsRes->assertStatus(200);
        $detailsRes->assertSee($booking->booking_reference);

        // Another passenger cannot view or cancel this booking
        $otherPassenger = User::create([
            'name'      => 'Imran Passenger',
            'email'     => 'imran@pass.com',
            'password'  => bcrypt('password123'),
            'user_type' => 0,
        ]);
        $this->actingAs($otherPassenger)->get('/my-bookings/' . $booking->id)->assertStatus(403);
        $this->actingAs($otherPassenger)->post('/my-bookings/' . $booking->id . '/cancel')->assertStatus(403);

        // Owner cancels booking -> seats restored to 14, status set to cancelled
        $cancelRes = $this->actingAs($this->passenger)->post('/my-bookings/' . $booking->id . '/cancel');
        $cancelRes->assertRedirect(route('passenger.bookings.index'));

        $this->assertEquals(14, $this->trip->fresh()->available_seats);
        $this->assertEquals('cancelled', $booking->fresh()->booking_status);

        // Already cancelled booking cannot be cancelled again
        $repeatCancel = $this->actingAs($this->passenger)->post('/my-bookings/' . $booking->id . '/cancel');
        $repeatCancel->assertSessionHas('error');
        $this->assertEquals(14, $this->trip->fresh()->available_seats);
    }

    /** 7. Pessimistic Overbooking and Negative Seats Prevention */
    public function test_overbooking_and_negative_seats_prevented(): void
    {
        // Trip has 14 seats. Trying to book 15 seats fails validation
        $failRes = $this->actingAs($this->passenger)->post('/booking/' . $this->trip->id, [
            'seats' => 15,
        ]);
        $failRes->assertSessionHasErrors('seats');
        $this->assertEquals(14, $this->trip->fresh()->available_seats);

        // Book 14 seats
        $this->actingAs($this->passenger)->post('/booking/' . $this->trip->id, [
            'seats' => 14,
        ]);
        $this->assertEquals(0, $this->trip->fresh()->available_seats);

        // Trying to book 1 more seat on fully booked trip fails
        $fullRes = $this->actingAs($this->passenger)->post('/booking/' . $this->trip->id, [
            'seats' => 1,
        ]);
        $fullRes->assertSessionHasErrors('seats');
        $this->assertEquals(0, $this->trip->fresh()->available_seats);
        $this->assertGreaterThanOrEqual(0, $this->trip->fresh()->available_seats);
    }

    /** 8. Notifications Hub and Mark as Read */
    public function test_notifications_hub_and_mark_as_read(): void
    {
        // Create notifications for passenger
        $notif1 = Notification::create([
            'user_id' => $this->passenger->id,
            'trip_id' => $this->trip->id,
            'message' => 'Your booking #RC-TEST-001 has been confirmed.',
            'is_read' => false,
        ]);
        $notif2 = Notification::create([
            'user_id' => $this->passenger->id,
            'trip_id' => $this->trip->id,
            'message' => 'Trip reminder: Departure tomorrow at 08:30 AM.',
            'is_read' => false,
        ]);

        $hubRes = $this->actingAs($this->passenger)->get('/notifications');
        $hubRes->assertStatus(200);
        $hubRes->assertSee('RC-TEST-001');

        // Mark single notification as read
        $markOneRes = $this->actingAs($this->passenger)->post("/notifications/{$notif1->id}/read");
        $markOneRes->assertSessionHas('success');
        $this->assertTrue((bool)$notif1->fresh()->is_read);
        $this->assertFalse((bool)$notif2->fresh()->is_read);

        // Mark all as read
        $markAllRes = $this->actingAs($this->passenger)->post('/notifications/mark-all-read');
        $markAllRes->assertSessionHas('success');
        $this->assertTrue((bool)$notif2->fresh()->is_read);
    }
}
