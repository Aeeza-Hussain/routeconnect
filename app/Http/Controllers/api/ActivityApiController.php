<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Conversation;
use App\Models\DriverApplication;
use App\Models\TravelRequest;
use App\Models\Trip;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityApiController extends Controller
{
    /**
     * Return comprehensive role-based activity for passenger or driver.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $isDriver = $user->isDriver();

        if (!$isDriver) {
            // ─── Passenger Activity ───
            $seatBookings = Booking::with(['trip.route', 'trip.driver', 'vehicle', 'fromStop', 'toStop'])
                ->where('user_id', $user->id)
                ->where(fn($q) => $q->whereNull('booking_type')->orWhere('booking_type', 'shared'))
                ->whereNotIn(DB::raw('LOWER(booking_status)'), ['completed', 'cancelled'])
                ->latest()
                ->get();

            $privateBookings = Booking::with(['vehicle.driver', 'driver'])
                ->where('user_id', $user->id)
                ->where('booking_type', 'private')
                ->whereNotIn(DB::raw('LOWER(booking_status)'), ['completed', 'cancelled'])
                ->latest()
                ->get();

            $myTravelRequests = TravelRequest::with(['applications.driver', 'selectedApplication.driver'])
                ->where('user_id', $user->id)
                ->whereNotIn(DB::raw('LOWER(status)'), ['completed', 'cancelled'])
                ->latest()
                ->get();

            $applicationsReceived = DriverApplication::with(['travelRequest', 'driver', 'vehicle'])
                ->whereHas('travelRequest', fn($q) => $q->where('user_id', $user->id))
                ->whereNotIn(DB::raw('LOWER(status)'), ['rejected', 'cancelled'])
                ->latest()
                ->get();

            $confirmedArrangements = Conversation::with(['users', 'confirmedBooking.vehicle.driver'])
                ->whereHas('participants', fn($q) => $q->where('user_id', $user->id))
                ->where('arrangement_status', 'confirmed')
                ->latest()
                ->get();

            $completedBookings = Booking::with(['trip.route', 'vehicle.driver'])
                ->where('user_id', $user->id)
                ->where(fn($q) => $q->whereIn(DB::raw('LOWER(booking_status)'), ['completed'])->orWhereIn(DB::raw('LOWER(status)'), ['completed']))
                ->latest()
                ->get();

            $cancelledBookings = Booking::with(['trip.route', 'vehicle.driver'])
                ->where('user_id', $user->id)
                ->where(fn($q) => $q->whereIn(DB::raw('LOWER(booking_status)'), ['cancelled', 'rejected'])->orWhereIn(DB::raw('LOWER(status)'), ['cancelled', 'rejected']))
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'role'    => 'passenger',
                'counts'  => [
                    'seat_bookings'          => $seatBookings->count(),
                    'private_bookings'       => $privateBookings->count(),
                    'travel_requests'        => $myTravelRequests->count(),
                    'applications_received'  => $applicationsReceived->count(),
                    'confirmed_arrangements' => $confirmedArrangements->count(),
                    'completed_trips'        => $completedBookings->count(),
                    'cancelled_expired'      => $cancelledBookings->count(),
                ],
                'sections' => [
                    'seat_bookings'          => $seatBookings,
                    'private_bookings'       => $privateBookings,
                    'travel_requests'        => $myTravelRequests,
                    'applications_received'  => $applicationsReceived,
                    'confirmed_arrangements' => $confirmedArrangements,
                    'completed_trips'        => $completedBookings,
                    'cancelled_expired'      => $cancelledBookings,
                ],
                'data' => [
                    'seat_bookings'          => $seatBookings,
                    'private_bookings'       => $privateBookings,
                    'travel_requests'        => $myTravelRequests,
                    'applications_received'  => $applicationsReceived,
                    'confirmed_arrangements' => $confirmedArrangements,
                    'completed_trips'        => $completedBookings,
                    'cancelled_expired'      => $cancelledBookings,
                ],
            ], 200);
        }

        // ─── Driver Activity ───
        $sharedRideListings = Trip::with(['route', 'vehicle', 'bookings'])
            ->where('user_id', $user->id)
            ->whereNotIn(DB::raw('LOWER(status)'), ['completed', 'cancelled'])
            ->latest('trip_date')
            ->get();

        $myVehicles = Vehicle::with('unavailabilities')
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        $myBookings = Booking::with(['user', 'trip.route', 'vehicle'])
            ->where(function ($q) use ($user) {
                $q->where('driver_id', $user->id)
                  ->orWhereHas('trip', fn($t) => $t->where('user_id', $user->id))
                  ->orWhereHas('vehicle', fn($v) => $v->where('user_id', $user->id));
            })
            ->whereNotIn(DB::raw('LOWER(booking_status)'), ['completed', 'cancelled'])
            ->latest()
            ->get();

        $passengerRequests = TravelRequest::with(['user', 'applications' => fn($q) => $q->where('driver_id', $user->id)])
            ->where('status', 'open')
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()))
            ->latest()
            ->get();

        $myApplications = DriverApplication::with(['travelRequest.user', 'vehicle'])
            ->where('driver_id', $user->id)
            ->whereNotIn(DB::raw('LOWER(status)'), ['cancelled', 'rejected'])
            ->latest()
            ->get();

        $confirmedArrangements = Conversation::with(['users', 'confirmedBooking.vehicle', 'confirmedBooking.user'])
            ->whereHas('participants', fn($q) => $q->where('user_id', $user->id))
            ->where('arrangement_status', 'confirmed')
            ->latest()
            ->get();

        $driverCompletedTrips = Trip::with(['route', 'vehicle', 'bookings.user'])
            ->where('user_id', $user->id)
            ->where(DB::raw('LOWER(status)'), 'completed')
            ->latest('trip_date')
            ->get();

        $driverCancelledTrips = Trip::with(['route', 'vehicle'])
            ->where('user_id', $user->id)
            ->where(DB::raw('LOWER(status)'), 'cancelled')
            ->latest('trip_date')
            ->get();

        return response()->json([
            'success' => true,
            'role'    => 'driver',
            'counts'  => [
                'shared_rides'           => $sharedRideListings->count(),
                'my_vehicles'            => $myVehicles->count(),
                'my_bookings'            => $myBookings->count(),
                'passenger_requests'     => $passengerRequests->count(),
                'my_applications'        => $myApplications->count(),
                'confirmed_arrangements' => $confirmedArrangements->count(),
                'completed_trips'        => $driverCompletedTrips->count(),
                'cancelled_expired'      => $driverCancelledTrips->count(),
            ],
            'sections' => [
                'shared_rides'           => $sharedRideListings,
                'my_vehicles'            => $myVehicles,
                'my_bookings'            => $myBookings,
                'passenger_requests'     => $passengerRequests,
                'my_applications'        => $myApplications,
                'confirmed_arrangements' => $confirmedArrangements,
                'completed_trips'        => $driverCompletedTrips,
                'cancelled_expired'      => $driverCancelledTrips,
            ],
            'data' => [
                'shared_rides'           => $sharedRideListings,
                'my_vehicles'            => $myVehicles,
                'my_bookings'            => $myBookings,
                'passenger_requests'     => $passengerRequests,
                'my_applications'        => $myApplications,
                'confirmed_arrangements' => $confirmedArrangements,
                'completed_trips'        => $driverCompletedTrips,
                'cancelled_expired'      => $driverCancelledTrips,
            ],
        ], 200);
    }
}
