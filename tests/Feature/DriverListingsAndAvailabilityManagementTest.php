<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Route;
use App\Models\Stop;
use App\Models\Trip;
use App\Models\TripStop;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehicleUnavailability;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverListingsAndAvailabilityManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $approvedDriver;
    private User $unapprovedDriver;
    private User $otherDriver;
    private User $passenger;
    private Vehicle $approvedVehicle;
    private Vehicle $pendingVehicle;
    private Vehicle $otherVehicle;
    private Route $route;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Approved Driver
        $this->approvedDriver = User::factory()->create([
            'user_type'     => 2,
            'driver_status' => 'approved',
            'name'          => 'Ahmad Khan',
            'email'         => 'ahmad@routeconnect.com',
        ]);

        // 2. Unapproved Driver (pending verification)
        $this->unapprovedDriver = User::factory()->create([
            'user_type'     => 2,
            'driver_status' => 'pending',
            'name'          => 'Tariq Pending',
            'email'         => 'tariq@routeconnect.com',
        ]);

        // 3. Other Approved Driver
        $this->otherDriver = User::factory()->create([
            'user_type'     => 2,
            'driver_status' => 'approved',
            'name'          => 'Saif Ullah',
            'email'         => 'saif@routeconnect.com',
        ]);

        // 4. Passenger User
        $this->passenger = User::factory()->create([
            'user_type'     => 0,
            'driver_status' => 'none',
            'name'          => 'Fatima Passenger',
            'email'         => 'fatima@routeconnect.com',
        ]);

        // 5. Approved Vehicle for approvedDriver
        $this->approvedVehicle = Vehicle::factory()->create([
            'user_id'                 => $this->approvedDriver->id,
            'model'                   => 'Toyota Hiace Grand Cabin',
            'type'                    => 'Van',
            'registration_no'         => 'GLT-8822',
            'total_seats'             => 14,
            'rental_rate_per_day'     => 6000,
            'rental_rate_per_hour'    => 600,
            'pickup_location'         => 'Gilgit Airport Terminal',
            'service_area'            => 'Gilgit, Hunza, Skardu',
            'is_available_for_rental' => true,
            'status'                  => 'approved',
        ]);

        // 6. Pending Vehicle for approvedDriver
        $this->pendingVehicle = Vehicle::factory()->create([
            'user_id'                 => $this->approvedDriver->id,
            'model'                   => 'Honda BR-V',
            'type'                    => 'Car',
            'registration_no'         => 'GLT-3311',
            'total_seats'             => 6,
            'rental_rate_per_day'     => 4500,
            'status'                  => 'pending',
            'is_available_for_rental' => false,
        ]);

        // 7. Vehicle belonging to otherDriver
        $this->otherVehicle = Vehicle::factory()->create([
            'user_id'                 => $this->otherDriver->id,
            'model'                   => 'Toyota Corolla GLI',
            'type'                    => 'Car',
            'registration_no'         => 'ISB-9900',
            'total_seats'             => 4,
            'rental_rate_per_day'     => 4000,
            'status'                  => 'approved',
            'is_available_for_rental' => true,
        ]);

        // 8. Pre-existing active Route
        $startStop = Stop::create(['name' => 'Gilgit Terminal', 'location' => 'Gilgit', 'status' => 'Active']);
        $endStop   = Stop::create(['name' => 'Hunza Bazaar', 'location' => 'Hunza', 'status' => 'Active']);

        $this->route = Route::create([
            'name'           => 'Gilgit to Hunza Express',
            'start_location' => 'Gilgit',
            'end_location'   => 'Hunza',
            'start_stop_id'  => $startStop->id,
            'end_stop_id'    => $endStop->id,
            'status'         => 'Active',
        ]);
    }

    /** ─────────────────────────────────────────────────────────────
     * 1. Dashboard & Navigation Verification
     * ───────────────────────────────────────────────────────────── */
    public function test_driver_dashboard_presents_both_shared_ride_and_private_vehicle_options(): void
    {
        $response = $this->actingAs($this->approvedDriver)->get(route('driver.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Offer a Shared Ride');
        $response->assertSee('Offer a Private Vehicle');
        $response->assertSee(route('driver.trips.create'));
        $response->assertSee(route('driver.vehicles.offer_private'));
    }

    public function test_driver_sidebar_contains_all_listing_and_fleet_navigation_links(): void
    {
        $response = $this->actingAs($this->approvedDriver)->get(route('driver.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Listings & Offers', false);
        $response->assertSee('Offer a Shared Ride');
        $response->assertSee('Offer a Private Vehicle');
        $response->assertSee('Shared Ride Listings');
        $response->assertSee('Private Listings');
        $response->assertSee('Fleet Vehicles');
        $response->assertSee(route('driver.trips'));
        $response->assertSee(route('driver.vehicles.private'));
        $response->assertSee(route('driver.vehicles.index'));
    }

    /** ─────────────────────────────────────────────────────────────
     * 2. Action A: Offer a Shared Ride
     * ───────────────────────────────────────────────────────────── */
    public function test_approved_driver_can_access_shared_ride_creation_form(): void
    {
        $response = $this->actingAs($this->approvedDriver)->get(route('driver.trips.create'));

        $response->assertStatus(200);
        $response->assertSee('Schedule a New Trip');
        $response->assertSee($this->approvedVehicle->registration_no);
        // Pending vehicle should not be selectable
        $response->assertDontSee($this->pendingVehicle->registration_no);
    }

    public function test_unapproved_driver_cannot_access_shared_ride_creation(): void
    {
        // Unapproved / pending driver redirected to pending approval screen
        $response = $this->actingAs($this->unapprovedDriver)->get(route('driver.trips.create'));
        $response->assertRedirect(route('driver.pending'));

        // Passenger user forbidden
        $passengerResponse = $this->actingAs($this->passenger)->get(route('driver.trips.create'));
        $passengerResponse->assertStatus(403);
    }

    public function test_approved_driver_can_successfully_publish_shared_ride_with_intermediate_stops(): void
    {
        $tripDate = Carbon::tomorrow()->format('Y-m-d');

        $payload = [
            'vehicle_id'            => $this->approvedVehicle->id,
            'route_id'              => $this->route->id,
            'trip_date'             => $tripDate,
            'departure_time'        => '08:30',
            'expected_arrival_time' => '12:00',
            'available_seats'       => 12,
            'fare'                  => 1500,
            'pickup_point'          => 'Gilgit Main Bus Stand',
            'notes'                 => 'AC available, luggage space included',
            'stops'                 => [
                ['name' => 'Nagar Chowk', 'stop_order' => 1, 'expected_time' => '10:00'],
                ['name' => 'Aliabad Gate', 'stop_order' => 2, 'expected_time' => '11:15'],
            ],
        ];

        $response = $this->actingAs($this->approvedDriver)->post(route('driver.trips.store'), $payload);

        $trip = Trip::latest('id')->first();
        $response->assertRedirect(route('driver.trips.show', $trip->id));
        $this->assertDatabaseHas('trips', [
            'user_id'         => $this->approvedDriver->id,
            'vehicle_id'      => $this->approvedVehicle->id,
            'route_id'        => $this->route->id,
            'departure_time'  => '08:30',
            'available_seats' => 12,
            'total_seats'     => 14,
            'fare'            => 1500,
            'status'          => 'scheduled',
        ]);

        $this->assertEquals(2, $trip->tripStops()->count());
    }

    public function test_driver_cannot_schedule_shared_ride_with_another_drivers_vehicle(): void
    {
        $payload = [
            'vehicle_id'            => $this->otherVehicle->id, // belongs to saif ullah
            'route_id'              => $this->route->id,
            'trip_date'             => Carbon::tomorrow()->format('Y-m-d'),
            'departure_time'        => '09:00',
            'available_seats'       => 4,
            'fare'                  => 1200,
        ];

        $response = $this->actingAs($this->approvedDriver)->post(route('driver.trips.store'), $payload);

        $response->assertSessionHasErrors(['vehicle_id']);
        $this->assertDatabaseMissing('trips', [
            'vehicle_id' => $this->otherVehicle->id,
            'user_id'    => $this->approvedDriver->id,
        ]);
    }

    public function test_driver_cannot_schedule_shared_ride_with_unapproved_vehicle(): void
    {
        $payload = [
            'vehicle_id'            => $this->pendingVehicle->id,
            'route_id'              => $this->route->id,
            'trip_date'             => Carbon::tomorrow()->format('Y-m-d'),
            'departure_time'        => '09:00',
            'available_seats'       => 4,
            'fare'                  => 1200,
        ];

        $response = $this->actingAs($this->approvedDriver)->post(route('driver.trips.store'), $payload);

        $response->assertSessionHasErrors(['vehicle_id']);
    }

    public function test_driver_cannot_set_seats_higher_than_vehicle_total_capacity(): void
    {
        $payload = [
            'vehicle_id'            => $this->approvedVehicle->id, // capacity is 14
            'route_id'              => $this->route->id,
            'trip_date'             => Carbon::tomorrow()->format('Y-m-d'),
            'departure_time'        => '09:00',
            'available_seats'       => 20, // exceeds 14!
            'fare'                  => 1200,
        ];

        $response = $this->actingAs($this->approvedDriver)->post(route('driver.trips.store'), $payload);

        $response->assertSessionHasErrors(['available_seats']);
    }

    /** ─────────────────────────────────────────────────────────────
     * 3. Action B: Offer a Private Vehicle
     * ───────────────────────────────────────────────────────────── */
    public function test_approved_driver_can_access_offer_private_vehicle_form(): void
    {
        $response = $this->actingAs($this->approvedDriver)->get(route('driver.vehicles.offer_private'));

        $response->assertStatus(200);
        $response->assertSee('Offer a Private Vehicle');
        $response->assertSee($this->approvedVehicle->registration_no);
        $response->assertDontSee($this->pendingVehicle->registration_no);
    }

    public function test_approved_driver_can_publish_private_vehicle_availability(): void
    {
        $availFrom = Carbon::tomorrow()->format('Y-m-d');
        $availTo   = Carbon::tomorrow()->addDays(10)->format('Y-m-d');

        $payload = [
            'vehicle_id'              => $this->approvedVehicle->id,
            'rental_rate_per_day'     => 7500,
            'rental_rate_per_hour'    => 750,
            'pickup_location'         => 'Gilgit Serena / Airport',
            'service_area'            => 'Gilgit, Hunza, Nagar, Skardu, Astore',
            'available_from'          => $availFrom,
            'available_to'            => $availTo,
            'is_available_for_rental' => 1,
            'description'             => 'Full AC, expert mountain driver included, child safety seats available.',
        ];

        $response = $this->actingAs($this->approvedDriver)->post(route('driver.vehicles.offer_private.store'), $payload);

        $response->assertRedirect(route('driver.vehicles.private'));
        $this->approvedVehicle->refresh();

        $this->assertEquals(7500, (float) $this->approvedVehicle->rental_rate_per_day);
        $this->assertEquals(750, (float) $this->approvedVehicle->rental_rate_per_hour);
        $this->assertEquals('Gilgit Serena / Airport', $this->approvedVehicle->pickup_location);
        $this->assertEquals('Gilgit, Hunza, Nagar, Skardu, Astore', $this->approvedVehicle->service_area);
        $this->assertTrue($this->approvedVehicle->is_available_for_rental);
    }

    public function test_driver_cannot_offer_another_drivers_vehicle_for_private_rental(): void
    {
        $payload = [
            'vehicle_id'              => $this->otherVehicle->id,
            'rental_rate_per_day'     => 5000,
            'is_available_for_rental' => 1,
        ];

        $response = $this->actingAs($this->approvedDriver)->post(route('driver.vehicles.offer_private.store'), $payload);
        $response->assertStatus(404);
    }

    public function test_driver_cannot_offer_unapproved_vehicle_for_private_rental(): void
    {
        $payload = [
            'vehicle_id'              => $this->pendingVehicle->id,
            'rental_rate_per_day'     => 5000,
            'is_available_for_rental' => 1,
        ];

        $response = $this->actingAs($this->approvedDriver)->post(route('driver.vehicles.offer_private.store'), $payload);
        $response->assertSessionHasErrors(['vehicle_id']);
    }

    /** ─────────────────────────────────────────────────────────────
     * 4. Availability Management (Toggle, Blocking Dates, Conflicts)
     * ───────────────────────────────────────────────────────────── */
    public function test_driver_can_toggle_private_rental_active_and_deactivated(): void
    {
        $this->assertTrue($this->approvedVehicle->is_available_for_rental);

        // Toggle OFF (Deactivate)
        $response = $this->actingAs($this->approvedDriver)->post(route('driver.vehicles.toggle_rental', $this->approvedVehicle->id));
        $response->assertRedirect();
        $this->approvedVehicle->refresh();
        $this->assertFalse($this->approvedVehicle->is_available_for_rental);

        // Toggle ON (Activate)
        $response = $this->actingAs($this->approvedDriver)->post(route('driver.vehicles.toggle_rental', $this->approvedVehicle->id));
        $response->assertRedirect();
        $this->approvedVehicle->refresh();
        $this->assertTrue($this->approvedVehicle->is_available_for_rental);
    }

    public function test_driver_can_block_and_unblock_unavailable_dates(): void
    {
        $start = Carbon::tomorrow()->setTime(8, 0);
        $end   = Carbon::tomorrow()->setTime(18, 0);

        // Block maintenance day
        $response = $this->actingAs($this->approvedDriver)->post(route('driver.vehicles.unavailabilities.store', $this->approvedVehicle->id), [
            'start_date' => $start->format('Y-m-d H:i:s'),
            'end_date'   => $end->format('Y-m-d H:i:s'),
            'reason'     => 'Scheduled brake servicing and oil change',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('vehicle_unavailabilities', [
            'vehicle_id' => $this->approvedVehicle->id,
            'reason'     => 'Scheduled brake servicing and oil change',
        ]);

        $unavailability = VehicleUnavailability::where('vehicle_id', $this->approvedVehicle->id)->first();

        // Unblock / Delete period
        $delResponse = $this->actingAs($this->approvedDriver)->delete(route('driver.vehicles.unavailabilities.destroy', [
            'id'             => $this->approvedVehicle->id,
            'unavailability' => $unavailability->id,
        ]));

        $delResponse->assertRedirect();
        $this->assertDatabaseMissing('vehicle_unavailabilities', ['id' => $unavailability->id]);
    }

    public function test_driver_cannot_create_overlapping_blocked_periods(): void
    {
        $start = Carbon::tomorrow()->setTime(9, 0);
        $end   = Carbon::tomorrow()->setTime(15, 0);

        $this->approvedVehicle->unavailabilities()->create([
            'start_date' => $start,
            'end_date'   => $end,
            'reason'     => 'Existing maintenance window',
        ]);

        // Attempt overlapping block (10:00 to 18:00)
        $response = $this->actingAs($this->approvedDriver)->post(route('driver.vehicles.unavailabilities.store', $this->approvedVehicle->id), [
            'start_date' => $start->copy()->addHour()->format('Y-m-d H:i:s'),
            'end_date'   => $end->copy()->addHours(3)->format('Y-m-d H:i:s'),
            'reason'     => 'Conflicting maintenance',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertEquals(1, $this->approvedVehicle->unavailabilities()->count());
    }

    public function test_scheduling_shared_trip_is_prevented_if_vehicle_has_driver_blocked_dates(): void
    {
        $blockDate = Carbon::tomorrow();
        $this->approvedVehicle->unavailabilities()->create([
            'start_date' => $blockDate->copy()->setTime(8, 0),
            'end_date'   => $blockDate->copy()->setTime(17, 0),
            'reason'     => 'Annual engine overhaul',
        ]);

        // Attempt scheduling trip in that blocked window
        $payload = [
            'vehicle_id'            => $this->approvedVehicle->id,
            'route_id'              => $this->route->id,
            'trip_date'             => $blockDate->format('Y-m-d'),
            'departure_time'        => '10:00',
            'expected_arrival_time' => '13:00',
            'available_seats'       => 10,
            'fare'                  => 1200,
        ];

        $response = $this->actingAs($this->approvedDriver)->post(route('driver.trips.store'), $payload);

        $response->assertSessionHasErrors(['vehicle_id']);
        $this->assertDatabaseMissing('trips', [
            'vehicle_id'     => $this->approvedVehicle->id,
            'trip_date'      => $blockDate->format('Y-m-d'),
            'departure_time' => '10:00:00',
        ]);
    }

    public function test_scheduling_shared_trip_is_prevented_if_vehicle_has_active_private_booking(): void
    {
        $travelDate = Carbon::tomorrow()->addDays(2);
        $start = $travelDate->copy()->setTime(9, 0);
        $end   = $travelDate->copy()->setTime(18, 0);

        // Pre-existing confirmed private booking
        Booking::create([
            'booking_reference' => 'RC-TEST-PRIV-001',
            'user_id'           => $this->passenger->id,
            'vehicle_id'        => $this->approvedVehicle->id,
            'driver_id'         => $this->approvedDriver->id,
            'booking_type'      => 'private',
            'pickup_location'   => 'Gilgit',
            'destination'       => 'Hunza',
            'start_datetime'    => $start,
            'end_datetime'      => $end,
            'passengers'        => 5,
            'seats'             => 14,
            'total_fare'        => 12000,
            'booking_status'    => 'confirmed',
            'payment_status'    => 'paid',
            'status'            => 'confirmed',
        ]);

        // Attempt scheduling a public shared trip during this private reservation
        $payload = [
            'vehicle_id'            => $this->approvedVehicle->id,
            'route_id'              => $this->route->id,
            'trip_date'             => $travelDate->format('Y-m-d'),
            'departure_time'        => '11:00',
            'expected_arrival_time' => '15:00',
            'available_seats'       => 10,
            'fare'                  => 1200,
        ];

        $response = $this->actingAs($this->approvedDriver)->post(route('driver.trips.store'), $payload);

        $response->assertSessionHasErrors(['vehicle_id']);
    }

    public function test_blocking_dates_is_prevented_if_vehicle_has_scheduled_public_trip(): void
    {
        $tripDate = Carbon::tomorrow()->addDays(3);

        // Create scheduled trip
        $trip = Trip::create([
            'user_id'               => $this->approvedDriver->id,
            'vehicle_id'            => $this->approvedVehicle->id,
            'route_id'              => $this->route->id,
            'trip_date'             => $tripDate->format('Y-m-d'),
            'departure_time'        => '09:00:00',
            'expected_arrival_time' => '13:00:00',
            'total_seats'           => 14,
            'available_seats'       => 14,
            'fare'                  => 1500,
            'status'                => 'scheduled',
        ]);

        // Attempt to block that date/time window
        $response = $this->actingAs($this->approvedDriver)->post(route('driver.vehicles.unavailabilities.store', $this->approvedVehicle->id), [
            'start_date' => $tripDate->copy()->setTime(8, 0)->format('Y-m-d H:i:s'),
            'end_date'   => $tripDate->copy()->setTime(14, 0)->format('Y-m-d H:i:s'),
            'reason'     => 'Want to block date after trip was already scheduled',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('vehicle_unavailabilities', [
            'vehicle_id' => $this->approvedVehicle->id,
        ]);
    }

    public function test_blocking_dates_is_prevented_if_vehicle_has_confirmed_private_booking(): void
    {
        $bookingDate = Carbon::tomorrow()->addDays(4);
        $start = $bookingDate->copy()->setTime(10, 0);
        $end   = $bookingDate->copy()->setTime(16, 0);

        Booking::create([
            'booking_reference' => 'RC-TEST-PRIV-002',
            'user_id'           => $this->passenger->id,
            'vehicle_id'        => $this->approvedVehicle->id,
            'driver_id'         => $this->approvedDriver->id,
            'booking_type'      => 'private',
            'pickup_location'   => 'Gilgit',
            'destination'       => 'Skardu',
            'start_datetime'    => $start,
            'end_datetime'      => $end,
            'passengers'        => 4,
            'seats'             => 14,
            'total_fare'        => 15000,
            'booking_status'    => 'confirmed',
            'payment_status'    => 'paid',
            'status'            => 'confirmed',
        ]);

        $response = $this->actingAs($this->approvedDriver)->post(route('driver.vehicles.unavailabilities.store', $this->approvedVehicle->id), [
            'start_date' => $start->copy()->subHour()->format('Y-m-d H:i:s'),
            'end_date'   => $end->copy()->addHour()->format('Y-m-d H:i:s'),
            'reason'     => 'Attempted block on existing confirmed rental',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('vehicle_unavailabilities', [
            'vehicle_id' => $this->approvedVehicle->id,
        ]);
    }

    public function test_driver_cannot_schedule_overlapping_trips_even_on_different_vehicles(): void
    {
        $tripDate = Carbon::tomorrow()->addDays(5);

        // Driver schedules Trip 1 with approvedVehicle
        Trip::create([
            'user_id'               => $this->approvedDriver->id,
            'vehicle_id'            => $this->approvedVehicle->id,
            'route_id'              => $this->route->id,
            'trip_date'             => $tripDate->format('Y-m-d'),
            'departure_time'        => '09:00:00',
            'expected_arrival_time' => '13:00:00',
            'total_seats'           => 14,
            'available_seats'       => 14,
            'fare'                  => 1500,
            'status'                => 'scheduled',
        ]);

        // Second approved vehicle for the same driver
        $secondApprovedVehicle = Vehicle::factory()->create([
            'user_id'         => $this->approvedDriver->id,
            'registration_no' => 'GLT-7744',
            'status'          => 'approved',
            'total_seats'     => 10,
        ]);

        // Driver attempts to schedule simultaneous Trip 2 on the other vehicle
        $payload = [
            'vehicle_id'            => $secondApprovedVehicle->id,
            'route_id'              => $this->route->id,
            'trip_date'             => $tripDate->format('Y-m-d'),
            'departure_time'        => '10:00', // overlaps with 09:00 - 13:00!
            'expected_arrival_time' => '14:00',
            'available_seats'       => 8,
            'fare'                  => 1200,
        ];

        $response = $this->actingAs($this->approvedDriver)->post(route('driver.trips.store'), $payload);

        $response->assertSessionHasErrors(['departure_time']);
        $this->assertDatabaseMissing('trips', [
            'vehicle_id' => $secondApprovedVehicle->id,
            'trip_date'  => $tripDate->format('Y-m-d'),
        ]);
    }

    public function test_trip_cancellation_frees_up_vehicle_availability(): void
    {
        $tripDate = Carbon::tomorrow()->addDays(6);
        $trip = Trip::create([
            'user_id'               => $this->approvedDriver->id,
            'vehicle_id'            => $this->approvedVehicle->id,
            'route_id'              => $this->route->id,
            'trip_date'             => $tripDate->format('Y-m-d'),
            'departure_time'        => '08:00:00',
            'expected_arrival_time' => '12:00:00',
            'total_seats'           => 14,
            'available_seats'       => 14,
            'fare'                  => 1500,
            'status'                => 'scheduled',
        ]);

        $start = Carbon::parse("{$tripDate->format('Y-m-d')} 08:00:00");
        $end   = Carbon::parse("{$tripDate->format('Y-m-d')} 12:00:00");

        // Vehicle is conflicted right now
        $this->assertFalse($this->approvedVehicle->isAvailableForRange($start, $end));

        // Driver cancels trip
        $response = $this->actingAs($this->approvedDriver)->put(route('driver.trips.update', $trip->id), [
            'status'          => 'cancelled',
            'departure_time'  => '08:00',
            'available_seats' => 0,
        ]);

        $response->assertRedirect();
        $trip->refresh();
        $this->assertEquals('cancelled', strtolower($trip->status));

        // Vehicle is now available for that timeframe
        $this->assertTrue($this->approvedVehicle->isAvailableForRange($start, $end));
    }
}
