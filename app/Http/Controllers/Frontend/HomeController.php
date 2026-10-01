<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Public Homepage
     */
    public function index()
    {
        return view('frontend.index');
    }

    /**
     * Trip Search & Listing Page
     */
    public function trips()
    {
        return view('frontend.trips');
    }

    /**
     * Passenger Booking Page
     */
    public function booking()
    {
        return view('frontend.booking');
    }
}
