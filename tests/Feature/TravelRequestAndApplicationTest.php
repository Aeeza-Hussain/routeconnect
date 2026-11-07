<?php

namespace Tests\Feature;

use App\Models\DriverApplication;
use App\Models\TravelRequest;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TravelRequestAndApplicationTest extends TestCase
{
    use RefreshDatabase;

    protected User $passenger;
    protected User $otherPassenger;
    protected User $driver;
    protected User $secondDriver;
    protected User $unapprovedDriver;
    protected Vehicle $driverVehicle;
    protected Vehicle $secondDriverVehicle;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Primary Passenger
        $this->passenger = User::factory()->create([
            'name'      => 'Aleeza Fatima',
            'email'     => 'aleeza@example.com',
            'user_type' => 0,
        ]);

        // 2. Secondary Passenger
        $this->otherPassenger = User::factory()->create([
            'name'      => 'Other Passenger',
            'email'     => 'other@example.com',
            'user_type' => 0,
        ]);

        // 3. Approved Driver 1
        $this->driver = User::factory()->create([
            'name'          => 'Ahmad Driver',
            'email'         => 'ahmad@example.com',
            'user_type'     => 2,
            'driver_status' => 'approved',
        ]);

        // 4. Approved Driver 2
        $this->secondDriver = User::factory()->create([
            'name'          => 'Bilal Driver',
            'email'         => 'bilal@example.com',
            'user_type'     => 2,
            'driver_status' => 'approved',
        ]);

        // 5. Unapproved Driver
        $this->unapprovedDriver = User::factory()->create([
            'name'          => 'Pending Driver',
            'email'         => 'pending@example.com',
            'user_type'     => 2,
            'driver_status' => 'pending',
        ]);

        // Vehicles
        $this->driverVehicle = Vehicle::create([
            'user_id'                 => $this->driver->id,
            'model'                   => 'Toyota HiAce Grand',
            'type'                    => 'Van',
            'registration_no'         => 'GLT-1001',
            'total_seats'             => 12,
            'status'                  => 'approved',
            'rental_rate_per_day'     => 6000.00,
            'is_available_for_rental' => true,
        ]);

        $this->secondDriverVehicle = Vehicle::create([
            'user_id'                 => $this->secondDriver->id,
            'model'                   => 'Honda Civic Sedan',
            'type'                    => 'Car',
            'registration_no'         => 'GLT-2002',
            'total_seats'             => 4,
            'status'                  => 'approved',
            'rental_rate_per_day'     => 4500.00,
            'is_available_for_rental' => true,
        ]);
    }

    /** 1. Passenger can post travel request with all required and optional fields */
    public function test_passenger_can_post_travel_request_with_full_details(): void
    {
        $response = $this->actingAs($this->passenger)->post('/my-activity/requests', [
            'pickup_location'        => 'Gilgit Terminal',
            'destination_location'   => 'Karimabad Hunza',
            'travel_date'            => Carbon::today()->addDays(3)->format('Y-m-d'),
            'travel_time'            => '10:00 AM',
            'seats_needed'           => 3,
            'preferred_vehicle_type' => 'Van',
            'intermediate_stops'     => 'Aliabad, Nagar',
            'budget'                 => 4500,
            'notes'                  => 'Have luggage and 2 small bags.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('travel_requests', [
            'user_id'                => $this->passenger->id,
            'pickup_location'        => 'Gilgit Terminal',
            'destination_location'   => 'Karimabad Hunza',
            'seats_needed'           => 3,
            'preferred_vehicle_type' => 'Van',
            'budget'                 => 4500,
            'status'                 => 'open',
        ]);

        $request = TravelRequest::where('user_id', $this->passenger->id)->first();
        $this->assertNotNull($request);
        $this->assertContains('Aliabad', $request->intermediate_stops);
        $this->assertContains('Nagar', $request->intermediate_stops);
    }

    /** 2. Driver cannot post passenger travel request */
    public function test_driver_cannot_post_passenger_travel_request(): void
    {
        $response = $this->actingAs($this->driver)->post('/my-activity/requests', [
            'pickup_location'      => 'Gilgit',
            'destination_location' => 'Hunza',
            'travel_date'          => Carbon::today()->addDays(2)->format('Y-m-d'),
            'seats_needed'         => 2,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('travel_requests', [
            'user_id' => $this->driver->id,
        ]);
    }

    /** 3. Approved driver can view passenger requests section in driver dashboard and filter */
    public function test_approved_driver_can_browse_and_filter_passenger_requests(): void
    {
        $req1 = TravelRequest::create([
            'user_id'                => $this->passenger->id,
            'pickup_location'        => 'Gilgit Airport',
            'destination_location'   => 'Hunza Valley',
            'travel_date'            => Carbon::today()->addDays(2)->format('Y-m-d'),
            'seats_needed'           => 4,
            'preferred_vehicle_type' => 'Van',
            'budget'                 => 5000,
            'status'                 => 'open',
        ]);

        $req2 = TravelRequest::create([
            'user_id'                => $this->otherPassenger->id,
            'pickup_location'        => 'Skardu Shangrila',
            'destination_location'   => 'Deosai Plains',
            'travel_date'            => Carbon::today()->addDays(5)->format('Y-m-d'),
            'seats_needed'           => 2,
            'preferred_vehicle_type' => 'Car',
            'budget'                 => 8000,
            'status'                 => 'open',
        ]);

        // Access dashboard passenger requests
        $response = $this->actingAs($this->driver)->get('/driver/passenger-requests');
        $response->assertStatus(200);
        $response->assertSee('Gilgit Airport');
        $response->assertSee('Skardu Shangrila');

        // Filter by route
        $filterRoute = $this->actingAs($this->driver)->get('/driver/passenger-requests?route=Hunza');
        $filterRoute->assertStatus(200);
        $filterRoute->assertSee('Gilgit Airport');
        $filterRoute->assertDontSee('Skardu Shangrila');

        // Filter by vehicle type
        $filterVehicle = $this->actingAs($this->driver)->get('/driver/passenger-requests?vehicle_type=Car');
        $filterVehicle->assertStatus(200);
        $filterVehicle->assertSee('Skardu Shangrila');
        $filterVehicle->assertDontSee('Gilgit Airport');
    }

    /** 4. Non-approved driver or passenger cannot access driver passenger requests */
    public function test_unapproved_driver_and_passenger_cannot_access_driver_requests(): void
    {
        // Passenger gets redirected or denied
        $passengerResponse = $this->actingAs($this->passenger)->get('/driver/passenger-requests');
        $this->assertTrue(in_array($passengerResponse->status(), [302, 403]));

        // Pending unapproved driver gets redirected to driver pending page
        $pendingDriverResponse = $this->actingAs($this->unapprovedDriver)->get('/driver/passenger-requests');
        $pendingDriverResponse->assertRedirect(route('driver.pending'));
    }

    /** 5. Approved driver can submit application with vehicle and fare */
    public function test_driver_can_apply_to_request_with_approved_vehicle(): void
    {
        $travelRequest = TravelRequest::create([
            'user_id'              => $this->passenger->id,
            'pickup_location'      => 'Gilgit Center',
            'destination_location' => 'Hunza Altit Fort',
            'travel_date'          => Carbon::today()->addDays(2)->format('Y-m-d'),
            'seats_needed'         => 3,
            'budget'               => 3500,
            'status'               => 'open',
        ]);

        $response = $this->actingAs($this->driver)->post("/driver/passenger-requests/{$travelRequest->id}/apply", [
            'offered_fare'    => 3200,
            'proposed_time'   => '09:00 AM',
            'vehicle_id'      => $this->driverVehicle->id,
            'available_seats' => 4,
            'message'         => 'Comfortable AC HiAce with professional driver.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('driver_applications', [
            'travel_request_id' => $travelRequest->id,
            'driver_id'         => $this->driver->id,
            'vehicle_id'        => $this->driverVehicle->id,
            'offered_fare'      => 3200,
            'available_seats'   => 4,
            'status'            => 'pending',
        ]);
    }

    /** 6. Driver cannot apply using another driver's vehicle or unapproved vehicle */
    public function test_driver_cannot_apply_with_unowned_vehicle(): void
    {
        $travelRequest = TravelRequest::create([
            'user_id'              => $this->passenger->id,
            'pickup_location'      => 'Gilgit',
            'destination_location' => 'Hunza',
            'travel_date'          => Carbon::today()->addDays(2)->format('Y-m-d'),
            'seats_needed'         => 2,
            'status'               => 'open',
        ]);

        // Driver 1 tries to use Driver 2's vehicle
        $response = $this->actingAs($this->driver)->post("/driver/passenger-requests/{$travelRequest->id}/apply", [
            'offered_fare' => 3000,
            'vehicle_id'   => $this->secondDriverVehicle->id, // belongs to secondDriver
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('driver_applications', [
            'travel_request_id' => $travelRequest->id,
            'driver_id'         => $this->driver->id,
        ]);
    }

    /** 7. Driver cannot apply multiple times to the same request (Duplicate Prevention) */
    public function test_driver_cannot_submit_duplicate_applications(): void
    {
        $travelRequest = TravelRequest::create([
            'user_id'              => $this->passenger->id,
            'pickup_location'      => 'Gilgit',
            'destination_location' => 'Hunza',
            'travel_date'          => Carbon::today()->addDays(2)->format('Y-m-d'),
            'seats_needed'         => 2,
            'status'               => 'open',
        ]);

        // First application succeeds
        DriverApplication::create([
            'travel_request_id' => $travelRequest->id,
            'driver_id'         => $this->driver->id,
            'vehicle_id'        => $this->driverVehicle->id,
            'offered_fare'      => 3000,
            'status'            => 'pending',
        ]);

        // Second application from same driver to same request is blocked
        $response = $this->actingAs($this->driver)->post("/driver/passenger-requests/{$travelRequest->id}/apply", [
            'offered_fare' => 2800,
            'vehicle_id'   => $this->driverVehicle->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertEquals(1, DriverApplication::where('travel_request_id', $travelRequest->id)->where('driver_id', $this->driver->id)->count());
    }

    /** 8. Passenger can view received applications and select one driver (Workflow & Status updates) */
    public function test_passenger_can_select_one_driver_and_other_offers_are_rejected(): void
    {
        $travelRequest = TravelRequest::create([
            'user_id'              => $this->passenger->id,
            'pickup_location'      => 'Gilgit Airport',
            'destination_location' => 'Karimabad Hunza',
            'travel_date'          => Carbon::today()->addDays(2)->format('Y-m-d'),
            'seats_needed'         => 3,
            'status'               => 'open',
        ]);

        // Driver 1 applies
        $app1 = DriverApplication::create([
            'travel_request_id' => $travelRequest->id,
            'driver_id'         => $this->driver->id,
            'vehicle_id'        => $this->driverVehicle->id,
            'offered_fare'      => 3200,
            'status'            => 'pending',
        ]);

        // Driver 2 applies
        $app2 = DriverApplication::create([
            'travel_request_id' => $travelRequest->id,
            'driver_id'         => $this->secondDriver->id,
            'vehicle_id'        => $this->secondDriverVehicle->id,
            'offered_fare'      => 2900,
            'status'            => 'pending',
        ]);

        // Passenger selects Driver 2
        $response = $this->actingAs($this->passenger)->post("/my-activity/requests/{$travelRequest->id}/applications/{$app2->id}/select");
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check statuses:
        // App2 is accepted
        $app2->refresh();
        $this->assertEquals('accepted', $app2->status);

        // App1 is rejected
        $app1->refresh();
        $this->assertEquals('rejected', $app1->status);

        // TravelRequest is confirmed with selected_application_id
        $travelRequest->refresh();
        $this->assertEquals('confirmed', $travelRequest->status);
        $this->assertEquals($app2->id, $travelRequest->selected_application_id);
    }

    /** 9. Passenger Privacy: Passenger cannot select driver on another passenger's request */
    public function test_passenger_cannot_manage_other_passengers_travel_requests(): void
    {
        $otherReq = TravelRequest::create([
            'user_id'              => $this->otherPassenger->id,
            'pickup_location'      => 'Secret Location',
            'destination_location' => 'Private Villa',
            'travel_date'          => Carbon::today()->addDays(3)->format('Y-m-d'),
            'seats_needed'         => 1,
            'status'               => 'open',
        ]);

        $app = DriverApplication::create([
            'travel_request_id' => $otherReq->id,
            'driver_id'         => $this->driver->id,
            'offered_fare'      => 2000,
            'status'            => 'pending',
        ]);

        // Primary passenger tries to select driver on other passenger's request
        $response = $this->actingAs($this->passenger)->post("/my-activity/requests/{$otherReq->id}/applications/{$app->id}/select");
        $response->assertStatus(403);

        // Primary passenger tries to delete other passenger's request
        $deleteResponse = $this->actingAs($this->passenger)->delete("/my-activity/requests/{$otherReq->id}");
        $deleteResponse->assertStatus(403);
    }

    /** 10. Passenger can cancel/close their own request */
    public function test_passenger_can_cancel_own_travel_request(): void
    {
        $req = TravelRequest::create([
            'user_id'              => $this->passenger->id,
            'pickup_location'      => 'Gilgit',
            'destination_location' => 'Hunza',
            'travel_date'          => Carbon::today()->addDays(2)->format('Y-m-d'),
            'seats_needed'         => 2,
            'status'               => 'open',
        ]);

        $response = $this->actingAs($this->passenger)->delete("/my-activity/requests/{$req->id}", [
            'reason' => 'Plans changed',
        ]);

        $response->assertRedirect();
        $req->refresh();
        $this->assertEquals('cancelled', $req->status);
    }
}
