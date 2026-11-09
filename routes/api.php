<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ActivityApiController;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\BookingApiController;
use App\Http\Controllers\Api\ConversationApiController;
use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\StopApiController;
use App\Http\Controllers\Api\TravelRequestApiController;
use App\Http\Controllers\Api\TripApiController;
use App\Http\Controllers\Api\UserContoller;
use App\Http\Controllers\Api\VehicleApiController;

/*
|--------------------------------------------------------------------------
| RouteConnect Mobile & External REST API Routes (Flutter Ready)
|--------------------------------------------------------------------------
*/

// Legacy compatibility routes
Route::get('get/user', [UserContoller::class, 'index']);
Route::get('get/driver', [DriverController::class, 'index']);
Route::get('get/driver/{id}', [DriverController::class, 'show']);

// -------------------------------------------------------------------------
// 1. Public Endpoints
// -------------------------------------------------------------------------
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthApiController::class, 'register']);
    Route::post('/login', [AuthApiController::class, 'login']);
});

// Reference data
Route::get('/stops', [StopApiController::class, 'stops']);
Route::get('/routes', [StopApiController::class, 'routes']);

// Shared ride search & details
Route::get('/trips', [TripApiController::class, 'index']);
Route::get('/trips/{trip}', [TripApiController::class, 'show']);

// Private vehicle search & details
Route::get('/vehicles', [VehicleApiController::class, 'index']);
Route::get('/vehicles/{vehicle}', [VehicleApiController::class, 'show']);
Route::get('/vehicles/{vehicle}/availability', [VehicleApiController::class, 'checkAvailability']);

// -------------------------------------------------------------------------
// 2. Protected Endpoints (Laravel Sanctum Bearer Token)
// -------------------------------------------------------------------------
Route::middleware('auth:sanctum')->group(function () {

    // Authentication & Profile
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthApiController::class, 'logout']);
        Route::get('/me', [AuthApiController::class, 'me']);
        Route::put('/profile', [AuthApiController::class, 'updateProfile']);
        Route::post('/apply-driver', [AuthApiController::class, 'applyDriver']);
    });

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Shared Rides - Driver publishing & Passenger booking
    Route::post('/driver/trips', [TripApiController::class, 'store']);
    Route::post('/driver/trips/{trip}/cancel', [TripApiController::class, 'cancel']);
    Route::post('/trips/{trip}/book', [TripApiController::class, 'book']);

    // Vehicles - Driver vehicle registration & Private vehicle booking
    Route::post('/vehicles', [VehicleApiController::class, 'store']);
    Route::post('/vehicles/{vehicle}/book', [VehicleApiController::class, 'bookPrivate']);

    // Bookings management
    Route::get('/bookings', [BookingApiController::class, 'index']);
    Route::get('/bookings/{booking}', [BookingApiController::class, 'show']);
    Route::post('/bookings/{booking}/cancel', [BookingApiController::class, 'cancel']);

    // Travel Requests & Driver Applications
    Route::get('/travel-requests', [TravelRequestApiController::class, 'index']);
    Route::post('/travel-requests', [TravelRequestApiController::class, 'store']);
    Route::get('/travel-requests/{travelRequest}', [TravelRequestApiController::class, 'show']);
    Route::put('/travel-requests/{travelRequest}', [TravelRequestApiController::class, 'update']);
    Route::delete('/travel-requests/{travelRequest}', [TravelRequestApiController::class, 'destroy']);
    Route::post('/travel-requests/{travelRequest}/apply', [TravelRequestApiController::class, 'apply']);
    Route::post('/travel-requests/{travelRequest}/applications/{application}/select', [TravelRequestApiController::class, 'selectApplication']);

    // In-App Messaging & Dynamic Trip Arrangements
    Route::prefix('conversations')->group(function () {
        Route::get('/', [ConversationApiController::class, 'index']);
        Route::post('/start', [ConversationApiController::class, 'start']);
        Route::get('/unread-count', [ConversationApiController::class, 'unreadCount']);
        Route::get('/{conversation}', [ConversationApiController::class, 'show']);
        Route::get('/{conversation}/messages', [ConversationApiController::class, 'messages']);
        Route::post('/{conversation}/messages', [ConversationApiController::class, 'sendMessage']);
        Route::get('/{conversation}/messages/{message}/attachment', [ConversationApiController::class, 'downloadAttachment']);
        Route::post('/{conversation}/propose', [ConversationApiController::class, 'proposeArrangement']);
        Route::post('/{conversation}/confirm', [ConversationApiController::class, 'confirmArrangement']);
        Route::post('/{conversation}/complete', [ConversationApiController::class, 'completeArrangement']);
        Route::post('/{conversation}/cancel', [ConversationApiController::class, 'cancelArrangement']);
        Route::post('/{conversation}/block', [ConversationApiController::class, 'toggleBlock']);
        Route::post('/{conversation}/report', [ConversationApiController::class, 'report']);
    });

    // My Activity Section (Passenger & Driver)
    Route::get('/activity', [ActivityApiController::class, 'index']);
});
