<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\DriverApplication;
use App\Models\Message;
use App\Models\Notification;
use App\Models\TravelRequest;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TravelRequestApiController extends Controller
{
    /**
     * List travel requests:
     * - Passengers see their own requests
     * - Approved drivers see open marketplace requests
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isDriver()) {
            $query = TravelRequest::with(['user', 'applications' => fn($q) => $q->where('driver_id', $user->id)])
                ->where('status', 'open')
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
                });

            if ($request->filled('route')) {
                $query->where(function ($q) use ($request) {
                    $q->where('pickup_location', 'like', '%' . $request->route . '%')
                      ->orWhere('destination_location', 'like', '%' . $request->route . '%');
                });
            }

            if ($request->filled('date')) {
                $query->whereDate('travel_date', $request->date);
            }

            if ($request->filled('vehicle_type')) {
                $query->where('preferred_vehicle_type', $request->vehicle_type);
            }

            $requests = $query->latest()->paginate(15);

            return response()->json([
                'success' => true,
                'role'    => 'driver',
                'data'    => $requests,
            ], 200);
        }

        // Passenger views their own requests
        $requests = TravelRequest::with(['applications.driver', 'selectedApplication.driver'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(15);

        return response()->json([
            'success' => true,
            'role'    => 'passenger',
            'data'    => $requests,
        ], 200);
    }

    /**
     * View details of a specific travel request.
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $travelRequest = TravelRequest::with(['user', 'applications.driver', 'applications.vehicle', 'selectedApplication'])->find($id);

        if (!$travelRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Travel request not found.',
            ], 404);
        }

        // Privacy check: If not open, only owner and applying drivers can view
        $hasApplied = $travelRequest->applications->contains('driver_id', $user->id);
        if (!$travelRequest->isOpen() && $travelRequest->user_id !== $user->id && !$hasApplied && !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this closed travel request.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'request' => $travelRequest,
        ], 200);
    }

    /**
     * Passenger posts a new travel request.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->isDriver()) {
            return response()->json([
                'success' => false,
                'message' => 'Driver accounts cannot post passenger travel requests. Use Schedule Trip instead.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'pickup_location'        => ['required', 'string', 'max:255'],
            'destination_location'   => ['required', 'string', 'max:255'],
            'travel_date'            => ['required', 'date', 'after_or_equal:today'],
            'travel_time'            => ['nullable', 'string', 'max:50'],
            'seats_needed'           => ['required', 'integer', 'min:1', 'max:50'],
            'preferred_vehicle_type' => ['nullable', 'string', 'max:50'],
            'intermediate_stops'     => ['nullable'],
            'budget'                 => ['nullable', 'numeric', 'min:0'],
            'notes'                  => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $stops = null;
        if (!empty($request->intermediate_stops)) {
            $stops = is_array($request->intermediate_stops)
                ? $request->intermediate_stops
                : array_filter(array_map('trim', explode(',', (string) $request->intermediate_stops)));
        }

        $travelRequest = TravelRequest::create([
            'user_id'                => $user->id,
            'pickup_location'        => trim($request->pickup_location),
            'destination_location'   => trim($request->destination_location),
            'intermediate_stops'     => $stops,
            'travel_date'            => $request->travel_date,
            'travel_time'            => $request->input('travel_time', $request->input('preferred_time')),
            'seats_needed'           => (int) $request->seats_needed,
            'preferred_vehicle_type' => $request->input('preferred_vehicle_type', $request->input('preferred_vehicle')),
            'budget'                 => $request->budget ? (float) $request->budget : null,
            'notes'                  => $request->notes,
            'status'                 => 'open',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Travel request posted successfully.',
            'request' => $travelRequest,
            'data'    => $travelRequest,
        ], 201);
    }

    /**
     * Passenger updates an open travel request.
     */
    public function update(Request $request, TravelRequest $travelRequest)
    {
        $user = $request->user();

        if ($travelRequest->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. You can only edit your own requests.',
            ], 403);
        }

        if (!$travelRequest->isOpen()) {
            return response()->json([
                'success' => false,
                'message' => 'Only open travel requests can be modified.',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'pickup_location'        => ['required', 'string', 'max:255'],
            'destination_location'   => ['required', 'string', 'max:255'],
            'travel_date'            => ['required', 'date', 'after_or_equal:today'],
            'travel_time'            => ['nullable', 'string', 'max:50'],
            'seats_needed'           => ['required', 'integer', 'min:1', 'max:50'],
            'preferred_vehicle_type' => ['nullable', 'string', 'max:50'],
            'intermediate_stops'     => ['nullable'],
            'budget'                 => ['nullable', 'numeric', 'min:0'],
            'notes'                  => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $stops = null;
        if (!empty($request->intermediate_stops)) {
            $stops = is_array($request->intermediate_stops)
                ? $request->intermediate_stops
                : array_filter(array_map('trim', explode(',', (string) $request->intermediate_stops)));
        }

        $travelRequest->update([
            'pickup_location'        => trim($request->pickup_location),
            'destination_location'   => trim($request->destination_location),
            'intermediate_stops'     => $stops,
            'travel_date'            => $request->travel_date,
            'travel_time'            => $request->travel_time,
            'seats_needed'           => (int) $request->seats_needed,
            'preferred_vehicle_type' => $request->preferred_vehicle_type,
            'budget'                 => $request->budget ? (float) $request->budget : null,
            'notes'                  => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Travel request updated successfully.',
            'request' => $travelRequest->fresh(),
        ], 200);
    }

    /**
     * Cancel an open travel request.
     */
    public function destroy(Request $request, TravelRequest $travelRequest)
    {
        $user = $request->user();

        if ($travelRequest->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        $travelRequest->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Travel request cancelled.',
        ], 200);
    }

    /**
     * Driver submits an application / offer to passenger request.
     */
    public function apply(Request $request, TravelRequest $travelRequest)
    {
        $driver = $request->user();

        if (!$driver->isApprovedDriver()) {
            return response()->json([
                'success' => false,
                'message' => 'Only verified and approved drivers can submit ride offers.',
            ], 403);
        }

        if ($travelRequest->user_id === $driver->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot apply to your own travel request.',
            ], 422);
        }

        if (!$travelRequest->isOpen()) {
            return response()->json([
                'success' => false,
                'message' => 'This travel request is closed or expired.',
            ], 422);
        }

        // Prevent duplicate offers
        $existing = DriverApplication::where('travel_request_id', $travelRequest->id)
            ->where('driver_id', $driver->id)
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'You have already submitted an active offer for this request.',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'offered_fare'    => ['required', 'numeric', 'min:50'],
            'proposed_time'   => ['nullable', 'string', 'max:50'],
            'vehicle_id'      => ['nullable', 'exists:vehicles,id'],
            'available_seats' => ['nullable', 'integer', 'min:1', 'max:50'],
            'message'         => ['nullable', 'string', 'max:1000'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        if ($request->filled('vehicle_id')) {
            $chosenVehicle = Vehicle::where('id', $request->vehicle_id)
                ->where('user_id', $driver->id)
                ->whereIn(DB::raw('LOWER(status)'), ['approved', 'active'])
                ->first();

            if (!$chosenVehicle) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected vehicle does not belong to you or is not approved.',
                ], 422);
            }
        }

        $application = DriverApplication::create([
            'travel_request_id' => $travelRequest->id,
            'driver_id'         => $driver->id,
            'vehicle_id'        => $request->vehicle_id,
            'available_seats'   => $request->available_seats,
            'offered_fare'      => (float) $request->offered_fare,
            'proposed_time'     => $request->proposed_time,
            'message'           => $request->message,
            'status'            => 'pending',
        ]);

        // Start / locate conversation
        $conversation = Conversation::where('subject_type', 'travel_request')
            ->where('subject_id', $travelRequest->id)
            ->whereHas('participants', fn($q) => $q->where('user_id', $driver->id))
            ->whereHas('participants', fn($q) => $q->where('user_id', $travelRequest->user_id))
            ->first();

        if (!$conversation) {
            $conversation = DB::transaction(function () use ($driver, $travelRequest, $application, $request) {
                $conv = Conversation::create([
                    'title'                 => "Ride: {$travelRequest->pickup_location} → {$travelRequest->destination_location}",
                    'subject_type'          => 'travel_request',
                    'subject_id'            => $travelRequest->id,
                    'travel_request_id'     => $travelRequest->id,
                    'driver_application_id' => $application->id,
                    'status'                => 'active',
                    'arrangement_status'    => 'negotiating',
                    'arrangement_details'   => [
                        'route' => "{$travelRequest->pickup_location} → {$travelRequest->destination_location}",
                        'date'  => $travelRequest->travel_date->format('Y-m-d'),
                        'time'  => $request->proposed_time ?: 'Flexible',
                        'seats' => $travelRequest->seats_needed,
                        'fare'  => (float) $request->offered_fare,
                    ],
                    'created_by'          => $driver->id,
                ]);

                ConversationParticipant::create([
                    'conversation_id' => $conv->id,
                    'user_id'         => $driver->id,
                    'last_read_at'    => now(),
                ]);

                ConversationParticipant::create([
                    'conversation_id' => $conv->id,
                    'user_id'         => $travelRequest->user_id,
                    'last_read_at'    => null,
                ]);

                Message::create([
                    'conversation_id' => $conv->id,
                    'sender_id'       => $driver->id,
                    'body'            => $request->message ?: "Hello! I can offer this trip for Rs. " . number_format($request->offered_fare, 2) . ".",
                    'type'            => 'arrangement_proposal',
                    'metadata'        => [
                        'fare' => (float) $request->offered_fare,
                    ],
                ]);

                return $conv;
            });
        }

        $loadedApp = $application->load(['driver', 'vehicle']);

        return response()->json([
            'success'         => true,
            'message'         => 'Offer submitted and conversation created.',
            'application'     => $loadedApp,
            'conversation_id' => $conversation->id,
            'data'            => [
                'application'     => $loadedApp,
                'conversation_id' => $conversation->id,
            ],
        ], 201);
    }

    /**
     * Passenger accepts a driver application.
     */
    public function selectApplication(Request $request, TravelRequest $travelRequest, DriverApplication $application)
    {
        $user = $request->user();

        if ($travelRequest->user_id !== $user->id && !$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        if ($application->travel_request_id !== $travelRequest->id) {
            return response()->json([
                'success' => false,
                'message' => 'Application does not belong to this request.',
            ], 422);
        }

        if ($travelRequest->selected_application_id) {
            return response()->json([
                'success' => false,
                'message' => 'A driver has already been selected for this request.',
            ], 422);
        }

        $travelRequest->selectApplication($application);

        Notification::create([
            'user_id' => $application->driver_id,
            'message' => "Congratulations! {$user->name} accepted your offer for {$travelRequest->pickup_location} → {$travelRequest->destination_location}.",
            'is_read' => false,
        ]);

        $freshApp = $application->fresh();
        $freshReq = $travelRequest->fresh();

        return response()->json([
            'success'        => true,
            'message'        => 'Driver offer accepted successfully.',
            'application'    => $freshApp,
            'travel_request' => $freshReq,
            'data'           => [
                'application'    => $freshApp,
                'travel_request' => $freshReq,
            ],
        ], 200);
    }
}
