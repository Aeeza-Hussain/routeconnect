<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Route;
use App\Models\Stop;
use App\Models\Trip;
use App\Models\TravelRequest;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FindARideUnifiedPageTest extends TestCase
{
    use RefreshDatabase;

    private User $passenger;
    private User $driver;
    private Vehicle $vehicle;
    private Trip $trip;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create Passenger
        $this->passenger = User::factory()->create([
            'name'      => 'Ayla Passenger',
            'email'     => 'ayla@example.com',
            'user_type' => 0, // Passenger
        ]);

        // 2. Create Driver
        $this->driver = User::factory()->create([
            'name'          => 'Farhan Driver',
            'email'         => 'farhan@example.com',
            'user_type'     => 2, // Driver
            'driver_status' => 'approved',
        ]);

        // 3. Create Vehicles: One for Scheduled Trips, One for Private Hire
        $this->vehicle = Vehicle::create([
            'user_id'                 => $this->driver->id,
            'type'                    => 'Van',
            'registration_no'         => 'RWP-1122',
            'model'                   => 'Toyota HiAce GL',
            'total_seats'             => 14,
            'rental_rate_per_day'     => 6000.00,
            'is_available_for_rental' => true,
            'status'                  => 'approved',
        ]);

        $tripVehicle = Vehicle::create([
            'user_id'                 => $this->driver->id,
            'type'                    => 'Bus',
            'registration_no'         => 'ISB-3344',
            'model'                   => 'Coaster Deluxe',
            'total_seats'             => 28,
            'status'                  => 'approved',
        ]);

        // 4. Create Scheduled Route & Trip
        $startStop = Stop::create(['name' => 'Islamabad Stop', 'location' => 'Faizabad', 'status' => 'Active']);
        $endStop   = Stop::create(['name' => 'Murree Stop', 'location' => 'Mall Road', 'status' => 'Active']);

        $route = Route::create([
            'name'           => 'Islamabad - Murree Expressway',
            'start_location' => 'Islamabad',
            'end_location'   => 'Murree',
            'start_stop_id'  => $startStop->id,
            'end_stop_id'    => $endStop->id,
            'status'         => 'Active',
        ]);

        $this->trip = Trip::create([
            'user_id'         => $this->driver->id,
            'vehicle_id'      => $tripVehicle->id,
            'route_id'        => $route->id,
            'trip_date'       => Carbon::today()->addDay()->format('Y-m-d'),
            'departure_time'  => '10:00:00',
            'available_seats' => 8,
            'fare'            => 1500.00,
            'pickup_point'    => 'Faizabad Bus Terminal',
            'status'          => 'scheduled',
        ]);
    }

    /** 1. Public user can access /find-a-ride and view the three clear options */
    public function test_public_user_can_access_find_a_ride_page_with_three_options(): void
    {
        $response = $this->get('/find-a-ride');
        $response->assertStatus(200);

        // Heading & Unified Marketplace
        $response->assertSee('Find a Ride');
        $response->assertSee('Public Ride Marketplace');

        // Three Clear Options
        $response->assertSee('Book a Seat');
        $response->assertSee('Book Entire Vehicle');
        $response->assertSee('Post a Travel Request');

        // Real Scheduled Trip Data
        $response->assertSee('Islamabad');
        $response->assertSee('Murree');
        $response->assertSee('Farhan Driver');
        $response->assertSee('8 Open Seats');
    }

    /** 2. Option 2: Book Entire Vehicle renders real vehicles and pricing */
    public function test_book_entire_vehicle_tab_displays_real_vehicles_and_pricing(): void
    {
        $response = $this->get('/find-a-ride?tab=vehicle');
        $response->assertStatus(200);

        $response->assertSee('Book Entire Vehicle');
        $response->assertSee('Toyota HiAce GL');
        $response->assertSee('RWP-1122');
        $response->assertSee('6,000.00'); // Daily rental rate
        $response->assertSee('14 Seats');
        $response->assertSee('Book Now');
    }

    /** 3. Option 3: Post a Travel Request renders form and community feed */
    public function test_post_travel_request_tab_renders_publish_form(): void
    {
        // Create an existing open travel request
        TravelRequest::create([
            'user_id'              => $this->passenger->id,
            'pickup_location'      => 'Islamabad Blue Area',
            'destination_location' => 'Abbottabad Mandian',
            'travel_date'          => Carbon::today()->addDays(2)->format('Y-m-d'),
            'travel_time'          => '08:00 AM',
            'seats_needed'         => 3,
            'budget'               => 3500.00,
            'notes'                => 'Traveling with family, light luggage',
            'status'               => 'open',
        ]);

        $response = $this->get('/find-a-ride?tab=request');
        $response->assertStatus(200);

        $response->assertSee('Post a Travel Request');
        $response->assertSee('Live Community Travel Requests');
        $response->assertSee('Islamabad Blue Area');
        $response->assertSee('Abbottabad Mandian');
    }

    /** 4. Guests cannot submit a travel request without login */
    public function test_guest_cannot_submit_travel_request(): void
    {
        $response = $this->post('/travel-requests', [
            'pickup_location'      => 'Rawalpindi Saddar',
            'destination_location' => 'Nathia Gali',
            'travel_date'          => Carbon::today()->addDays(3)->format('Y-m-d'),
            'travel_time'          => '09:00 AM',
            'seats_needed'         => 2,
            'budget'               => 4000.00,
        ]);

        // Must redirect to login
        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('travel_requests', [
            'pickup_location' => 'Rawalpindi Saddar',
        ]);
    }

    /** 5. Authenticated passenger can post a travel request from the page */
    public function test_authenticated_passenger_can_publish_travel_request(): void
    {
        $response = $this->actingAs($this->passenger)->post('/travel-requests', [
            'pickup_location'      => 'Islamabad F-10',
            'destination_location' => 'Gilgit City',
            'travel_date'          => Carbon::today()->addDays(4)->format('Y-m-d'),
            'travel_time'          => '06:00 AM',
            'seats_needed'         => 4,
            'budget'               => 12000.00,
            'notes'                => 'Need comfortable van for 4 passengers',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('travel_requests', [
            'user_id'              => $this->passenger->id,
            'pickup_location'      => 'Islamabad F-10',
            'destination_location' => 'Gilgit City',
            'seats_needed'         => 4,
            'budget'               => 12000.00,
            'status'               => 'open',
        ]);
    }

    /** 6. Navbar does NOT show separate Book a Vehicle or My Bookings */
    public function test_navbar_does_not_contain_separate_book_vehicle_or_my_bookings_links(): void
    {
        // As Guest
        $guestResponse = $this->get('/find-a-ride');
        $guestHtml = $guestResponse->getContent();
        
        // Extract navbar markup between header tags
        preg_match('/<header[^>]*id="siteNavbar"[^>]*>(.*?)<\/header>/s', $guestHtml, $guestNavMatches);
        $guestNavHtml = $guestNavMatches[1] ?? $guestHtml;

        $this->assertStringNotContainsString('Book a Vehicle', $guestNavHtml);
        $this->assertStringNotContainsString('My Bookings', $guestNavHtml);
        $this->assertStringContainsString('Find a Ride', $guestNavHtml);
        $this->assertStringContainsString('About', $guestNavHtml);
        $this->assertStringContainsString('Login', $guestNavHtml);
        $this->assertStringContainsString('Register', $guestNavHtml);

        // As Authenticated Passenger
        $authResponse = $this->actingAs($this->passenger)->get('/find-a-ride');
        $authHtml = $authResponse->getContent();

        preg_match('/<header[^>]*id="siteNavbar"[^>]*>(.*?)<\/header>/s', $authHtml, $authNavMatches);
        $authNavHtml = $authNavMatches[1] ?? $authHtml;

        $this->assertStringNotContainsString('Book a Vehicle', $authNavHtml);
        $this->assertStringNotContainsString('My Bookings', $authNavHtml);
        $this->assertStringContainsString('Find a Ride', $authNavHtml);
        $this->assertStringContainsString('My Activity', $authNavHtml);
        $this->assertStringContainsString('Messages', $authNavHtml);
    }
}
