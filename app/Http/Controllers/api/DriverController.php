<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $query = User::where('user_type', 2)
            ->with(['vehicles' => function ($q) {
                $q->latest();
            }]);

        if ($status && $status !== 'all') {
            $query->where('driver_status', $status);
        }

        $driver = $query->latest()->get();

        return response()->json([
            'status' => true,
            'driver' => $driver,
        ]);
    }
}
