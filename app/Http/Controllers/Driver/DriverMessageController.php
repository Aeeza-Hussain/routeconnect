<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\TripMessage;

class DriverMessageController extends Controller
{
    /**
     * Display a listing of messages posted by the driver, along with a message posting form.
     */
    public function index(Request $request)
    {
        $driver = auth()->user();

        // Get driver's own trips for selection dropdown
        $driverTrips = Trip::where('user_id', $driver->id)
            ->with('route')
            ->latest('trip_date')
            ->latest('departure_time')
            ->get();

        // Query driver's own messages
        $query = TripMessage::where('user_id', $driver->id)->with('trip.route');

        if ($request->filled('trip_id')) {
            $query->where('trip_id', $request->trip_id);
        }

        $messages = $query->latest()->get();

        return view('backend.driver.messages', compact('driver', 'driverTrips', 'messages'));
    }

    /**
     * Store a newly created trip message.
     * Enforces that the driver can ONLY post a message for their own trip.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'trip_id' => 'required|exists:trips,id',
            'message' => 'required|string|min:3|max:1000',
        ]);

        $trip = Trip::findOrFail($validated['trip_id']);

        // Strict authorization check: Driver must own the trip
        if ($trip->user_id !== auth()->id()) {
            abort(403, 'Unauthorized. You can only post messages for your own trips.');
        }

        TripMessage::create([
            'trip_id' => $trip->id,
            'user_id' => auth()->id(),
            'message' => $validated['message'],
        ]);

        return redirect()->back()->with('success', 'Trip message posted successfully.');
    }

    /**
     * Remove the specified message.
     * Enforces ownership check.
     */
    public function destroy($id)
    {
        $message = TripMessage::findOrFail($id);

        if ($message->user_id !== auth()->id()) {
            abort(403, 'Unauthorized. You can only delete your own messages.');
        }

        $message->delete();

        return redirect()->back()->with('success', 'Message removed successfully.');
    }
}
