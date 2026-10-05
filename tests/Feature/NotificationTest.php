<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Route;
use App\Models\Stop;
use App\Models\Trip;
use App\Models\Booking;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    private User $passenger;
    private User $otherPassenger;
    private User $driver;
    private User $admin;
    private Vehicle $vehicle;
    private Route $route;
    private Trip $trip;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Users
        $this->passenger = User::factory()->create([
            'name'      => 'Amina Passenger',
            'email'     => 'amina@passenger.test',
            'user_type' => 0,
        ]);

        $this->otherPassenger = User::factory()->create([
            'name'      => 'Zainab User',
            'email'     => 'zainab@passenger.test',
            'user_type' => 0,
        ]);

        $this->driver = User::factory()->create([
            'name'          => 'Kareem Driver',
            'email'         => 'kareem@driver.test',
            'user_type'     => 2,
            'driver_status' => 'pending',
        ]);

        $this->admin = User::factory()->create([
            'name'      => 'System Admin',
            'email'     => 'admin@routeconnect.test',
            'user_type' => 1,
        ]);

        // 2. Vehicle & Route
        $this->vehicle = Vehicle::factory()->create([
            'user_id'     => $this->driver->id,
            'total_seats' => 14,
            'status'      => 'Active',
        ]);

        $stopA = Stop::create(['name' => 'Gilgit Terminal', 'location' => 'Gilgit', 'status' => 'Active']);
        $stopB = Stop::create(['name' => 'Hunza Station',   'location' => 'Hunza',  'status' => 'Active']);

        $this->route = Route::create([
            'name'           => 'Hunza Route',
            'start_location' => 'Gilgit Terminal',
            'end_location'   => 'Hunza Station',
            'status'         => 'Active',
            'start_stop_id'  => $stopA->id,
            'end_stop_id'    => $stopB->id,
        ]);

        // 3. Trip
        $this->trip = Trip::create([
            'user_id'         => $this->driver->id,
            'vehicle_id'      => $this->vehicle->id,
            'route_id'        => $this->route->id,
            'trip_date'       => '2026-12-01',
            'departure_time'  => '09:00:00',
            'available_seats' => 10,
            'fare'            => 1000.00,
            'status'          => 'Scheduled',
        ]);
    }

    public function test_guest_cannot_access_notifications_page(): void
    {
        $response = $this->get('/notifications');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_notifications_page(): void
    {
        Notification::create([
            'user_id' => $this->passenger->id,
            'message' => 'Welcome to RouteConnect!',
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->passenger)->get('/notifications');

        $response->assertStatus(200);
        $response->assertSee('Notification Center');
        $response->assertSee('Welcome to RouteConnect!');
    }

    public function test_user_only_sees_their_own_notifications(): void
    {
        Notification::create([
            'user_id' => $this->passenger->id,
            'message' => "Amina's secret notification",
            'is_read' => false,
        ]);

        Notification::create([
            'user_id' => $this->otherPassenger->id,
            'message' => "Zainab's private alert",
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->passenger)->get('/notifications');

        $response->assertStatus(200);
        $response->assertSee("Amina's secret notification");
        $response->assertDontSee("Zainab's private alert");
    }

    public function test_user_can_mark_notification_as_read(): void
    {
        $notification = Notification::create([
            'user_id' => $this->passenger->id,
            'message' => 'Please confirm your luggage check.',
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->passenger)->post("/notifications/{$notification->id}/read");

        $response->assertRedirect();
        $notification->refresh();
        $this->assertTrue($notification->is_read);
    }

    public function test_user_cannot_mark_another_users_notification_as_read(): void
    {
        $notification = Notification::create([
            'user_id' => $this->otherPassenger->id,
            'message' => 'Private update.',
            'is_read' => false,
        ]);

        $response = $this->actingAs($this->passenger)->post("/notifications/{$notification->id}/read");

        $response->assertStatus(403);
        $notification->refresh();
        $this->assertFalse($notification->is_read);
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        Notification::create(['user_id' => $this->passenger->id, 'message' => 'Alert 1', 'is_read' => false]);
        Notification::create(['user_id' => $this->passenger->id, 'message' => 'Alert 2', 'is_read' => false]);
        $otherNotif = Notification::create(['user_id' => $this->otherPassenger->id, 'message' => 'Other alert', 'is_read' => false]);

        $response = $this->actingAs($this->passenger)->post('/notifications/mark-all-read');

        $response->assertRedirect();
        $this->assertEquals(0, Notification::where('user_id', $this->passenger->id)->where('is_read', false)->count());
        
        // Other passenger's notification should remain unread
        $otherNotif->refresh();
        $this->assertFalse($otherNotif->is_read);
    }

    public function test_successful_booking_creates_notifications(): void
    {
        $this->actingAs($this->passenger)->post("/booking/{$this->trip->id}", [
            'seats' => 2,
        ]);

        // Passenger notification
        $passengerNotification = Notification::where('user_id', $this->passenger->id)
            ->where('message', 'like', '%booking%confirmed%')
            ->first();
        $this->assertNotNull($passengerNotification);

        // Driver notification
        $driverNotification = Notification::where('user_id', $this->driver->id)
            ->where('message', 'like', '%booked 2 seat(s)%')
            ->first();
        $this->assertNotNull($driverNotification);
    }

    public function test_booking_cancellation_creates_notifications(): void
    {
        $booking = Booking::create([
            'user_id'           => $this->passenger->id,
            'trip_id'           => $this->trip->id,
            'seats'             => 2,
            'total_fare'        => 2000.00,
            'booking_reference' => 'RC-NOTIF-99',
            'status'            => 'confirmed',
            'booking_status'    => 'confirmed',
        ]);

        $this->actingAs($this->passenger)->post("/my-bookings/{$booking->id}/cancel");

        // Passenger notification
        $passengerCancelNotification = Notification::where('user_id', $this->passenger->id)
            ->where('message', 'like', '%cancelled%')
            ->first();
        $this->assertNotNull($passengerCancelNotification);

        // Driver notification
        $driverCancelNotification = Notification::where('user_id', $this->driver->id)
            ->where('message', 'like', '%cancelled%')
            ->first();
        $this->assertNotNull($driverCancelNotification);
    }

    public function test_driver_approval_creates_notification(): void
    {
        $this->actingAs($this->admin)->post("/admin/drivers/{$this->driver->id}/approve");

        $approvalNotification = Notification::where('user_id', $this->driver->id)
            ->where('message', 'like', '%approved%')
            ->first();

        $this->assertNotNull($approvalNotification);
        $this->assertFalse($approvalNotification->is_read);
    }

    public function test_driver_rejection_creates_notification(): void
    {
        $this->actingAs($this->admin)->post("/admin/drivers/{$this->driver->id}/reject");

        $rejectionNotification = Notification::where('user_id', $this->driver->id)
            ->where('message', 'like', '%rejected%')
            ->first();

        $this->assertNotNull($rejectionNotification);
        $this->assertFalse($rejectionNotification->is_read);
    }
}
