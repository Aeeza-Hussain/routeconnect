<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class DriverController extends Controller
{
    public function index(){
        $driver = User::where('user_type', 2)->get();
        return response()->json([
            'status' => true,
            'driver' => $driver
        ]);
    }
}
