<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\DriverApplication;
use App\Models\Message;
use App\Models\Route as RouteModel;
use App\Models\Stop;
use App\Models\TravelRequest;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FlutterApiIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $passenger;
    protected User $driver;
    protected Vehicle $vehicle;
    protected RouteModel $route;
    protected Stop $startStop;
    protected Stop $endStop;

    protected function setUp(): void
    {
        parent::setUp();

        $this->passenger = User::create([
            'name'              => 'Flutter Passenger',
            'email'             => 'passenger_' . uniqid() . '@routeconnect.test',
            'phone'             => '03001234567',
            'user_type'         => 0,
            'password'          => Hash::make('password123'),
            'driver_status'     => 'inactive',
            'email_verified_at' => now(),
        ]);

        $this->driver = User::create([
            'name'              => 'Flutter Driver',
            'email'             => 'driver_' . uniqid() . '@routeconnect.test',
            'phone'             => '03007654321',
            'user_type'         => 2,
            'password'          => Hash::make('password123'),
            'driver_status'     => 'approved',
            'email_verified_at' => now(),
        ]);

        $this->vehicle = Vehicle::create([
            'user_id'                 => $this->driver->id,
            'model'                   => 'Corolla GLi',
            'type'                    => 'car',
            'registration_no'         => 'FL-' . rand(1000, 9999),
            'total_seats'             => 4,
            'status'                  => 'approved',
            'is_available_for_rental' => true,
            'rental_rate_per_day'     => 4500,
        ]);

        $this->startStop = Stop::create([
            'name'     => 'Lahore Thokar',
            'location' => 'Lahore',
            'status'   => 'active',
        ]);

        $this->endStop = Stop::create([
            'name'     => 'Islamabad Faizabad',
            'location' => 'Islamabad',
            'status'   => 'active',
        ]);

        $this->route = RouteModel::create([
            'name'           => 'Lahore to Islamabad M2',
            'start_location' => 'Lahore',
            'end_location'   => 'Islamabad',
            'start_stop_id'  => $this->startStop->id,
            'end_stop_id'    => $this->endStop->id,
            'status'         => 'active',
        ]);
    }

    public function test_auth_registration_returns_token_and_user(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name'                  => 'New API User',
            'email'                 => 'newapi_' . uniqid() . '@routeconnect.test',
            'phone'                 => '03129876543',
            'role'                  => 'passenger',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email', 'role'],
                    'token',
                    'token_type',
                ],
            ]);
    }

    public function test_auth_login_and_me_and_logout(): void
    {
        // Login
        $loginRes = $this->postJson('/api/auth/login', [
            'email'    => $this->passenger->email,
            'password' => 'password123',
        ]);

        $loginRes->assertStatus(200)
            ->assertJsonPath('success', true);

        $token = $loginRes->json('data.token');
        $this->assertNotEmpty($token);

        // Get current user profile
        $meRes = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/auth/me');

        $meRes->assertStatus(200)
            ->assertJsonPath('data.user.id', $this->passenger->id);

        // Update profile
        $updateRes = $this->withHeader('Authorization', "Bearer {$token}")
            ->putJson('/api/auth/profile', [
                'name'  => 'Flutter Passenger Updated',
                'phone' => '03009998877',
            ]);

        $updateRes->assertStatus(200)
            ->assertJsonPath('data.user.name', 'Flutter Passenger Updated');

        // Logout
        $logoutRes = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/auth/logout');

        $logoutRes->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_reference_data_stops_and_routes(): void
    {
        $stopsRes = $this->getJson('/api/stops');
        $stopsRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => [['id', 'name', 'location']]]);

        $routesRes = $this->getJson('/api/routes');
        $routesRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => [['id', 'name', 'start_location', 'end_location']]]);
    }

    public function test_driver_publishes_shared_trip_and_passenger_books_seats(): void
    {
        // Driver publishes a shared trip
        $tripRes = $this->actingAs($this->driver, 'sanctum')
            ->postJson('/api/driver/trips', [
                'route_id'        => $this->route->id,
                'vehicle_id'      => $this->vehicle->id,
                'trip_date'       => Carbon::tomorrow()->format('Y-m-d'),
                'departure_time'  => '08:30:00',
                'available_seats' => 3,
                'price_per_seat'  => 1200,
            ]);

        $tripRes->assertStatus(201)
            ->assertJsonPath('success', true);

        $tripId = $tripRes->json('data.id');

        // Passenger searches trips
        $searchRes = $this->getJson('/api/trips?origin=Lahore&destination=Islamabad');
        $searchRes->assertStatus(200)
            ->assertJsonPath('success', true);

        // Passenger books 2 seats
        $bookRes = $this->actingAs($this->passenger, 'sanctum')
            ->postJson("/api/trips/{$tripId}/book", [
                'seats'          => 2,
                'pickup_stop_id' => $this->startStop->id,
                'drop_stop_id'   => $this->endStop->id,
            ]);

        $bookRes->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.seats', 2);
        $this->assertEquals(2400, (float) $bookRes->json('data.total_fare'));

        // Verify remaining seats reduced to 1
        $trip = Trip::find($tripId);
        $this->assertEquals(1, $trip->available_seats);

        // Attempting to book 2 more seats should fail with 422
        $overbookRes = $this->actingAs($this->passenger, 'sanctum')
            ->postJson("/api/trips/{$tripId}/book", [
                'seats' => 2,
            ]);

        $overbookRes->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    public function test_driver_registers_vehicle_and_passenger_books_private_rental(): void
    {
        // Driver adds vehicle
        $newVehRes = $this->actingAs($this->driver, 'sanctum')
            ->postJson('/api/vehicles', [
                'make'                => 'Honda',
                'model'               => 'Civic RS',
                'year'                => 2023,
                'registration_no'     => 'API-' . rand(1000, 9999),
                'color'               => 'Black',
                'total_seats'         => 4,
                'type'                => 'car',
                'rental_rate_per_day' => 6000,
            ]);

        $newVehRes->assertStatus(201)
            ->assertJsonPath('success', true);

        // Passenger books private vehicle
        $bookDate = Carbon::now()->addDays(5)->format('Y-m-d');
        $privateRes = $this->actingAs($this->passenger, 'sanctum')
            ->postJson("/api/vehicles/{$this->vehicle->id}/book", [
                'pickup_location'      => 'DHA Phase 5 Lahore',
                'destination_location' => 'Islamabad F-7',
                'booking_date'         => $bookDate,
                'booking_time'         => '10:00',
                'total_fare'           => 4500,
            ]);

        $privateRes->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.booking_type', 'private');

        // Check availability endpoint returns booked=false for that date
        $availRes = $this->getJson("/api/vehicles/{$this->vehicle->id}/availability?date={$bookDate}");
        $availRes->assertStatus(200)
            ->assertJsonPath('available', false);
    }

    public function test_travel_request_and_driver_application_workflow(): void
    {
        // Passenger creates travel request
        $reqRes = $this->actingAs($this->passenger, 'sanctum')
            ->postJson('/api/travel-requests', [
                'pickup_location'      => 'Model Town Lahore',
                'destination_location' => 'Blue Area Islamabad',
                'travel_date'          => Carbon::tomorrow()->format('Y-m-d'),
                'preferred_time'       => '07:30',
                'seats_needed'         => 2,
                'preferred_vehicle'    => 'car',
                'budget'               => 3000,
                'notes'                => 'Need punctual ride with AC.',
            ]);

        $reqRes->assertStatus(201)
            ->assertJsonPath('success', true);

        $requestId = $reqRes->json('data.id');

        // Driver views travel requests
        $listRes = $this->actingAs($this->driver, 'sanctum')
            ->getJson('/api/travel-requests');

        $listRes->assertStatus(200)
            ->assertJsonPath('success', true);

        // Driver applies to travel request
        $applyRes = $this->actingAs($this->driver, 'sanctum')
            ->postJson("/api/travel-requests/{$requestId}/apply", [
                'vehicle_id'    => $this->vehicle->id,
                'offered_fare'  => 2800,
                'proposed_time' => '07:45',
                'message'       => 'I can pick you up at 07:45 in a clean Corolla.',
            ]);

        $applyRes->assertStatus(201)
            ->assertJsonPath('success', true);

        $appId = $applyRes->json('data.application.id');

        // Passenger selects/accepts this driver application
        $acceptRes = $this->actingAs($this->passenger, 'sanctum')
            ->postJson("/api/travel-requests/{$requestId}/applications/{$appId}/select");

        $acceptRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.application.status', 'accepted')
            ->assertJsonPath('data.travel_request.status', 'confirmed');
    }

    public function test_in_app_messaging_and_trip_arrangements(): void
    {
        // Start conversation from vehicle listing
        $startRes = $this->actingAs($this->passenger, 'sanctum')
            ->postJson('/api/conversations/start', [
                'vehicle_id' => $this->vehicle->id,
            ]);

        $startRes->assertStatus(200)
            ->assertJsonPath('success', true);

        $conversationId = $startRes->json('data.id');

        // Passenger sends message
        $sendRes = $this->actingAs($this->passenger, 'sanctum')
            ->postJson("/api/conversations/{$conversationId}/messages", [
                'body' => 'Hi, is this car available for Sunday?',
            ]);

        $sendRes->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.body', 'Hi, is this car available for Sunday?');

        // Check unread count for driver
        $unreadRes = $this->actingAs($this->driver, 'sanctum')
            ->getJson('/api/conversations/unread-count');

        $unreadRes->assertStatus(200)
            ->assertJsonPath('success', true);
        $this->assertGreaterThanOrEqual(1, $unreadRes->json('data.unread_count'));

        // Driver reads messages
        $msgListRes = $this->actingAs($this->driver, 'sanctum')
            ->getJson("/api/conversations/{$conversationId}/messages");

        $msgListRes->assertStatus(200)
            ->assertJsonPath('success', true);

        // Driver proposes arrangement
        $propDate = Carbon::tomorrow()->format('Y-m-d');
        $propRes = $this->actingAs($this->driver, 'sanctum')
            ->postJson("/api/conversations/{$conversationId}/propose", [
                'route' => 'Lahore → Islamabad',
                'date'  => $propDate,
                'time'  => '09:00',
                'fare'  => 5000,
                'seats' => 4,
            ]);

        $propRes->assertStatus(200)
            ->assertJsonPath('success', true);

        // Passenger confirms arrangement
        $confRes = $this->actingAs($this->passenger, 'sanctum')
            ->postJson("/api/conversations/{$conversationId}/confirm");

        $confRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.arrangement_status', 'confirmed');

        // Passenger completes arrangement
        $compRes = $this->actingAs($this->passenger, 'sanctum')
            ->postJson("/api/conversations/{$conversationId}/complete");

        $compRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.arrangement_status', 'completed');
    }

    public function test_my_activity_endpoint_for_passenger_and_driver(): void
    {
        // Passenger activity
        $passActRes = $this->actingAs($this->passenger, 'sanctum')
            ->getJson('/api/activity');

        $passActRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('role', 'passenger')
            ->assertJsonStructure([
                'success',
                'role',
                'counts' => [
                    'seat_bookings',
                    'private_bookings',
                    'travel_requests',
                    'applications_received',
                    'confirmed_arrangements',
                    'completed_trips',
                    'cancelled_expired',
                ],
                'data' => [
                    'seat_bookings',
                    'private_bookings',
                    'travel_requests',
                    'applications_received',
                    'confirmed_arrangements',
                    'completed_trips',
                    'cancelled_expired',
                ],
            ]);

        // Driver activity
        $driverActRes = $this->actingAs($this->driver, 'sanctum')
            ->getJson('/api/activity');

        $driverActRes->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('role', 'driver')
            ->assertJsonStructure([
                'success',
                'role',
                'counts' => [
                    'shared_rides',
                    'my_vehicles',
                    'my_bookings',
                    'passenger_requests',
                    'my_applications',
                    'confirmed_arrangements',
                    'completed_trips',
                    'cancelled_expired',
                ],
                'data' => [
                    'shared_rides',
                    'my_vehicles',
                    'my_bookings',
                    'passenger_requests',
                    'my_applications',
                    'confirmed_arrangements',
                    'completed_trips',
                    'cancelled_expired',
                ],
            ]);
    }

    public function test_security_unauthenticated_requests_are_rejected(): void
    {
        $this->getJson('/api/auth/me')->assertStatus(401);
        $this->getJson('/api/activity')->assertStatus(401);
        $this->getJson('/api/travel-requests')->assertStatus(401);
        $this->getJson('/api/conversations')->assertStatus(401);
    }

    public function test_security_conversation_isolation(): void
    {
        $outsider = User::create([
            'name'              => 'Outsider User',
            'email'             => 'outsider_' . uniqid() . '@routeconnect.test',
            'phone'             => '03451112233',
            'user_type'         => 0,
            'password'          => Hash::make('password123'),
            'driver_status'     => 'inactive',
            'email_verified_at' => now(),
        ]);
        $outsiderToken = $outsider->createToken('outsider-token')->plainTextToken;

        // Conversation between passenger and driver
        $conv = Conversation::create([
            'title'              => 'Private Chat',
            'subject_type'       => 'direct',
            'subject_id'         => $this->driver->id,
            'status'             => 'active',
            'arrangement_status' => 'negotiating',
            'created_by'         => $this->passenger->id,
        ]);
        ConversationParticipant::create(['conversation_id' => $conv->id, 'user_id' => $this->passenger->id]);
        ConversationParticipant::create(['conversation_id' => $conv->id, 'user_id' => $this->driver->id]);

        // Outsider attempts to access messages
        $res = $this->withHeader('Authorization', "Bearer {$outsiderToken}")
            ->getJson("/api/conversations/{$conv->id}/messages");

        $res->assertStatus(403);
    }
}
