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

class ActivityAndMessagingFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $passenger;
    protected User $driver;
    protected Vehicle $vehicle;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create Passenger
        $this->passenger = User::factory()->create([
            'name'      => 'Aleeza Passenger',
            'email'     => 'passenger.aleeza@example.com',
            'user_type' => 0,
            'phone'     => '03120000001',
        ]);

        // 2. Create Approved Driver
        $this->driver = User::factory()->create([
            'name'          => 'Ahmed Driver',
            'email'         => 'driver.ahmed@example.com',
            'user_type'     => 2,
            'driver_status' => 'approved',
            'phone'         => '03120000002',
        ]);

        // 3. Create Approved Vehicle
        $this->vehicle = Vehicle::create([
            'user_id'                 => $this->driver->id,
            'type'                    => 'Van',
            'registration_no'         => 'ICT-7788',
            'model'                   => 'Toyota HiAce',
            'total_seats'             => 14,
            'rental_rate_per_day'     => 6000.00,
            'is_available_for_rental' => true,
            'status'                  => 'approved',
        ]);
    }

    // ─── 1. My Activity: Passenger Role Tests ─────────────────────────────────

    public function test_passenger_can_access_my_activity_and_sees_passenger_tabs(): void
    {
        $response = $this->actingAs($this->passenger)->get('/my-activity');
        $response->assertOk();

        $html = $response->getContent();

        $this->assertStringContainsString('My Activity', $html);
        $this->assertStringContainsString('Seat Bookings', $html);
        $this->assertStringContainsString('Private Vehicle Bookings', $html);
        $this->assertStringContainsString('My Travel Requests', $html);
        $this->assertStringContainsString('Driver Offers', $html);
        $this->assertStringContainsString('Confirmed Arrangements', $html);
        $this->assertStringContainsString('Completed & Cancelled', $html);
        $this->assertStringContainsString('Post Travel Request', $html);
    }

    public function test_passenger_can_post_and_delete_travel_request(): void
    {
        $postData = [
            'pickup_location'      => 'Gilgit Airport',
            'destination_location' => 'Hunza Aliabad',
            'travel_date'          => Carbon::today()->addDays(2)->format('Y-m-d'),
            'travel_time'          => '10:00 AM',
            'seats_needed'         => 3,
            'budget'               => 3500.00,
            'notes'                => 'We have 2 large luggage bags.',
        ];

        $response = $this->actingAs($this->passenger)->post('/my-activity/requests', $postData);
        $response->assertRedirect('/my-activity?tab=travel-requests');

        $this->assertDatabaseHas('travel_requests', [
            'user_id'              => $this->passenger->id,
            'pickup_location'      => 'Gilgit Airport',
            'destination_location' => 'Hunza Aliabad',
            'seats_needed'         => 3,
            'budget'               => 3500.00,
            'status'               => 'open',
        ]);

        $travelRequest = TravelRequest::where('user_id', $this->passenger->id)->first();

        // Passenger can delete / cancel the request
        $delResponse = $this->actingAs($this->passenger)->delete("/my-activity/requests/{$travelRequest->id}", [
            'reason' => 'Found a driver on chat',
        ]);
        $delResponse->assertRedirect('/my-activity?tab=travel-requests');

        $travelRequest->refresh();
        $this->assertEquals('cancelled', $travelRequest->status);
    }

    // ─── 2. My Activity: Driver Role Tests ────────────────────────────────────

    public function test_driver_can_access_my_activity_and_sees_driver_tabs(): void
    {
        $response = $this->actingAs($this->driver)->get('/my-activity');
        $response->assertOk();

        $html = $response->getContent();

        $this->assertStringContainsString('My Activity', $html);
        $this->assertStringContainsString('My Vehicles', $html);
        $this->assertStringContainsString('Shared Ride Listings', $html);
        $this->assertStringContainsString('Private Bookings', $html);
        $this->assertStringContainsString('Passenger Requests', $html);
        $this->assertStringContainsString('My Applications', $html);
        $this->assertStringContainsString('Confirmed Arrangements', $html);
        $this->assertStringContainsString('Schedule a Trip', $html);
        $this->assertStringContainsString($this->vehicle->registration_no, $html);
    }

    public function test_driver_can_apply_to_passenger_travel_request_and_starts_chat(): void
    {
        // Passenger creates travel request
        $travelRequest = TravelRequest::create([
            'user_id'              => $this->passenger->id,
            'pickup_location'      => 'Islamabad',
            'destination_location' => 'Naran',
            'travel_date'          => Carbon::today()->addDays(3)->format('Y-m-d'),
            'seats_needed'         => 4,
            'budget'               => 8000.00,
            'status'               => 'open',
        ]);

        // Driver applies
        $applyData = [
            'offered_fare'  => 7500.00,
            'proposed_time' => '08:00 AM',
            'vehicle_id'    => $this->vehicle->id,
            'message'       => 'I have a spacious HiAce with AC available for your trip.',
        ];

        $response = $this->actingAs($this->driver)->post("/my-activity/requests/{$travelRequest->id}/apply", $applyData);

        $this->assertDatabaseHas('driver_applications', [
            'travel_request_id' => $travelRequest->id,
            'driver_id'         => $this->driver->id,
            'offered_fare'      => 7500.00,
            'status'            => 'pending',
        ]);

        // Conversation should be automatically created between driver and passenger
        $conversation = Conversation::where('subject_type', 'travel_request')
            ->where('subject_id', $travelRequest->id)
            ->first();

        $this->assertNotNull($conversation);
        $response->assertRedirect("/messages/{$conversation->id}");
        $this->assertTrue($conversation->isParticipant($this->driver));
        $this->assertTrue($conversation->isParticipant($this->passenger));
    }

    // ─── 3. In-App Messaging & Duplicate Prevention ───────────────────────────

    public function test_passenger_can_start_conversation_from_vehicle_listing(): void
    {
        $response = $this->actingAs($this->passenger)->get("/messages/start?vehicle_id={$this->vehicle->id}");

        $conversation = Conversation::where('subject_type', 'vehicle')
            ->where('subject_id', $this->vehicle->id)
            ->first();

        $this->assertNotNull($conversation);
        $response->assertRedirect("/messages?conversation_id={$conversation->id}");

        // Duplicate prevention test: starting again must redirect to existing conversation
        $secondResponse = $this->actingAs($this->passenger)->get("/messages/start?vehicle_id={$this->vehicle->id}");
        $secondResponse->assertRedirect("/messages?conversation_id={$conversation->id}");
        $this->assertEquals(1, Conversation::where('subject_type', 'vehicle')->where('subject_id', $this->vehicle->id)->count());
    }

    public function test_non_participant_cannot_view_or_send_messages(): void
    {
        $stranger = User::factory()->create(['user_type' => 0]);

        $conv = Conversation::create([
            'title'      => 'Private Chat',
            'created_by' => $this->driver->id,
        ]);
        ConversationParticipant::create(['conversation_id' => $conv->id, 'user_id' => $this->driver->id]);
        ConversationParticipant::create(['conversation_id' => $conv->id, 'user_id' => $this->passenger->id]);

        $viewResponse = $this->actingAs($stranger)->get("/messages/{$conv->id}");
        $viewResponse->assertForbidden();

        $sendResponse = $this->actingAs($stranger)->post("/messages/{$conv->id}", ['body' => 'Intruder message']);
        $sendResponse->assertForbidden();
    }

    public function test_participants_can_exchange_messages_and_poll_updates(): void
    {
        $conv = Conversation::create([
            'title'      => 'Discussion',
            'created_by' => $this->passenger->id,
        ]);
        ConversationParticipant::create(['conversation_id' => $conv->id, 'user_id' => $this->passenger->id]);
        ConversationParticipant::create(['conversation_id' => $conv->id, 'user_id' => $this->driver->id]);

        // Passenger sends message
        $sendResponse = $this->actingAs($this->passenger)->postJson("/messages/{$conv->id}", [
            'body' => 'Can we depart at 9:00 AM instead?',
        ]);
        $sendResponse->assertOk();
        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conv->id,
            'sender_id'       => $this->passenger->id,
            'body'            => 'Can we depart at 9:00 AM instead?',
        ]);

        // Driver fetches messages via polling API
        $fetchResponse = $this->actingAs($this->driver)->getJson("/messages/{$conv->id}/fetch");
        $fetchResponse->assertOk();
        $data = $fetchResponse->json();
        $this->assertCount(1, $data['messages']);
        $this->assertEquals('Can we depart at 9:00 AM instead?', $data['messages'][0]['body']);
    }

    // ─── 4. Arrangement Confirmation & Overlap Conflict Prevention ───────────

    public function test_arrangement_proposal_and_confirmation_locks_booking(): void
    {
        $conv = Conversation::create([
            'title'        => "Vehicle: {$this->vehicle->model}",
            'subject_type' => 'vehicle',
            'subject_id'   => $this->vehicle->id,
            'created_by'   => $this->passenger->id,
        ]);
        ConversationParticipant::create(['conversation_id' => $conv->id, 'user_id' => $this->passenger->id]);
        ConversationParticipant::create(['conversation_id' => $conv->id, 'user_id' => $this->driver->id]);

        // 1. Propose terms
        $proposeData = [
            'route' => 'Islamabad → Abbottabad',
            'date'  => Carbon::today()->addDays(5)->format('Y-m-d'),
            'time'  => '08:30 AM',
            'fare'  => 12000.00,
            'seats' => 10,
            'notes' => 'Confirmed on route',
        ];

        $propResponse = $this->actingAs($this->driver)->post("/messages/{$conv->id}/propose", $proposeData);
        $propResponse->assertRedirect("/messages?conversation_id={$conv->id}");

        $conv->refresh();
        $this->assertEquals('negotiating', $conv->arrangement_status);
        $this->assertEquals(12000.00, $conv->arrangement_details['fare']);

        // 2. Passenger confirms arrangement
        $confirmResponse = $this->actingAs($this->passenger)->post("/messages/{$conv->id}/confirm");
        $confirmResponse->assertRedirect("/messages?conversation_id={$conv->id}");

        $conv->refresh();
        $this->assertEquals('confirmed', $conv->arrangement_status);
        $this->assertNotNull($conv->confirmed_booking_id);

        // Booking record must be created with 'confirmed' status
        $this->assertDatabaseHas('bookings', [
            'id'             => $conv->confirmed_booking_id,
            'user_id'        => $this->passenger->id,
            'vehicle_id'     => $this->vehicle->id,
            'total_fare'     => 12000.00,
            'booking_status' => 'confirmed',
        ]);
    }

    public function test_conflicting_confirmed_arrangement_prevents_double_booking(): void
    {
        $targetDate = Carbon::today()->addDays(7);

        // First confirmed booking
        Booking::create([
            'user_id'              => $this->passenger->id,
            'vehicle_id'           => $this->vehicle->id,
            'booking_type'         => 'private',
            'pickup_location'      => 'Islamabad',
            'destination_location' => 'Murree',
            'start_datetime'       => (clone $targetDate)->setTime(9, 0),
            'end_datetime'         => (clone $targetDate)->addDay()->setTime(9, 0),
            'days'                 => 1,
            'daily_rate'           => 6000.00,
            'total_fare'           => 6000.00,
            'seats'                => 14,
            'booking_reference'    => 'RC-ARR-TEST-1',
            'status'               => 'confirmed',
            'booking_status'       => 'confirmed',
        ]);

        // A second passenger tries to confirm an arrangement for the same vehicle on overlapping date
        $passenger2 = User::factory()->create(['user_type' => 0]);
        $conv2 = Conversation::create([
            'title'               => "Vehicle: {$this->vehicle->model}",
            'subject_type'        => 'vehicle',
            'subject_id'          => $this->vehicle->id,
            'arrangement_status'  => 'negotiating',
            'arrangement_details' => [
                'route' => 'Islamabad → Naran',
                'date'  => $targetDate->format('Y-m-d'),
                'time'  => '10:00 AM',
                'fare'  => 8000.00,
                'seats' => 5,
            ],
            'created_by'          => $passenger2->id,
        ]);
        ConversationParticipant::create(['conversation_id' => $conv2->id, 'user_id' => $passenger2->id]);
        ConversationParticipant::create(['conversation_id' => $conv2->id, 'user_id' => $this->driver->id]);

        $attemptResponse = $this->actingAs($passenger2)->post("/messages/{$conv2->id}/confirm");

        // Must fail and redirect back with conflict error
        $conv2->refresh();
        $this->assertNotEquals('confirmed', $conv2->arrangement_status);
        $this->assertNull($conv2->confirmed_booking_id);
    }

    // ─── 5. Block and Report Features ─────────────────────────────────────────

    public function test_participant_can_block_and_unblock_conversation(): void
    {
        $conv = Conversation::create([
            'title'      => 'Spam Chat',
            'created_by' => $this->passenger->id,
        ]);
        ConversationParticipant::create(['conversation_id' => $conv->id, 'user_id' => $this->passenger->id]);
        ConversationParticipant::create(['conversation_id' => $conv->id, 'user_id' => $this->driver->id]);

        // Passenger blocks conversation
        $this->actingAs($this->passenger)->post("/messages/{$conv->id}/block");
        $this->assertTrue($conv->isBlocked());

        // Driver cannot send messages when blocked
        $sendResponse = $this->actingAs($this->driver)->postJson("/messages/{$conv->id}", ['body' => 'Are you there?']);
        $sendResponse->assertStatus(403);

        // Passenger unblocks conversation
        $this->actingAs($this->passenger)->post("/messages/{$conv->id}/block");
        $this->assertFalse($conv->isBlocked());
    }

    public function test_participant_can_submit_moderation_report(): void
    {
        $conv = Conversation::create([
            'title'      => 'Reportable Chat',
            'created_by' => $this->passenger->id,
        ]);
        ConversationParticipant::create(['conversation_id' => $conv->id, 'user_id' => $this->passenger->id]);
        ConversationParticipant::create(['conversation_id' => $conv->id, 'user_id' => $this->driver->id]);

        $reportData = [
            'reason'  => 'Inappropriate or abusive language',
            'details' => 'The user used harsh words when negotiating fare.',
        ];

        $response = $this->actingAs($this->passenger)->post("/messages/{$conv->id}/report", $reportData);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('conversation_reports', [
            'conversation_id'  => $conv->id,
            'reported_by'      => $this->passenger->id,
            'reported_user_id' => $this->driver->id,
            'reason'           => 'Inappropriate or abusive language',
        ]);
    }
}

