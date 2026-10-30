<?php

namespace Tests\Feature;

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FrontendNavbarTest extends TestCase
{
    use RefreshDatabase;

    // ─── Guest Navbar Tests ───────────────────────────────────────────────────

    public function test_guest_navbar_shows_home_find_ride_about_login_register(): void
    {
        $response = $this->get('/');
        $response->assertOk();

        $html = $response->getContent();

        $this->assertStringContainsString('Home', $html);
        $this->assertStringContainsString('Find a Ride', $html);
        $this->assertStringContainsString('About', $html);
        $this->assertStringContainsString('Login', $html);
        $this->assertStringContainsString('Register', $html);

        // Guests should not see My Activity or Messages in main desktop navigation
        $this->assertStringNotContainsString('>My Activity</a>', $html);
        $this->assertStringNotContainsString('>Messages</a>', $html);
    }

    // ─── Authenticated Navbar Tests ───────────────────────────────────────────

    public function test_authenticated_navbar_shows_activity_messages_and_profile_dropdown(): void
    {
        $user = User::factory()->create([
            'name'      => 'Aleeza Fatima',
            'email'     => 'aleeza@example.com',
            'user_type' => 0, // Passenger
        ]);

        $response = $this->actingAs($user)->get('/');
        $response->assertOk();

        $html = $response->getContent();

        $this->assertStringContainsString('Home', $html);
        $this->assertStringContainsString('Find a Ride', $html);
        $this->assertStringContainsString('My Activity', $html);
        $this->assertStringContainsString('Messages', $html);
        $this->assertStringContainsString('About', $html);
        $this->assertStringContainsString('Aleeza Fatima', $html);
        $this->assertStringContainsString('profileDropdownBtn', $html);
    }

    // ─── Active Navigation States ─────────────────────────────────────────────

    public function test_home_page_activates_home_nav_link(): void
    {
        $response = $this->get('/');
        $response->assertOk();

        $html = $response->getContent();

        $this->assertStringContainsString('class="relative py-2 text-emerald-600 dark:text-emerald-400 transition-colors"', $html);
    }

    public function test_find_a_ride_activates_nav_link(): void
    {
        $response = $this->get('/trips');
        $response->assertOk();

        $html = $response->getContent();

        $this->assertStringContainsString('Find a Ride', $html);
        $this->assertMatchesRegularExpression(
            '/Find a Ride\s+<span class="absolute bottom-0/',
            $html
        );
    }

    public function test_my_activity_activates_nav_link_for_authenticated_user(): void
    {
        $user = User::factory()->create(['user_type' => 0]);

        $response = $this->actingAs($user)->get('/my-activity');
        $response->assertOk();

        $html = $response->getContent();

        $this->assertStringContainsString('My Activity', $html);
        $this->assertMatchesRegularExpression(
            '/My Activity\s+<span class="absolute bottom-0/',
            $html
        );
    }

    public function test_messages_page_activates_nav_link(): void
    {
        $user = User::factory()->create(['user_type' => 0]);

        $response = $this->actingAs($user)->get('/messages');
        $response->assertOk();

        $html = $response->getContent();

        $this->assertStringContainsString('Messages', $html);
        $this->assertMatchesRegularExpression(
            '/Messages.*?<span class="absolute bottom-0/s',
            $html
        );
    }

    public function test_unread_message_count_badge_appears_in_navbar(): void
    {
        $passenger = User::factory()->create(['user_type' => 0]);
        $driver = User::factory()->create(['user_type' => 2, 'driver_status' => 'approved']);

        $conv = Conversation::create([
            'title'      => 'Chat',
            'created_by' => $driver->id,
        ]);

        ConversationParticipant::create([
            'conversation_id' => $conv->id,
            'user_id'         => $passenger->id,
            'last_read_at'    => now()->subHour(),
        ]);
        ConversationParticipant::create([
            'conversation_id' => $conv->id,
            'user_id'         => $driver->id,
            'last_read_at'    => now(),
        ]);

        // Create 2 unread messages from driver to passenger
        Message::create([
            'conversation_id' => $conv->id,
            'sender_id'       => $driver->id,
            'body'            => 'Hello there!',
            'created_at'      => now()->subMinutes(10),
        ]);
        Message::create([
            'conversation_id' => $conv->id,
            'sender_id'       => $driver->id,
            'body'            => 'Are you available?',
            'created_at'      => now()->subMinutes(5),
        ]);

        $response = $this->actingAs($passenger)->get('/');
        $response->assertOk();

        $html = $response->getContent();

        // Must show unread badge count
        $this->assertStringContainsString('Messages', $html);
        $this->assertStringContainsString('2', $html);
    }
}
