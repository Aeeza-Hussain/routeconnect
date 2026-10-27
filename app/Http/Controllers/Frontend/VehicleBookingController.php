<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\Stop;
use App\Models\Trip;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class VehicleBookingController extends Controller
{
    /**
     * Display the Book a Vehicle page with dual modes (Shared Ride vs Private Vehicle)
     * and live search results.
     */
    public function index(Request $request)
    {
        $mode = $request->query('mode', 'private'); // 'private' or 'shared'
        if (!in_array($mode, ['private', 'shared'])) {
            $mode = 'private';
        }

        // Available vehicle types in RouteConnect
        $vehicleTypes = ['Car', 'Van', 'Daba', 'Bus', 'Other'];

        // Existing Stops for search suggestions / dropdowns
        $stops = Stop::orderBy('city')->orderBy('name')->get();

        // Search inputs
        $pickup      = trim((string) $request->query('pickup', ''));
        $destination = trim((string) $request->query('destination', ''));
        $pickupDate  = $request->query('pickup_date', Carbon::today()->format('Y-m-d'));
        $pickupTime  = $request->query('pickup_time', '09:00');
        $returnDate  = $request->query('return_date', Carbon::today()->addDays(1)->format('Y-m-d'));
        $returnTime  = $request->query('return_time', '18:00');
        $vehicleType = trim((string) $request->query('vehicle_type', ''));
        $passengers  = max(1, (int) $request->query('passengers', 1));

        // Date validation: ensure dates are not in the past
        $todayStr = Carbon::today()->format('Y-m-d');
        if ($pickupDate && $pickupDate < $todayStr) {
            $pickupDate = $todayStr;
        }
        if ($returnDate && $returnDate < $pickupDate) {
            $returnDate = $pickupDate;
        }

        // ─── Mode A: Shared Ride Search ───
        $sharedTrips = collect();
        if ($mode === 'shared') {
            $tripQuery = Trip::with([
                'route.startStop',
                'route.endStop',
                'route.routeStops.stop',
                'driver',
                'vehicle',
            ])
            ->where('status', 'scheduled')
            ->whereHas('driver', function ($q) {
                $q->where('driver_status', 'approved');
            })
            ->whereHas('vehicle', function ($q) {
                $q->whereIn(DB::raw('LOWER(status)'), ['approved', 'active']);
            });

            if ($pickupDate) {
                $tripQuery->whereDate('trip_date', $pickupDate);
            }

            if ($passengers > 1) {
                $tripQuery->where('available_seats', '>=', $passengers);
            } else {
                $tripQuery->where('available_seats', '>', 0);
            }

            if ($vehicleType && $vehicleType !== 'all') {
                $tripQuery->whereHas('vehicle', function ($q) use ($vehicleType) {
                    $q->where('type', $vehicleType);
                });
            }

            if ($pickup) {
                $tripQuery->where(function ($q) use ($pickup) {
                    $q->where('pickup_point', 'like', "%{$pickup}%")
                      ->orWhereHas('route', function ($rq) use ($pickup) {
                          $rq->where('origin', 'like', "%{$pickup}%")
                             ->orWhere('name', 'like', "%{$pickup}%")
                             ->orWhereHas('startStop', function ($sq) use ($pickup) {
                                 $sq->where('name', 'like', "%{$pickup}%")
                                    ->orWhere('city', 'like', "%{$pickup}%");
                             })
                             ->orWhereHas('routeStops.stop', function ($sq) use ($pickup) {
                                 $sq->where('name', 'like', "%{$pickup}%")
                                    ->orWhere('city', 'like', "%{$pickup}%");
                             });
                      });
                });
            }

            if ($destination) {
                $tripQuery->where(function ($q) use ($destination) {
                    $q->whereHas('route', function ($rq) use ($destination) {
                        $rq->where('destination', 'like', "%{$destination}%")
                           ->orWhere('name', 'like', "%{$destination}%")
                           ->orWhereHas('endStop', function ($sq) use ($destination) {
                               $sq->where('name', 'like', "%{$destination}%")
                                  ->orWhere('city', 'like', "%{$destination}%");
                           })
                           ->orWhereHas('routeStops.stop', function ($sq) use ($destination) {
                               $sq->where('name', 'like', "%{$destination}%")
                                  ->orWhere('city', 'like', "%{$destination}%");
                           });
                    });
                });
            }

            $sharedTrips = $tripQuery->orderBy('trip_date')->orderBy('departure_time')->get();
        }

        // ─── Mode B: Private Vehicle Rental Search ───
        $privateVehicles = collect();
        $startDatetime = null;
        $endDatetime   = null;
        $calculatedDays = 1;

        if ($mode === 'private') {
            try {
                $startDatetime = Carbon::parse("{$pickupDate} {$pickupTime}");
                $endDatetime   = Carbon::parse("{$returnDate} {$returnTime}");

                if ($endDatetime->lessThanOrEqualTo($startDatetime)) {
                    $endDatetime = (clone $startDatetime)->addDay();
                }
            } catch (\Exception $e) {
                $startDatetime = Carbon::today()->setTime(9, 0);
                $endDatetime   = Carbon::today()->addDay()->setTime(18, 0);
            }

            $vehicleQuery = Vehicle::with(['driver', 'unavailabilities'])
                ->whereIn(DB::raw('LOWER(status)'), ['approved', 'active'])
                ->where('is_available_for_rental', true)
                ->whereHas('driver', function ($q) {
                    $q->where('driver_status', 'approved');
                });

            if ($vehicleType && $vehicleType !== 'all') {
                $vehicleQuery->where('type', $vehicleType);
            }

            if ($passengers > 1) {
                $vehicleQuery->where('total_seats', '>=', $passengers);
            }

            $candidates = $vehicleQuery->latest()->get();

            // Filter vehicles based on overlap conflict engine
            $privateVehicles = $candidates->filter(function (Vehicle $vehicle) use ($startDatetime, $endDatetime) {
                return $vehicle->isAvailableForRange($startDatetime, $endDatetime);
            })->values();

            // Calculate pricing details for each available vehicle
            foreach ($privateVehicles as $v) {
                $v->pricing = $v->calculateRentalPrice($startDatetime, $endDatetime);
            }

            $diffHours = $startDatetime->diffInHours($endDatetime);
            $calculatedDays = (int) ceil(max(1, $diffHours / 24));
        }

        return view('frontend.vehicles.index', compact(
            'mode',
            'vehicleTypes',
            'stops',
            'pickup',
            'destination',
            'pickupDate',
            'pickupTime',
            'returnDate',
            'returnTime',
            'vehicleType',
            'passengers',
            'sharedTrips',
            'privateVehicles',
            'startDatetime',
            'endDatetime',
            'calculatedDays'
        ));
    }

    /**
     * Show vehicle details and rental booking summary.
     */
    public function show(Request $request, Vehicle $vehicle)
    {
        if (!$vehicle->isApproved()) {
            abort(404, 'Vehicle is not available.');
        }

        $vehicle->load(['driver', 'unavailabilities']);

        $pickupDate = $request->query('pickup_date', Carbon::today()->format('Y-m-d'));
        $pickupTime = $request->query('pickup_time', '09:00');
        $returnDate = $request->query('return_date', Carbon::today()->addDays(1)->format('Y-m-d'));
        $returnTime = $request->query('return_time', '18:00');
        $pickup     = trim((string) $request->query('pickup', ''));
        $destination = trim((string) $request->query('destination', ''));

        $startDatetime = Carbon::parse("{$pickupDate} {$pickupTime}");
        $endDatetime   = Carbon::parse("{$returnDate} {$returnTime}");
        if ($endDatetime->lessThanOrEqualTo($startDatetime)) {
            $endDatetime = (clone $startDatetime)->addDay();
        }

        $isAvailable    = $vehicle->isAvailableForRange($startDatetime, $endDatetime);
        $conflictReason = $isAvailable ? null : $vehicle->getConflictReason($startDatetime, $endDatetime);
        $pricing        = $vehicle->calculateRentalPrice($startDatetime, $endDatetime);

        return view('frontend.vehicles.show', compact(
            'vehicle',
            'pickup',
            'destination',
            'pickupDate',
            'pickupTime',
            'returnDate',
            'returnTime',
            'startDatetime',
            'endDatetime',
            'isAvailable',
            'conflictReason',
            'pricing'
        ));
    }

    /**
     * Process private vehicle booking reservation.
     * Prevents duplicate/conflicting reservations using database transactions and row-level locking.
     */
    public function store(Request $request, Vehicle $vehicle)
    {
        // 1. Ensure user is logged in
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Please login or create an account to book this vehicle.')
                ->with('intended', url()->current());
        }

        // 2. Validate input
        $request->validate([
            'pickup_location'      => ['required', 'string', 'max:255'],
            'destination_location' => ['required', 'string', 'max:255'],
            'pickup_date'          => ['required', 'date', 'after_or_equal:today'],
            'pickup_time'          => ['required', 'string'],
            'return_date'          => ['required', 'date', 'after_or_equal:pickup_date'],
            'return_time'          => ['required', 'string'],
            'passenger_notes'      => ['nullable', 'string', 'max:1000'],
        ], [
            'pickup_location.required'      => 'Please specify a pickup location.',
            'destination_location.required' => 'Please specify a destination.',
            'pickup_date.after_or_equal'    => 'Pickup date cannot be in the past.',
            'return_date.after_or_equal'    => 'Return date must be on or after pickup date.',
        ]);

        $startDatetime = Carbon::parse($request->pickup_date . ' ' . $request->pickup_time);
        $endDatetime   = Carbon::parse($request->return_date . ' ' . $request->return_time);

        if ($endDatetime->lessThanOrEqualTo($startDatetime)) {
            throw ValidationException::withMessages([
                'return_time' => 'Return date and time must be after pickup date and time.',
            ]);
        }

        // 3. Execute atomic transaction with pessimistic locking on vehicle
        $booking = DB::transaction(function () use ($vehicle, $startDatetime, $endDatetime, $request) {
            // Lock vehicle row to prevent concurrent double-booking
            $lockedVehicle = Vehicle::with('driver')->where('id', $vehicle->id)->lockForUpdate()->firstOrFail();

            if (!$lockedVehicle->isApproved()) {
                throw ValidationException::withMessages([
                    'vehicle' => 'This vehicle is no longer approved for rental reservations.',
                ]);
            }

            if ($lockedVehicle->driver && !$lockedVehicle->driver->isApprovedDriver()) {
                throw ValidationException::withMessages([
                    'vehicle' => 'The assigned driver for this vehicle is not eligible for reservations.',
                ]);
            }

            if ($lockedVehicle->is_available_for_rental === false) {
                throw ValidationException::withMessages([
                    'vehicle' => 'This vehicle is currently not available for private rentals.',
                ]);
            }

            // Conflict check with row locking
            $conflict = $lockedVehicle->getConflictReason($startDatetime, $endDatetime);
            if ($conflict) {
                throw ValidationException::withMessages([
                    'pickup_date' => $conflict,
                ]);
            }

            // Calculate rental price
            $pricing = $lockedVehicle->calculateRentalPrice($startDatetime, $endDatetime);

            // Generate unique booking reference
            do {
                $reference = 'RC-PV-' . strtoupper(Str::random(4)) . '-' . mt_rand(1000, 9999);
            } while (Booking::where('booking_reference', $reference)->exists());

            // Create booking record
            $newBooking = Booking::create([
                'user_id'              => auth()->id(),
                'vehicle_id'           => $lockedVehicle->id,
                'trip_id'              => null,
                'booking_type'         => 'private',
                'pickup_location'      => trim($request->pickup_location),
                'destination_location' => trim($request->destination_location),
                'start_datetime'       => $startDatetime,
                'end_datetime'         => $endDatetime,
                'days'                 => $pricing['days'],
                'daily_rate'           => $pricing['daily_rate'],
                'total_fare'           => $pricing['total_fare'],
                'seats'                => $lockedVehicle->total_seats,
                'booking_reference'    => $reference,
                'passenger_notes'      => $request->passenger_notes ? trim($request->passenger_notes) : null,
                'status'               => 'confirmed',
                'booking_status'       => 'confirmed',
            ]);

            // Passenger notification
            Notification::create([
                'user_id' => auth()->id(),
                'message' => "Your private vehicle booking #{$reference} ({$lockedVehicle->model} - {$lockedVehicle->registration_no}) has been confirmed from {$startDatetime->format('d M Y h:i A')} to {$endDatetime->format('d M Y h:i A')}.",
                'is_read' => false,
            ]);

            // Driver notification
            if ($lockedVehicle->user_id && $lockedVehicle->user_id !== auth()->id()) {
                Notification::create([
                    'user_id' => $lockedVehicle->user_id,
                    'message' => "New private vehicle reservation #{$reference} from " . auth()->user()->name . " for {$lockedVehicle->registration_no} ({$pricing['days']} days).",
                    'is_read' => false,
                ]);
            }

            return $newBooking;
        });

        return redirect()->route('booking.confirmation', $booking->id)
            ->with('success', 'Your private vehicle booking #' . $booking->booking_reference . ' has been confirmed successfully!');
    }
}
