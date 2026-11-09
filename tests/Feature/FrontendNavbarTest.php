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

    // ─── Separate Role Navigation Structure Tests ─────────────────────────────

    public function test_guest_navbar_has_exact_required_structure(): void
    {
        $response = $this->get('/');
        $response->assertOk();

        $html = $response->getContent();

        // Logo
        $this->assertStringContainsString('RouteConnect', $html);

        // Required links
        $this->assertStringContainsString('Home', $html);
        $this->assertStringContainsString('Find a Ride', $html);
        $this->assertStringContainsString('About', $html);
        $this->assertStringContainsString('Login', $html);
        $this->assertStringContainsString('Register', $html);

        // Guest exclusions in navbar
        preg_match('/<header id="siteNavbar".*?<\/header>/s', $html, $matches);
        $navbarHtml = $matches[0] ?? $html;

        $this->assertStringNotContainsString('My Activity', $navbarHtml);
        $this->assertStringNotContainsString('Dashboard', $navbarHtml);
        $this->assertStringNotContainsString('Passenger Requests', $navbarHtml);
        $this->assertStringNotContainsString('messagesInboxToggleBtn', $navbarHtml);
        $this->assertStringNotContainsString('notificationsDropdownBtn', $navbarHtml);
    }

    public function test_passenger_navbar_has_exact_required_structure(): void
    {
        $passenger = User::factory()->create([
            'name'      => 'Aleeza Passenger',
            'email'     => 'passenger@routeconnect.test',
            'user_type' => 0, // Passenger
        ]);

        $response = $this->actingAs($passenger)->get('/');
        $response->assertOk();

        $html = $response->getContent();

        // Logo & Common
        $this->assertStringContainsString('RouteConnect', $html);
        $this->assertStringContainsString('Home', $html);
        $this->assertStringContainsString('Find a Ride', $html);
        $this->assertStringContainsString('My Activity', $html);

        // Message and Notification Icons
        $this->assertStringContainsString('messagesInboxToggleBtn', $html);
        $this->assertStringContainsString('notificationsDropdownBtn', $html);

        // Profile dropdown items
        $this->assertStringContainsString('profileDropdownBtn', $html);
        $this->assertStringContainsString('My Profile', $html);
        $this->assertStringContainsString('Edit Profile', $html);
        $this->assertStringContainsString('Settings', $html);
        $this->assertStringContainsString('Logout', $html);

        // Passenger should NOT see driver specific main nav items
        $this->assertStringNotContainsString('>Passenger Requests</a>', $html);
    }

    public function test_driver_navbar_has_exact_required_structure(): void
    {
        $driver = User::factory()->create([
            'name'          => 'Ahmad Driver',
            'email'         => 'driver@routeconnect.test',
            'user_type'     => 2, // Driver
            'driver_status' => 'approved',
        ]);

        $response = $this->actingAs($driver)->get('/');
        $response->assertOk();

        $html = $response->getContent();

        // Logo
        $this->assertStringContainsString('RouteConnect', $html);

        // Driver required main nav
        $this->assertStringContainsString('Home', $html);
        $this->assertStringContainsString('Dashboard', $html);
        $this->assertStringContainsString('Passenger Requests', $html);

        // Message and Notification Icons
        $this->assertStringContainsString('messagesInboxToggleBtn', $html);
        $this->assertStringContainsString('notificationsDropdownBtn', $html);

        // Profile dropdown items
        $this->assertStringContainsString('profileDropdownBtn', $html);
        $this->assertStringContainsString('My Profile', $html);
        $this->assertStringContainsString('Edit Profile', $html);
        $this->assertStringContainsString('Settings', $html);
        $this->assertStringContainsString('Logout', $html);

        // Individual dashboard sub-features must NOT clutter the main navbar
        $this->assertStringNotContainsString('>Offer a Shared Ride</a>', $html);
        $this->assertStringNotContainsString('>Offer a Private Vehicle</a>', $html);
        $this->assertStringNotContainsString('>My Listings</a>', $html);
    }

    public function test_admin_navigation_separation(): void
    {
        $admin = User::factory()->create([
            'name'      => 'System Admin',
            'email'     => 'admin@routeconnect.test',
            'user_type' => 1, // Admin
        ]);

        // Front-end page
        $response = $this->actingAs($admin)->get('/');
        $response->assertOk();

        $html = $response->getContent();

        // Shows Admin Portal shortcut, but does NOT put admin CRUD into public nav
        $this->assertStringContainsString('Admin Portal', $html);
        $this->assertStringNotContainsString('>Manage Users</a>', $html);
        $this->assertStringNotContainsString('>Manage Routes</a>', $html);
        $this->assertStringNotContainsString('>Manage Vehicles</a>', $html);

        // Admin dashboard is accessible with its separate portal
        $adminDash = $this->actingAs($admin)->get('/admin/dashboard');
        $adminDash->assertOk();
    }

    public function test_passenger_profile_edit_and_settings_pages(): void
    {
        $passenger = User::factory()->create(['user_type' => 0]);

        $this->actingAs($passenger)->get('/profile')->assertOk()->assertSee('My Profile');
        $this->actingAs($passenger)->get('/profile/edit')->assertOk()->assertSee('Edit Profile');
        $this->actingAs($passenger)->get('/settings')->assertOk()->assertSee('Account Settings');
    }
}
