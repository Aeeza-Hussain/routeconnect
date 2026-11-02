<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Route;
use App\Models\RouteStop;
use App\Models\Stop;
use App\Models\Trip;
use App\Models\TripStop;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleUnavailability;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PassengerBookingSystemTest extends TestCase
{
    use RefreshDatabase;

    private User $passenger1;
    private User $passenger2;
    private User $driver;
    private Vehicle $vehicle;
    private Route $route;
    private Stop $stopGilgit;
    private Stop $stopNagar;
    private Stop $stopHunza;
    private Trip $trip;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Passengers
        $this->passenger1 = User::factory()->create([
            'user_type' => 0,
            'name'      => 'Amina Khan',
            'email'     => 'amina@passenger.com',
        ]);

        $this->passenger2 = User::factory()->create([
            'user_type' => 0,
            'name'      => 'Tariq Mehmood',
            'email'     => 'tariq@passenger.com',
        ]);

        // 2. Approved Driver
        $this->driver = User::factory()->create([
            'user_type'     => 2,
            'driver_status' => 'approved',
            'name'          => 'Rashid Driver',
            'email'         => 'rashid@driver.com',
        ]);

        // 3. Approved Fleet Vehicle
        $this->vehicle = Vehicle::factory()->create([
            'user_id'                 => $this->driver->id,
            'registration_no'         => 'GLT-4400',
            'model'                   => 'Toyota HiAce Grand Cabin',
            'type'                    => 'Van',
            'total_seats'             => 14,
            'rental_rate_per_day'     => 5000,
            'rental_rate_per_hour'    => 500,
            'pickup_location'         => 'Gilgit Terminal',
            'service_area'            => 'Gilgit, Nagar, Hunza',
            'is_available_for_rental' => true,
            'status'                  => 'approved',
        ]);

        // 4. Stops: Gilgit -> Nagar -> Hunza
        $this->stopGilgit = Stop::create(['name' => 'Gilgit Terminal', 'location' => 'Gilgit', 'status' => 'Active']);
        $this->stopNagar  = Stop::create(['name' => 'Nagar Chowk',     'location' => 'Nagar',  'status' => 'Active']);
        $this->stopHunza  = Stop::create(['name' => 'Hunza Bazaar',    'location' => 'Hunza',  'status' => 'Active']);

        // 5. Route with intermediate stop
        $this->route = Route::create([
            'name'           => 'Karakoram Highway Express',
            'start_location' => 'Gilgit Terminal',
            'end_location'   => 'Hunza Bazaar',
            'start_stop_id'  => $this->stopGilgit->id,
            'end_stop_id'    => $this->stopHunza->id,
            'status'         => 'Active',
        ]);

        RouteStop::create(['route_id' => $this->route->id, 'stop_id' => $this->stopGilgit->id, 'stop_order' => 1]);
        RouteStop::create(['route_id' => $this->route->id, 'stop_id' => $this->stopNagar->id,  'stop_order' => 2]);
        RouteStop::create(['route_id' => $this->route->id, 'stop_id' => $this->stopHunza->id,  'stop_order' => 3]);

        // 6. Scheduled Trip
        $this->trip = Trip::create([
            'user_id'               => $this->driver->id,
            'vehicle_id'            => $this->vehicle->id,
            'route_id'              => $this->route->id,
            'trip_date'             => Carbon::tomorrow()->format('Y-m-d'),
            'departure_time'        => '09:00:00',
            'expected_arrival_time' => '13:00:00',
            'total_seats'           => 14,
            'available_seats'       => 14,
            'fare'                  => 1200,
            'pickup_point'          => 'Gilgit Central Bus Stand',
            'status'                => 'scheduled',
        ]);

        TripStop::create(['trip_id' => $this->trip->id, 'stop_id' => $this->stopGilgit->id, 'stop_order' => 1, 'expected_time' => '09:00']);
        TripStop::create(['trip_id' => $this->trip->id, 'stop_id' => $this->stopNagar->id,  'stop_order' => 2, 'expected_time' => '10:45']);
        TripStop::create(['trip_id' => $this->trip->id, 'stop_id' => $this->stopHunza->id,  'stop_order' => 3, 'expected_time' => '13:00']);
    }

    /** ─────────────────────────────────────────────────────────────
     * 1. SHARED SEAT BOOKING & SEGMENT-BASED SEAT CALCULATION
     * ───────────────────────────────────────────────────────────── */
    public function test_seat_availability_is_calculated_per_route_segment(): void
    {
        // Initially, all segments have 14 seats
        $this->assertEquals(14, $this->trip->getSegmentAvailableSeats($this->stopGilgit->id, $this->stopNagar->id));
        $this->assertEquals(14, $this->trip->getSegmentAvailableSeats($this->stopNagar->id, $this->stopHunza->id));
        $this->assertEquals(14, $this->trip->getSegmentAvailableSeats($this->stopGilgit->id, $this->stopHunza->id));

        // Passenger 1 books 4 seats for Nagar -> Hunza
        $response = $this->actingAs($this->passenger1)->post(route('booking.store', $this->trip->id), [
            'from_stop_id' => $this->stopNagar->id,
            'to_stop_id'   => $this->stopHunza->id,
            'seats'        => 4,
        ]);

        $response->assertSessionHasNoErrors();
        $this->trip->refresh();

        // Nagar -> Hunza has 10 seats remaining (14 - 4)
        $this->assertEquals(10, $this->trip->getSegmentAvailableSeats($this->stopNagar->id, $this->stopHunza->id));

        // Gilgit -> Nagar is COMPLETELY unaffected (still has 14 seats available!)
        $this->assertEquals(14, $this->trip->getSegmentAvailableSeats($this->stopGilgit->id, $this->stopNagar->id));

        // Gilgit -> Hunza (through journey) is capped at the bottleneck segment (min(14, 10) = 10)
        $this->assertEquals(10, $this->trip->getSegmentAvailableSeats($this->stopGilgit->id, $this->stopHunza->id));
    }

    public function test_passenger_can_book_unoccupied_segment_independently(): void
    {
        // Passenger 1 books 10 seats for Nagar -> Hunza
        $this->actingAs($this->passenger1)->post(route('booking.store', $this->trip->id), [
            'from_stop_id' => $this->stopNagar->id,
            'to_stop_id'   => $this->stopHunza->id,
            'seats'        => 10,
        ]);

        // Passenger 2 books 14 seats for Gilgit -> Nagar (full capacity on that segment)
        $response = $this->actingAs($this->passenger2)->post(route('booking.store', $this->trip->id), [
            'from_stop_id' => $this->stopGilgit->id,
            'to_stop_id'   => $this->stopNagar->id,
            'seats'        => 14,
        ]);

        $response->assertSessionHasNoErrors();
        $this->trip->refresh();

        // Gilgit -> Nagar is now fully booked (0 seats)
        $this->assertEquals(0, $this->trip->getSegmentAvailableSeats($this->stopGilgit->id, $this->stopNagar->id));

        // Nagar -> Hunza still has 4 seats available (14 - 10)
        $this->assertEquals(4, $this->trip->getSegmentAvailableSeats($this->stopNagar->id, $this->stopHunza->id));

        // Gilgit -> Hunza has 0 seats available
        $this->assertEquals(0, $this->trip->getSegmentAvailableSeats($this->stopGilgit->id, $this->stopHunza->id));
    }

    public function test_overbooking_on_a_specific_segment_is_prevented(): void
    {
        // Passenger 1 books 12 seats on Nagar -> Hunza
        $this->actingAs($this->passenger1)->post(route('booking.store', $this->trip->id), [
            'from_stop_id' => $this->stopNagar->id,
            'to_stop_id'   => $this->stopHunza->id,
            'seats'        => 12,
        ]);

        // Passenger 2 attempts to book 5 seats on Nagar -> Hunza (only 2 left)
        $response = $this->actingAs($this->passenger2)->post(route('booking.store', $this->trip->id), [
            'from_stop_id' => $this->stopNagar->id,
            'to_stop_id'   => $this->stopHunza->id,
            'seats'        => 5,
        ]);

        $response->assertSessionHasErrors(['seats']);
        $this->assertEquals(1, Booking::where('trip_id', $this->trip->id)->count());
    }

    public function test_booking_fails_if_boarding_stop_comes_after_dropoff_stop(): void
    {
        // Passenger attempts backward travel: Hunza -> Gilgit
        $response = $this->actingAs($this->passenger1)->post(route('booking.store', $this->trip->id), [
            'from_stop_id' => $this->stopHunza->id,
            'to_stop_id'   => $this->stopGilgit->id,
            'seats'        => 2,
        ]);

        $response->assertSessionHasErrors(['from_stop_id']);
        $this->assertEquals(0, Booking::where('trip_id', $this->trip->id)->count());
    }

    public function test_passenger_can_select_and_persist_specific_seat_numbers(): void
    {
        $response = $this->actingAs($this->passenger1)->post(route('booking.store', $this->trip->id), [
            'from_stop_id'    => $this->stopGilgit->id,
            'to_stop_id'      => $this->stopHunza->id,
            'seats'           => 2,
            'seat_numbers'    => '3, 4',
            'passenger_notes' => 'Near window',
        ]);

        $response->assertSessionHasNoErrors();
        $booking = Booking::where('trip_id', $this->trip->id)->first();
        $this->assertNotNull($booking);
        $this->assertEquals('3, 4', $booking->seat_numbers);
        $this->assertEquals('Near window', $booking->passenger_notes);
        $this->assertEquals(2400, (float) $booking->total_fare);
    }

    /** ─────────────────────────────────────────────────────────────
     * 2. PRIVATE VEHICLE BOOKING & INTERVAL BLOCKING
     * ───────────────────────────────────────────────────────────── */
    public function test_passenger_can_search_and_reserve_private_vehicle(): void
    {
        $startDate = Carbon::tomorrow()->addDays(2)->setTime(9, 0);
        $endDate   = Carbon::tomorrow()->addDays(4)->setTime(18, 0);

        $response = $this->actingAs($this->passenger1)->post(route('vehicles.book.reserve', $this->vehicle->id), [
            'pickup_location'      => 'Gilgit Airport Terminal',
            'destination_location' => 'Hunza Serena Inn',
            'pickup_date'          => $startDate->format('Y-m-d'),
            'pickup_time'          => $startDate->format('H:i'),
            'return_date'          => $endDate->format('Y-m-d'),
            'return_time'          => $endDate->format('H:i'),
            'passenger_notes'      => 'Family trip with luggage',
        ]);

        $response->assertSessionHasNoErrors();

        $booking = Booking::where('vehicle_id', $this->vehicle->id)->where('booking_type', 'private')->first();
        $this->assertNotNull($booking);
        $this->assertEquals($this->passenger1->id, $booking->user_id);
        $this->assertEquals($this->driver->id, $booking->driver_id);
        $this->assertEquals('private', $booking->booking_type);
        $this->assertEquals(14, $booking->seats);
        $this->assertTrue($booking->isConfirmed());
        $this->assertEquals(3, $booking->days); // 3 calendar rental days
        $this->assertEquals(15000, (float) $booking->total_fare); // 3 * 5000
    }

    public function test_overlapping_private_vehicle_booking_is_prevented(): void
    {
        $startDate = Carbon::tomorrow()->addDays(3)->setTime(10, 0);
        $endDate   = Carbon::tomorrow()->addDays(5)->setTime(17, 0);

        // Pre-existing reservation
        Booking::create([
            'booking_reference'    => 'RC-PV-EXST-01',
            'user_id'              => $this->passenger1->id,
            'driver_id'            => $this->driver->id,
            'vehicle_id'           => $this->vehicle->id,
            'booking_type'         => 'private',
            'pickup_location'      => 'Gilgit',
            'destination_location' => 'Hunza',
            'start_datetime'       => $startDate,
            'end_datetime'         => $endDate,
            'days'                 => 3,
            'daily_rate'           => 5000,
            'total_fare'           => 15000,
            'seats'                => 14,
            'booking_status'       => 'confirmed',
            'status'               => 'confirmed',
        ]);

        // Passenger 2 attempts overlapping reservation on same vehicle (Day 4 to Day 6)
        $conflictStart = Carbon::tomorrow()->addDays(4)->setTime(9, 0);
        $conflictEnd   = Carbon::tomorrow()->addDays(6)->setTime(18, 0);

        $response = $this->actingAs($this->passenger2)->post(route('vehicles.book.reserve', $this->vehicle->id), [
            'pickup_location'      => 'Gilgit Serena',
            'destination_location' => 'Skardu Shangrila',
            'pickup_date'          => $conflictStart->format('Y-m-d'),
            'pickup_time'          => $conflictStart->format('H:i'),
            'return_date'          => $conflictEnd->format('Y-m-d'),
            'return_time'          => $conflictEnd->format('H:i'),
        ]);

        $response->assertSessionHasErrors(['pickup_date']);
        $this->assertEquals(1, Booking::where('vehicle_id', $this->vehicle->id)->count());
    }

    public function test_private_booking_prevented_if_driver_blocked_maintenance_dates(): void
    {
        $blockStart = Carbon::tomorrow()->addDays(7)->setTime(8, 0);
        $blockEnd   = Carbon::tomorrow()->addDays(8)->setTime(18, 0);

        $this->vehicle->unavailabilities()->create([
            'start_date' => $blockStart,
            'end_date'   => $blockEnd,
            'reason'     => 'Scheduled vehicle inspection',
        ]);

        // Attempt reservation overlapping with blocked dates
        $response = $this->actingAs($this->passenger1)->post(route('vehicles.book.reserve', $this->vehicle->id), [
            'pickup_location'      => 'Gilgit',
            'destination_location' => 'Hunza',
            'pickup_date'          => $blockStart->format('Y-m-d'),
            'pickup_time'          => '10:00',
            'return_date'          => $blockEnd->format('Y-m-d'),
            'return_time'          => '15:00',
        ]);

        $response->assertSessionHasErrors(['pickup_date']);
        $this->assertEquals(0, Booking::where('vehicle_id', $this->vehicle->id)->count());
    }

    /** ─────────────────────────────────────────────────────────────
     * 3. CANCELLATION & AVAILABILITY RESTORATION
     * ───────────────────────────────────────────────────────────── */
    public function test_cancelling_shared_booking_restores_segment_availability(): void
    {
        // Passenger 1 books 5 seats on Nagar -> Hunza
        $this->actingAs($this->passenger1)->post(route('booking.store', $this->trip->id), [
            'from_stop_id' => $this->stopNagar->id,
            'to_stop_id'   => $this->stopHunza->id,
            'seats'        => 5,
        ]);

        $this->trip->refresh();
        $this->assertEquals(9, $this->trip->getSegmentAvailableSeats($this->stopNagar->id, $this->stopHunza->id));

        $booking = Booking::where('trip_id', $this->trip->id)->first();

        // Passenger 1 cancels booking
        $cancelResponse = $this->actingAs($this->passenger1)->post(route('passenger.bookings.cancel', $booking->id));
        $cancelResponse->assertRedirect();

        $booking->refresh();
        $this->assertTrue($booking->isCancelled());

        // Availability on Nagar -> Hunza is restored to 14
        $this->trip->refresh();
        $this->assertEquals(14, $this->trip->getSegmentAvailableSeats($this->stopNagar->id, $this->stopHunza->id));
    }

    public function test_cancelling_private_vehicle_booking_frees_up_schedule(): void
    {
        $startDate = Carbon::tomorrow()->addDays(10)->setTime(9, 0);
        $endDate   = Carbon::tomorrow()->addDays(12)->setTime(18, 0);

        // Book private vehicle
        $this->actingAs($this->passenger1)->post(route('vehicles.book.reserve', $this->vehicle->id), [
            'pickup_location'      => 'Gilgit',
            'destination_location' => 'Hunza',
            'pickup_date'          => $startDate->format('Y-m-d'),
            'pickup_time'          => '09:00',
            'return_date'          => $endDate->format('Y-m-d'),
            'return_time'          => '18:00',
        ]);

        $booking = Booking::where('vehicle_id', $this->vehicle->id)->where('booking_type', 'private')->first();
        $this->assertFalse($this->vehicle->isAvailableForRange($startDate, $endDate));

        // Cancel reservation
        $this->actingAs($this->passenger1)->post(route('passenger.bookings.cancel', $booking->id));

        $booking->refresh();
        $this->assertTrue($booking->isCancelled());

        // Schedule is now free again
        $this->assertTrue($this->vehicle->isAvailableForRange($startDate, $endDate));
    }

    /** ─────────────────────────────────────────────────────────────
     * 4. BOOKING STATUS & AUTHENTICATION GUARDRAILS
     * ───────────────────────────────────────────────────────────── */
    public function test_booking_status_helpers_work_consistently(): void
    {
        $booking = new Booking();

        $booking->booking_status = 'confirmed';
        $this->assertTrue($booking->isConfirmed());
        $this->assertFalse($booking->isCancelled());
        $this->assertFalse($booking->isPending());

        $booking->booking_status = 'cancelled';
        $this->assertTrue($booking->isCancelled());
        $this->assertFalse($booking->isConfirmed());

        $booking->booking_status = 'pending';
        $this->assertTrue($booking->isPending());

        $booking->booking_status = 'completed';
        $this->assertTrue($booking->isCompleted());

        $booking->booking_status = 'rejected';
        $this->assertTrue($booking->isRejected());
    }

    public function test_guest_cannot_book_seats_or_private_vehicles(): void
    {
        // Guest attempting shared seat booking
        $seatResp = $this->post(route('booking.store', $this->trip->id), ['seats' => 1]);
        $seatResp->assertRedirect(route('login'));

        // Guest attempting private vehicle booking
        $privResp = $this->post(route('vehicles.book.reserve', $this->vehicle->id), [
            'pickup_location'      => 'Gilgit',
            'destination_location' => 'Hunza',
            'pickup_date'          => Carbon::tomorrow()->format('Y-m-d'),
            'pickup_time'          => '09:00',
            'return_date'          => Carbon::tomorrow()->addDay()->format('Y-m-d'),
            'return_time'          => '18:00',
        ]);
        $privResp->assertRedirect(route('login'));
    }
}

