<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\DriverApplication;
use App\Models\Message;
use App\Models\Route;
use App\Models\Stop;
use App\Models\TravelRequest;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PassengerDriverMessagingSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $passenger;
    protected User $otherPassenger;
    protected User $driver;
    protected User $otherDriver;
    protected User $admin;
    protected Vehicle $vehicle;
    protected Route $route;
    protected Trip $trip;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Passenger
        $this->passenger = User::factory()->create([
            'name'      => 'Aleeza Fatima',
            'email'     => 'passenger@routeconnect.test',
            'user_type' => 0,
        ]);

        // 2. Unrelated other passenger
        $this->otherPassenger = User::factory()->create([
            'name'      => 'Other Passenger',
            'email'     => 'otherpassenger@routeconnect.test',
            'user_type' => 0,
        ]);

        // 3. Approved Driver
        $this->driver = User::factory()->create([
            'name'          => 'Ahmad Driver',
            'email'         => 'driver@routeconnect.test',
            'user_type'     => 2,
            'driver_status' => 'approved',
        ]);

        // 4. Other Driver
        $this->otherDriver = User::factory()->create([
            'name'          => 'Bilal Driver',
            'email'         => 'otherdriver@routeconnect.test',
            'user_type'     => 2,
            'driver_status' => 'approved',
        ]);

        // 5. Admin
        $this->admin = User::factory()->create([
            'name'      => 'Admin User',
            'email'     => 'admin@routeconnect.test',
            'user_type' => 1,
        ]);

        // 6. Vehicle
        $this->vehicle = Vehicle::create([
            'user_id'                 => $this->driver->id,
            'model'                   => 'Toyota HiAce GL',
            'type'                    => 'Van',
            'registration_no'         => 'RC-MSG-88',
            'total_seats'             => 14,
            'status'                  => 'approved',
            'rental_rate_per_day'     => 6000.00,
            'is_available_for_rental' => true,
        ]);

        // 7. Route & Trip
        $s1 = Stop::create(['name' => 'Gilgit Terminal', 'location' => 'Gilgit', 'status' => 'Active']);
        $s2 = Stop::create(['name' => 'Hunza Aliabad', 'location' => 'Hunza', 'status' => 'Active']);

        $this->route = Route::create([
            'name'           => 'Gilgit - Hunza Line',
            'start_stop_id'  => $s1->id,
            'end_stop_id'    => $s2->id,
            'start_location' => 'Gilgit',
            'end_location'   => 'Hunza',
            'distance_km'    => 100,
            'base_fare'      => 1200,
            'status'         => 'Active',
        ]);

        $this->trip = Trip::create([
            'user_id'         => $this->driver->id,
            'vehicle_id'      => $this->vehicle->id,
            'route_id'        => $this->route->id,
            'trip_date'       => Carbon::today()->addDays(2)->format('Y-m-d'),
            'departure_time'  => '08:30:00',
            'available_seats' => 10,
            'fare'            => 1200.00,
            'status'          => 'scheduled',
        ]);
    }

    /** 1. Dedicated Messages page loads with conversation list and active chat */
    public function test_messages_page_loads_with_conversation_list_and_active_chat(): void
    {
        $conv = Conversation::create([
            'title'              => 'Direct Discussion',
            'subject_type'       => 'direct',
            'created_by'         => $this->passenger->id,
            'arrangement_status' => 'negotiating',
        ]);

        ConversationParticipant::create([
            'conversation_id' => $conv->id,
            'user_id'         => $this->passenger->id,
            'last_read_at'    => now(),
        ]);

        ConversationParticipant::create([
            'conversation_id' => $conv->id,
            'user_id'         => $this->driver->id,
            'last_read_at'    => null,
        ]);

        Message::create([
            'conversation_id' => $conv->id,
            'sender_id'       => $this->driver->id,
            'body'            => 'Hello! Yes I am available on that date.',
            'type'            => 'text',
        ]);

        $response = $this->actingAs($this->passenger)->get('/messages?conversation_id=' . $conv->id);
        $response->assertStatus(200);
        $response->assertSee('Messages');
        $response->assertSee('Ahmad Driver');
        $response->assertSee('Hello! Yes I am available on that date.');
        $response->assertSee('Send');
    }

    /** 2. Participant can send a message and it persists in database */
    public function test_participant_can_send_message(): void
    {
        $conv = Conversation::create([
            'title'      => 'Chat',
            'created_by' => $this->passenger->id,
        ]);

        ConversationParticipant::create(['conversation_id' => $conv->id, 'user_id' => $this->passenger->id]);
        ConversationParticipant::create(['conversation_id' => $conv->id, 'user_id' => $this->driver->id]);

        $response = $this->actingAs($this->passenger)->postJson("/messages/{$conv->id}", [
            'body' => 'What is your best price for 4 seats?',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conv->id,
            'sender_id'       => $this->passenger->id,
            'body'            => 'What is your best price for 4 seats?',
            'is_read'         => false,
        ]);
    }

    /** 3. Security: Non-participant cannot view or post messages in unrelated conversation */
    public function test_security_non_participant_is_denied(): void
    {
        $conv = Conversation::create([
            'title'      => 'Private Passenger-Driver Chat',
            'created_by' => $this->passenger->id,
        ]);

        ConversationParticipant::create(['conversation_id' => $conv->id, 'user_id' => $this->passenger->id]);
        ConversationParticipant::create(['conversation_id' => $conv->id, 'user_id' => $this->driver->id]);

        // Unrelated other passenger tries to view
        $response = $this->actingAs($this->otherPassenger)->get("/messages/{$conv->id}");
        $response->assertStatus(403);

        // Unrelated other passenger tries to send message
        $postResponse = $this->actingAs($this->otherPassenger)->postJson("/messages/{$conv->id}", [
            'body' => 'Intruder message',
        ]);
        $postResponse->assertStatus(403);
    }

    /** 4. Reading a conversation marks received messages as read */
    public function test_viewing_conversation_marks_messages_as_read(): void
    {
        $conv = Conversation::create(['created_by' => $this->driver->id]);

        ConversationParticipant::create(['conversation_id' => $conv->id, 'user_id' => $this->passenger->id]);
        ConversationParticipant::create(['conversation_id' => $conv->id, 'user_id' => $this->driver->id]);

        $msg = Message::create([
            'conversation_id' => $conv->id,
            'sender_id'       => $this->driver->id,
            'body'            => 'Unread message from driver',
            'is_read'         => false,
        ]);

        $this->assertEquals(1, $this->passenger->unreadMessagesCount());

        // Passenger opens conversation
        $this->actingAs($this->passenger)->get('/messages?conversation_id=' . $conv->id);

        $msg->refresh();
        $this->assertTrue((bool) $msg->is_read);
        $this->assertNotNull($msg->read_at);
        $this->assertEquals(0, $this->passenger->unreadMessagesCount());
    }

    /** 5. Start conversation from shared ride listing (trip_id) */
    public function test_start_conversation_from_shared_ride_listing(): void
    {
        $response = $this->actingAs($this->passenger)->get("/messages/start?trip_id={$this->trip->id}");
        $response->assertRedirect();

        $this->assertDatabaseHas('conversations', [
            'subject_type' => 'trip',
            'subject_id'   => $this->trip->id,
        ]);

        $conv = Conversation::where('subject_type', 'trip')->where('subject_id', $this->trip->id)->first();
        $this->assertTrue($conv->isParticipant($this->passenger));
        $this->assertTrue($conv->isParticipant($this->driver));
    }

    /** 6. Start conversation from private vehicle listing (vehicle_id) */
    public function test_start_conversation_from_private_vehicle_listing(): void
    {
        $response = $this->actingAs($this->passenger)->get("/messages/start?vehicle_id={$this->vehicle->id}");
        $response->assertRedirect();

        $this->assertDatabaseHas('conversations', [
            'subject_type' => 'vehicle',
            'subject_id'   => $this->vehicle->id,
        ]);
    }

    /** 7. Start conversation from existing booking (booking_id) */
    public function test_start_conversation_from_existing_booking(): void
    {
        $booking = Booking::create([
            'user_id'              => $this->passenger->id,
            'trip_id'              => $this->trip->id,
            'driver_id'            => $this->driver->id,
            'vehicle_id'           => $this->vehicle->id,
            'seats'                => 2,
            'total_fare'           => 2400,
            'booking_reference'    => 'RC-TEST-BOOK-123',
            'booking_status'       => 'confirmed',
            'status'               => 'confirmed',
        ]);

        $response = $this->actingAs($this->passenger)->get("/messages/start?booking_id={$booking->id}");
        $response->assertRedirect();

        $this->assertDatabaseHas('conversations', [
            'subject_type' => 'booking',
            'subject_id'   => $booking->id,
        ]);
    }

    /** 8. Start conversation from driver profile / direct driver_id */
    public function test_start_conversation_from_driver_profile(): void
    {
        $response = $this->actingAs($this->passenger)->get("/messages/start?driver_id={$this->driver->id}");
        $response->assertRedirect();

        $this->assertDatabaseHas('conversations', [
            'subject_type' => 'direct',
            'subject_id'   => $this->driver->id,
        ]);
    }

    /** 9. Driver to Passenger: Started from travel request application */
    public function test_driver_to_passenger_from_travel_request_application(): void
    {
        $travelRequest = TravelRequest::create([
            'user_id'              => $this->passenger->id,
            'pickup_location'      => 'Gilgit City',
            'destination_location' => 'Hunza Passu',
            'travel_date'          => Carbon::today()->addDays(3)->format('Y-m-d'),
            'seats_needed'         => 4,
            'status'               => 'open',
        ]);

        $application = DriverApplication::create([
            'travel_request_id' => $travelRequest->id,
            'driver_id'         => $this->driver->id,
            'offered_fare'      => 4000,
            'status'            => 'pending',
        ]);

        $response = $this->actingAs($this->driver)->get("/messages/start?driver_application_id={$application->id}");
        $response->assertRedirect();

        $this->assertDatabaseHas('conversations', [
            'subject_type' => 'driver_application',
            'subject_id'   => $application->id,
        ]);
    }

    /** 10. Business Rule: Chatting alone does not automatically confirm booking */
    public function test_chatting_does_not_automatically_confirm_booking(): void
    {
        $conv = Conversation::create([
            'title'              => 'Car Inquiry',
            'subject_type'       => 'vehicle',
            'subject_id'         => $this->vehicle->id,
            'arrangement_status' => 'negotiating',
            'created_by'         => $this->passenger->id,
        ]);

        ConversationParticipant::create(['conversation_id' => $conv->id, 'user_id' => $this->passenger->id]);
        ConversationParticipant::create(['conversation_id' => $conv->id, 'user_id' => $this->driver->id]);

        // Exchange 5 messages
        for ($i = 1; $i <= 5; $i++) {
            Message::create([
                'conversation_id' => $conv->id,
                'sender_id'       => ($i % 2 === 0) ? $this->passenger->id : $this->driver->id,
                'body'            => "Message number {$i}",
            ]);
        }

        $conv->refresh();
        // Arrangement status must NOT be confirmed automatically
        $this->assertEquals('negotiating', $conv->arrangement_status);
        $this->assertNull($conv->confirmed_booking_id);
    }

    /** 11. Preservation: Conversations are preserved even after a booking is cancelled */
    public function test_conversations_preserved_after_booking_cancellation(): void
    {
        $booking = Booking::create([
            'user_id'              => $this->passenger->id,
            'driver_id'            => $this->driver->id,
            'vehicle_id'           => $this->vehicle->id,
            'seats'                => 1,
            'total_fare'           => 1200,
            'booking_reference'    => 'RC-CANCEL-PRESERVE',
            'booking_status'       => 'confirmed',
            'status'               => 'confirmed',
        ]);

        $conv = Conversation::create([
            'title'                => 'Booking #RC-CANCEL-PRESERVE',
            'subject_type'         => 'booking',
            'subject_id'           => $booking->id,
            'confirmed_booking_id' => $booking->id,
            'created_by'           => $this->passenger->id,
        ]);

        ConversationParticipant::create(['conversation_id' => $conv->id, 'user_id' => $this->passenger->id]);
        ConversationParticipant::create(['conversation_id' => $conv->id, 'user_id' => $this->driver->id]);

        Message::create([
            'conversation_id' => $conv->id,
            'sender_id'       => $this->passenger->id,
            'body'            => 'Let us keep this discussion history.',
        ]);

        // Cancel the booking
        $booking->update(['status' => 'cancelled', 'booking_status' => 'cancelled']);

        // Conversation and messages remain intact
        $this->assertDatabaseHas('conversations', ['id' => $conv->id]);
        $this->assertDatabaseHas('messages', ['conversation_id' => $conv->id, 'body' => 'Let us keep this discussion history.']);
    }

    /** 12. Polling / fetch messages endpoint returns new messages correctly */
    public function test_fetch_messages_polling_endpoint(): void
    {
        $conv = Conversation::create(['created_by' => $this->passenger->id]);
        ConversationParticipant::create(['conversation_id' => $conv->id, 'user_id' => $this->passenger->id]);
        ConversationParticipant::create(['conversation_id' => $conv->id, 'user_id' => $this->driver->id]);

        $msg1 = Message::create([
            'conversation_id' => $conv->id,
            'sender_id'       => $this->driver->id,
            'body'            => 'First message',
        ]);

        $msg2 = Message::create([
            'conversation_id' => $conv->id,
            'sender_id'       => $this->driver->id,
            'body'            => 'Second live polled message',
        ]);

        $response = $this->actingAs($this->passenger)->getJson("/messages/{$conv->id}/fetch?since_id={$msg1->id}");
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'messages');
        $response->assertJsonFragment(['body' => 'Second live polled message']);
    }
}

