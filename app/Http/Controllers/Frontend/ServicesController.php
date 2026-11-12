<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServicesController extends Controller
{
    /**
     * Display the RouteConnect Services page.
     */
    public function index(): View
    {
        return view('frontend.services');
    }
}

