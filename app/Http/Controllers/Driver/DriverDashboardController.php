<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DriverDashboardController extends Controller
{
    /**
     * Approved Driver Dashboard Page
     */
    public function index()
    {
        return view('backend.driver.dashboard');
    }

    /**
     * Driver Application Pending/Status Page
     */
    public function pending()
    {
        $user = auth()->user();
        return view('backend.driver.pending', compact('user'));
    }
}
