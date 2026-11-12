<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServicesAndContactFeatureTest extends TestCase
{
    use RefreshDatabase;

    // ─────────────────────────────────────────────────────────────────────────
    // 1. Services Page Tests
    // ─────────────────────────────────────────────────────────────────────────

    public function test_guest_can_access_services_page_with_all_sections(): void
    {
        $response = $this->get('/services');

        $response->assertOk();
        $response->assertSee('Our Services');
        $response->assertSee('Smart, flexible and reliable transportation solutions for every journey.');
        $response->assertSee('Find a Ride');
        $response->assertSee('Become a Driver');

        // Verify the 4 main required service cards
        $response->assertSee('Shared Ride Booking');
        $response->assertSee('Passengers can search scheduled trips and reserve individual seats');

        $response->assertSee('Private Vehicle Booking');
        $response->assertSee('Passengers can request an entire vehicle, such as a car or van, for private transportation.');

        $response->assertSee('Travel Requests');
        $response->assertSee('Passengers can post their travel requirements when they cannot find a suitable ride.');

        $response->assertSee('Scheduled Trips');
        $response->assertSee('Approved drivers can publish trips with departure times, destinations, intermediate stops, and available seats.');

        // How It Works section
        $response->assertSee('How RouteConnect Works');
        $response->assertSee('Register or Log In');
        $response->assertSee('Find a Ride or Offer a Vehicle');
        $response->assertSee('Select a Suitable Travel Option');
        $response->assertSee('Confirm Booking');
    }

    public function test_authenticated_passenger_can_access_services_page(): void
    {
        $passenger = User::factory()->create(['user_type' => 0]);

        $response = $this->actingAs($passenger)->get('/services');

        $response->assertOk();
        $response->assertSee('Our Services');
        $response->assertSee('Shared Ride Booking');
    }

    public function test_authenticated_driver_can_access_services_page(): void
    {
        $driver = User::factory()->create(['user_type' => 2, 'driver_status' => 'approved']);

        $response = $this->actingAs($driver)->get('/services');

        $response->assertOk();
        $response->assertSee('Our Services');
        $response->assertSee('Schedule a Trip Now');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 2. Contact Us Page & Submission Tests
    // ─────────────────────────────────────────────────────────────────────────

    public function test_any_visitor_can_access_contact_us_page(): void
    {
        $response = $this->get('/contact');

        $response->assertOk();
        $response->assertSee('Get in Touch');
        $response->assertSee('Have a question or need assistance? We are here to help.');
        $response->assertSee('info@routeconnect.com');
        $response->assertSee('Gilgit-Baltistan, Pakistan');
        $response->assertSee('Send Us a Message');
        $response->assertSee('Submit Message');
    }

    public function test_authenticated_user_contact_form_prefills_name_and_email(): void
    {
        $user = User::factory()->create([
            'name'  => 'Aleeza Hussain',
            'email' => 'aleeza.hussain@example.com',
        ]);

        $response = $this->actingAs($user)->get('/contact');

        $response->assertOk();
        $response->assertSee('value="Aleeza Hussain"', false);
        $response->assertSee('value="aleeza.hussain@example.com"', false);
    }

    public function test_contact_form_submits_successfully_and_persists_to_database(): void
    {
        $payload = [
            'name'    => 'Ali Raza',
            'email'   => 'ali.raza@example.com',
            'subject' => 'Inquiry on Skardu to Gilgit routes',
            'message' => 'Hello, I wanted to inquire about weekly scheduled vans between Skardu and Gilgit.',
        ];

        $response = $this->post('/contact', $payload);

        $response->assertRedirect('/contact');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('contact_messages', [
            'name'    => 'Ali Raza',
            'email'   => 'ali.raza@example.com',
            'subject' => 'Inquiry on Skardu to Gilgit routes',
            'is_read' => false,
        ]);
    }

    public function test_authenticated_contact_submission_associates_user_id(): void
    {
        $user = User::factory()->create();

        $payload = [
            'name'    => $user->name,
            'email'   => $user->email,
            'subject' => 'Account verification question',
            'message' => 'Could you please check my driver application status?',
        ];

        $response = $this->actingAs($user)->post('/contact', $payload);

        $response->assertRedirect('/contact');

        $this->assertDatabaseHas('contact_messages', [
            'user_id' => $user->id,
            'subject' => 'Account verification question',
        ]);
    }

    public function test_contact_form_validation_rules_enforced(): void
    {
        // 1. All empty fields
        $response = $this->post('/contact', []);
        $response->assertSessionHasErrors(['name', 'email', 'subject', 'message']);

        // 2. Invalid email format
        $response2 = $this->post('/contact', [
            'name'    => 'Test User',
            'email'   => 'not-an-email',
            'subject' => 'Subject',
            'message' => 'Valid length message here',
        ]);
        $response2->assertSessionHasErrors(['email']);

        // 3. Message too short (under 5 characters)
        $response3 = $this->post('/contact', [
            'name'    => 'Test User',
            'email'   => 'user@test.com',
            'subject' => 'Subject',
            'message' => 'Hi',
        ]);
        $response3->assertSessionHasErrors(['message']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 3. Admin Contact Message Management Tests
    // ─────────────────────────────────────────────────────────────────────────

    public function test_admin_can_view_contact_messages_index_with_statistics(): void
    {
        $admin = User::factory()->create(['user_type' => 1]);

        ContactMessage::create([
            'name'    => 'Zahid Khan',
            'email'   => 'zahid@example.com',
            'subject' => 'Booking issue',
            'message' => 'I was trying to book a seat from Danyor to Aliabad.',
            'is_read' => false,
        ]);

        ContactMessage::create([
            'name'    => 'Sara Ahmed',
            'email'   => 'sara@example.com',
            'subject' => 'Private rental',
            'message' => 'Need Hiace for 3 days.',
            'is_read' => true,
        ]);

        $response = $this->actingAs($admin)->get('/admin/contact-messages');

        $response->assertOk();
        $response->assertSee('Contact Messages');
        $response->assertSee('Zahid Khan');
        $response->assertSee('Sara Ahmed');
        $response->assertSee('Booking issue');
        $response->assertSee('Private rental');
    }

    public function test_admin_can_filter_contact_messages_by_status(): void
    {
        $admin = User::factory()->create(['user_type' => 1]);

        ContactMessage::create([
            'name'    => 'Unread Sender',
            'email'   => 'unread@test.com',
            'subject' => 'Unread Subject',
            'message' => 'This is unread.',
            'is_read' => false,
        ]);

        ContactMessage::create([
            'name'    => 'Read Sender',
            'email'   => 'read@test.com',
            'subject' => 'Read Subject',
            'message' => 'This is read.',
            'is_read' => true,
        ]);

        // Filter: unread
        $responseUnread = $this->actingAs($admin)->get('/admin/contact-messages?status=unread');
        $responseUnread->assertOk();
        $responseUnread->assertSee('Unread Sender');
        $responseUnread->assertDontSee('Read Sender');

        // Filter: read
        $responseRead = $this->actingAs($admin)->get('/admin/contact-messages?status=read');
        $responseRead->assertOk();
        $responseRead->assertSee('Read Sender');
        $responseRead->assertDontSee('Unread Sender');
    }

    public function test_admin_can_view_message_details_and_it_automatically_marks_as_read(): void
    {
        $admin = User::factory()->create(['user_type' => 1]);

        $message = ContactMessage::create([
            'name'    => 'Karim Ullah',
            'email'   => 'karim@test.com',
            'subject' => 'Route inquiry for Hunza',
            'message' => 'Can drivers pick up from Nagar Khas?',
            'is_read' => false,
        ]);

        $response = $this->actingAs($admin)->get("/admin/contact-messages/{$message->id}");

        $response->assertOk();
        $response->assertSee('Message Details');
        $response->assertSee('Karim Ullah');
        $response->assertSee('Can drivers pick up from Nagar Khas?');

        // Verify status automatically updated to read
        $this->assertTrue($message->fresh()->is_read);
    }

    public function test_admin_can_toggle_message_read_and_unread_status(): void
    {
        $admin = User::factory()->create(['user_type' => 1]);

        $message = ContactMessage::create([
            'name'    => 'Farhan',
            'email'   => 'farhan@test.com',
            'subject' => 'Testing toggle',
            'message' => 'Toggle this message read state.',
            'is_read' => true,
        ]);

        // Toggle from read -> unread
        $response = $this->actingAs($admin)->patch("/admin/contact-messages/{$message->id}/toggle-read");
        $response->assertRedirect();
        $this->assertFalse($message->fresh()->is_read);

        // Toggle from unread -> read
        $response2 = $this->actingAs($admin)->patch("/admin/contact-messages/{$message->id}/toggle-read");
        $response2->assertRedirect();
        $this->assertTrue($message->fresh()->is_read);
    }

    public function test_admin_can_delete_contact_message(): void
    {
        $admin = User::factory()->create(['user_type' => 1]);

        $message = ContactMessage::create([
            'name'    => 'Spam Sender',
            'email'   => 'spam@test.com',
            'subject' => 'Spam Subject',
            'message' => 'Spam content to delete.',
            'is_read' => true,
        ]);

        $response = $this->actingAs($admin)->delete("/admin/contact-messages/{$message->id}");

        $response->assertRedirect('/admin/contact-messages');
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('contact_messages', ['id' => $message->id]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 4. Security & Role Authorization Tests
    // ─────────────────────────────────────────────────────────────────────────

    public function test_guest_cannot_access_admin_contact_messages(): void
    {
        $response = $this->get('/admin/contact-messages');
        $response->assertRedirect('/login');
    }

    public function test_passenger_cannot_access_admin_contact_messages(): void
    {
        $passenger = User::factory()->create(['user_type' => 0]);

        $response = $this->actingAs($passenger)->get('/admin/contact-messages');
        $response->assertForbidden();
    }

    public function test_driver_cannot_access_admin_contact_messages(): void
    {
        $driver = User::factory()->create(['user_type' => 2, 'driver_status' => 'approved']);

        $response = $this->actingAs($driver)->get('/admin/contact-messages');
        $response->assertForbidden();
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 5. Navigation & Sidebar Tests
    // ─────────────────────────────────────────────────────────────────────────

    public function test_admin_sidebar_contains_contact_messages_and_view_website(): void
    {
        $admin = User::factory()->create(['user_type' => 1]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertOk();
        $response->assertSee('Contact Messages');
        $response->assertSee('View Website');
    }

    public function test_navbar_contains_services_and_contact_for_all_roles(): void
    {
        // 1. Guest
        $guestResp = $this->get('/');
        $guestResp->assertOk();
        $guestResp->assertSee('Services');
        $guestResp->assertSee('Contact');

        // 2. Passenger
        $passenger = User::factory()->create(['user_type' => 0]);
        $passResp = $this->actingAs($passenger)->get('/');
        $passResp->assertOk();
        $passResp->assertSee('Services');
        $passResp->assertSee('Contact');

        // 3. Driver
        $driver = User::factory()->create(['user_type' => 2, 'driver_status' => 'approved']);
        $driverResp = $this->actingAs($driver)->get('/');
        $driverResp->assertOk();
        $driverResp->assertSee('Services');
        $driverResp->assertSee('Contact');
    }
}
