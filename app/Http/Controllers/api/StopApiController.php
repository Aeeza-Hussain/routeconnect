<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\Stop;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StopApiController extends Controller
{
    /**
     * Get all active stops or search by keyword.
     */
    public function stops(Request $request): JsonResponse
    {
        $query = Stop::query();

        if (!$request->boolean('include_inactive')) {
            $query->active();
        }

        if ($request->filled('q')) {
            $search = '%' . trim($request->q) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('location', 'like', $search);
            });
        }

        $stops = $query->orderBy('name')->get();

        return response()->json([
            'success' => true,
            'message' => 'Stops retrieved successfully.',
            'data'    => $stops,
        ]);
    }

    /**
     * Get all active routes with stops.
     */
    public function routes(Request $request): JsonResponse
    {
        $query = Route::with(['startStop', 'endStop', 'routeStops.stop']);

        if (!$request->boolean('include_inactive')) {
            $query->where('status', 'active');
        }

        if ($request->filled('q')) {
            $search = '%' . trim($request->q) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('start_location', 'like', $search)
                  ->orWhere('end_location', 'like', $search);
            });
        }

        $routes = $query->latest('id')->get();

        return response()->json([
            'success' => true,
            'message' => 'Routes retrieved successfully.',
            'data'    => $routes,
        ]);
    }
}
