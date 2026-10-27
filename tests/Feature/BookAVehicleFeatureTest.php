<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Route;
use App\Models\Stop;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleUnavailability;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookAVehicleFeatureTest extends TestCase
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
            'name'        => 'Sarah Passenger',
            'email'       => 'sarah@example.com',
            'user_type'   => 0, // Passenger
            'phone'       => '03001234567',
        ]);

        // 2. Create Approved Driver
        $this->driver = User::factory()->create([
            'name'          => 'Ahmed Khan Driver',
            'email'         => 'driver.ahmed@example.com',
            'user_type'     => 2, // Driver
            'driver_status' => 'approved',
            'phone'         => '03119876543',
        ]);

        // 3. Create Approved Vehicle
        $this->vehicle = Vehicle::create([
            'user_id'                 => $this->driver->id,
            'type'                    => 'Van',
            'registration_no'         => 'ICT-7890',
            'model'                   => 'Toyota HiAce Grand Cabin',
            'total_seats'             => 14,
            'rental_rate_per_day'     => 5000.00,
            'is_available_for_rental' => true,
            'status'                  => 'approved',
        ]);
    }

    /** 1. Test Navbar Book a Vehicle Link & Active State */
    public function test_navbar_displays_book_a_vehicle_and_highlights_active_state(): void
    {
        $response = $this->get('/book-vehicle');
        $response->assertOk();

        $html = $response->getContent();

        // Must contain Book a Vehicle
        $this->assertStringContainsString('Book a Vehicle', $html);

        // Active indicator on Book a Vehicle link
        $this->assertMatchesRegularExpression(
            '/Book a Vehicle\s+<span class="absolute bottom-0/',
            $html
        );

        // Home and Trips must not be active on /book-vehicle
        $this->assertDoesNotMatchRegularExpression(
            '/Home\s+<span class="absolute bottom-0/',
            $html
        );
        $this->assertDoesNotMatchRegularExpression(
            '/Search Trips\s+<span class="absolute bottom-0/',
            $html
        );
    }

    /** 2. Test Book a Vehicle page renders with mode selectors and search form */
    public function test_book_a_vehicle_page_renders_with_modes_and_search_inputs(): void
    {
        $response = $this->get('/book-vehicle');
        $response->assertOk();

        $response->assertSee('Book a Vehicle');
        $response->assertSee('Find the right ride for your journey.');
        $response->assertSee('Private Vehicle');
        $response->assertSee('Shared Ride');
        $response->assertSee('Search Available Vehicles');
        $response->assertSee($this->vehicle->model);
        $response->assertSee($this->vehicle->registration_no);
    }

    /** 3. Test Private Vehicle Multi-Day Rental Price Calculation */
    public function test_private_vehicle_multi_day_price_calculation(): void
    {
        $startDate = Carbon::today()->addDays(2)->format('Y-m-d');
        $endDate   = Carbon::today()->addDays(5)->format('Y-m-d'); // 3 days

        $response = $this->get("/book-vehicle?mode=private&pickup_date={$startDate}&pickup_time=09:00&return_date={$endDate}&return_time=09:00");
        $response->assertOk();

        // 3 days * 5000 = 15,000
        $response->assertSee('Rs. 15,000.00');
        $response->assertSee('3 days');
    }

    /** 4. Test Unapproved Vehicle is NOT shown in rental results */
    public function test_unapproved_vehicle_is_excluded_from_search(): void
    {
        $pendingVehicle = Vehicle::create([
            'user_id'                 => $this->driver->id,
            'type'                    => 'Car',
            'registration_no'         => 'PENDING-123',
            'model'                   => 'Honda Civic',
            'total_seats'             => 4,
            'rental_rate_per_day'     => 4000.00,
            'is_available_for_rental' => true,
            'status'                  => 'pending',
        ]);

        $response = $this->get('/book-vehicle?mode=private');
        $response->assertOk();

        $response->assertSee($this->vehicle->registration_no);
        $response->assertDontSee($pendingVehicle->registration_no);
    }

    /** 5. Test Successful Private Vehicle Booking */
    public function test_authenticated_passenger_can_book_private_vehicle(): void
    {
        $startDate = Carbon::today()->addDays(3)->format('Y-m-d');
        $endDate   = Carbon::today()->addDays(5)->format('Y-m-d');

        $response = $this->actingAs($this->passenger)->post("/book-vehicle/{$this->vehicle->id}/reserve", [
            'pickup_location'      => 'Islamabad F-10 Markaz',
            'destination_location' => 'Nathia Gali Main Bazaar',
            'pickup_date'          => $startDate,
            'pickup_time'          => '08:00',
            'return_date'          => $endDate,
            'return_time'          => '18:00',
            'passenger_notes'      => 'Family trip with 4 bags.',
        ]);

        $booking = Booking::where('user_id', $this->passenger->id)
            ->where('vehicle_id', $this->vehicle->id)
            ->first();

        $this->assertNotNull($booking);
        $this->assertEquals('private', $booking->booking_type);
        $this->assertEquals('confirmed', $booking->booking_status);
        $this->assertStringStartsWith('RC-PV-', $booking->booking_reference);
        $this->assertEquals('Islamabad F-10 Markaz', $booking->pickup_location);
        $this->assertEquals('Nathia Gali Main Bazaar', $booking->destination_location);
        $this->assertGreaterThan(0, $booking->total_fare);

        $response->assertRedirect(route('booking.confirmation', $booking->id));
    }

    /** 6. Test Overlapping Private Booking Conflict Prevention */
    public function test_overlapping_private_vehicle_booking_is_prevented(): void
    {
        $start = Carbon::today()->addDays(5)->setTime(10, 0);
        $end   = Carbon::today()->addDays(8)->setTime(18, 0);

        // Create first confirmed reservation
        Booking::create([
            'user_id'              => $this->passenger->id,
            'vehicle_id'           => $this->vehicle->id,
            'trip_id'              => null,
            'booking_type'         => 'private',
            'pickup_location'      => 'Rawalpindi',
            'destination_location' => 'Lahore',
            'start_datetime'       => $start,
            'end_datetime'         => $end,
            'days'                 => 3,
            'daily_rate'           => 5000.00,
            'total_fare'           => 15000.00,
            'booking_reference'    => 'RC-PV-TEST-0001',
            'booking_status'       => 'confirmed',
            'status'               => 'confirmed',
        ]);

        // Second passenger attempts to book overlapping range: Day 6 to Day 7
        $overlapPassenger = User::factory()->create([
            'user_type' => 0,
        ]);

        $response = $this->actingAs($overlapPassenger)->post("/book-vehicle/{$this->vehicle->id}/reserve", [
            'pickup_location'      => 'Islamabad',
            'destination_location' => 'Murree',
            'pickup_date'          => Carbon::today()->addDays(6)->format('Y-m-d'),
            'pickup_time'          => '09:00',
            'return_date'          => Carbon::today()->addDays(7)->format('Y-m-d'),
            'return_time'          => '17:00',
        ]);

        // Must fail with validation error mentioning conflict
        $response->assertSessionHasErrors('pickup_date');
        $this->assertEquals(1, Booking::where('vehicle_id', $this->vehicle->id)->count());
    }

    /** 7. Test Driver Blocked Date Conflict Prevention */
    public function test_driver_blocked_dates_prevent_booking(): void
    {
        $blockStart = Carbon::today()->addDays(10)->setTime(0, 0);
        $blockEnd   = Carbon::today()->addDays(12)->setTime(23, 59);

        // Driver blocks vehicle for maintenance
        VehicleUnavailability::create([
            'vehicle_id' => $this->vehicle->id,
            'start_date' => $blockStart,
            'end_date'   => $blockEnd,
            'reason'     => 'Transmission servicing',
        ]);

        // Passenger attempts to reserve during blocked dates
        $response = $this->actingAs($this->passenger)->post("/book-vehicle/{$this->vehicle->id}/reserve", [
            'pickup_location'      => 'Islamabad',
            'destination_location' => 'Peshawar',
            'pickup_date'          => Carbon::today()->addDays(10)->format('Y-m-d'),
            'pickup_time'          => '10:00',
            'return_date'          => Carbon::today()->addDays(11)->format('Y-m-d'),
            'return_time'          => '18:00',
        ]);

        $response->assertSessionHasErrors('pickup_date');
        $this->assertEquals(0, Booking::where('vehicle_id', $this->vehicle->id)->count());
    }

    /** 8. Test Passenger Booking History on /booking with Real Data & Tabs */
    public function test_booking_history_page_displays_real_passenger_data_and_tabs(): void
    {
        // Create an upcoming private booking
        $booking = Booking::create([
            'user_id'              => $this->passenger->id,
            'vehicle_id'           => $this->vehicle->id,
            'trip_id'              => null,
            'booking_type'         => 'private',
            'pickup_location'      => 'Airport Terminal 1',
            'destination_location' => 'Serena Hotel Islamabad',
            'start_datetime'       => Carbon::today()->addDays(3)->setTime(10, 0),
            'end_datetime'         => Carbon::today()->addDays(4)->setTime(18, 0),
            'days'                 => 1,
            'daily_rate'           => 5000.00,
            'total_fare'           => 5000.00,
            'booking_reference'    => 'RC-PV-HIST-9999',
            'booking_status'       => 'confirmed',
            'status'               => 'confirmed',
        ]);

        // Passenger visits /booking
        $response = $this->actingAs($this->passenger)->get('/booking');
        $response->assertOk();

        $response->assertSee('RC-PV-HIST-9999');
        $response->assertSee('Private Rental');
        $response->assertSee('Airport Terminal 1');
        $response->assertSee('Serena Hotel Islamabad');
        $response->assertSee('Rs. 5,000.00');
        $response->assertSee('Confirmed');
        $response->assertSee('Upcoming');
    }

    /** 9. Test Passenger Cancellation of Private Booking */
    public function test_passenger_can_cancel_private_booking(): void
    {
        $booking = Booking::create([
            'user_id'              => $this->passenger->id,
            'vehicle_id'           => $this->vehicle->id,
            'trip_id'              => null,
            'booking_type'         => 'private',
            'pickup_location'      => 'Start Point',
            'destination_location' => 'End Point',
            'start_datetime'       => Carbon::today()->addDays(4)->setTime(10, 0),
            'end_datetime'         => Carbon::today()->addDays(5)->setTime(18, 0),
            'days'                 => 1,
            'daily_rate'           => 5000.00,
            'total_fare'           => 5000.00,
            'booking_reference'    => 'RC-PV-CANCEL-1234',
            'booking_status'       => 'confirmed',
            'status'               => 'confirmed',
        ]);

        $response = $this->actingAs($this->passenger)->post("/my-bookings/{$booking->id}/cancel");
        $response->assertSessionHas('success');

        $this->assertEquals('cancelled', $booking->fresh()->booking_status);
    }

    /** 10. Test Driver Can Manage Rental Rate and Block Dates */
    public function test_driver_can_configure_rental_rate_and_block_dates(): void
    {
        // 1. Update rental settings
        $settingsRes = $this->actingAs($this->driver)->post("/driver/vehicles/{$this->vehicle->id}/rental-settings", [
            'rental_rate_per_day'     => 6500.00,
            'is_available_for_rental' => 1,
            'description'             => 'Clean, AC-equipped family van.',
        ]);
        $settingsRes->assertSessionHas('success');
        $this->assertEquals(6500.00, $this->vehicle->fresh()->rental_rate_per_day);

        // 2. Add blocked period
        $blockRes = $this->actingAs($this->driver)->post("/driver/vehicles/{$this->vehicle->id}/unavailabilities", [
            'start_date' => Carbon::today()->addDays(7)->format('Y-m-d H:i:s'),
            'end_date'   => Carbon::today()->addDays(9)->format('Y-m-d H:i:s'),
            'reason'     => 'Personal driver vacation',
        ]);
        $blockRes->assertSessionHas('success');
        $this->assertEquals(1, $this->vehicle->unavailabilities()->count());

        // 3. Remove blocked period
        $unavailability = $this->vehicle->unavailabilities()->first();
        $destroyRes = $this->actingAs($this->driver)->delete("/driver/vehicles/{$this->vehicle->id}/unavailabilities/{$unavailability->id}");
        $destroyRes->assertSessionHas('success');
        $this->assertEquals(0, $this->vehicle->unavailabilities()->count());
    }

    /** 11. Test Empty State on /booking when Passenger has no bookings */
    public function test_empty_state_shows_search_trips_and_book_vehicle_buttons(): void
    {
        $newPassenger = User::factory()->create(['user_type' => 0]);

        $response = $this->actingAs($newPassenger)->get('/booking');
        $response->assertOk();

        $response->assertSee('No Bookings Found');
        $response->assertSee('Search Scheduled Trips');
        $response->assertSee('Book a Vehicle');
    }
}
