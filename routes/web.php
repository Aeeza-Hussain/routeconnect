<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminDriverController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminVehicleController;
use App\Http\Controllers\Admin\AdminRouteController;
use App\Http\Controllers\Admin\AdminStopController;
use App\Http\Controllers\Admin\AdminTripController;
use App\Http\Controllers\Admin\AdminTripStopController;
use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Driver\DriverDashboardController;
use App\Http\Controllers\Driver\DriverTripController;
use App\Http\Controllers\Driver\DriverBookingController;
use App\Http\Controllers\Driver\DriverMessageController;
use App\Http\Controllers\Driver\DriverProfileController;

use App\Http\Controllers\Frontend\BookingController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Public Frontend Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/trips', [HomeController::class, 'trips'])->name('trips.index');
Route::get('/trips/{trip}', [HomeController::class, 'showTrip'])->name('trips.show')->whereNumber('trip');

/*
|--------------------------------------------------------------------------
| Passenger Seat Booking Routes (user_type == 0)
|--------------------------------------------------------------------------
*/
Route::get('/booking', [BookingController::class, 'index'])->name('booking.index');

Route::middleware(['passenger'])->group(function () {
    Route::get('/my-bookings', [BookingController::class, 'myBookings'])->name('passenger.bookings.index');
    Route::get('/my-bookings/{booking}', [BookingController::class, 'showBooking'])->name('passenger.bookings.show')->whereNumber('booking');
    Route::post('/my-bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('passenger.bookings.cancel')->whereNumber('booking');
    Route::delete('/my-bookings/{booking}/cancel', [BookingController::class, 'cancel'])->whereNumber('booking');
    Route::delete('/my-bookings/{booking}', [BookingController::class, 'cancel'])->whereNumber('booking');

    Route::get('/booking/{trip}', [BookingController::class, 'create'])->name('booking.create')->whereNumber('trip');
    Route::post('/booking/{trip}', [BookingController::class, 'store'])->name('booking.store')->whereNumber('trip');
    Route::get('/booking/confirmation/{booking}', [BookingController::class, 'confirmation'])->name('booking.confirmation')->whereNumber('booking');
});

/*
|--------------------------------------------------------------------------
| Authenticated User Notification Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read')->whereNumber('notification');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->whereNumber('notification');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/driver/register', [AuthController::class, 'showDriverRegisterForm'])->name('driver.register');
Route::post('/driver/register', [AuthController::class, 'registerDriver'])->name('driver.register.submit');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Driver Pending Status Route (Authenticated Drivers)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->get('/driver/pending', [DriverDashboardController::class, 'pending'])->name('driver.pending');

/*
|--------------------------------------------------------------------------
| Protected Admin Routes (Admin Only)
|--------------------------------------------------------------------------
*/
Route::middleware(['admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    
    // Driver Applications Management
    Route::get('/drivers/applications', [AdminDriverController::class, 'applications'])->name('admin.drivers.applications');
    Route::get('/drivers/applications/{id}', [AdminDriverController::class, 'show'])->name('admin.drivers.show');
    Route::post('/drivers/{id}/approve', [AdminDriverController::class, 'approve'])->name('admin.drivers.approve');
    Route::post('/drivers/{id}/reject', [AdminDriverController::class, 'reject'])->name('admin.drivers.reject');
    Route::get('/drivers', [AdminDriverController::class, 'index'])->name('admin.drivers.index');

    // Users CRUD Management
    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{id}', [AdminUserController::class, 'show'])->name('admin.users.show');
    Route::get('/users/{id}/edit', [AdminUserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{id}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');

    // Vehicles CRUD Management
    Route::get('/vehicles', [AdminVehicleController::class, 'index'])->name('admin.vehicles.index');
    Route::get('/vehicles/create', [AdminVehicleController::class, 'create'])->name('admin.vehicles.create');
    Route::post('/vehicles', [AdminVehicleController::class, 'store'])->name('admin.vehicles.store');
    Route::get('/vehicles/{id}', [AdminVehicleController::class, 'show'])->name('admin.vehicles.show');
    Route::get('/vehicles/{id}/edit', [AdminVehicleController::class, 'edit'])->name('admin.vehicles.edit');
    Route::put('/vehicles/{id}', [AdminVehicleController::class, 'update'])->name('admin.vehicles.update');
    Route::delete('/vehicles/{id}', [AdminVehicleController::class, 'destroy'])->name('admin.vehicles.destroy');

    // Routes CRUD Management
    Route::get('/routes', [AdminRouteController::class, 'index'])->name('admin.routes.index');
    Route::get('/routes/create', [AdminRouteController::class, 'create'])->name('admin.routes.create');
    Route::post('/routes', [AdminRouteController::class, 'store'])->name('admin.routes.store');
    Route::get('/routes/{id}', [AdminRouteController::class, 'show'])->name('admin.routes.show');
    Route::get('/routes/{id}/edit', [AdminRouteController::class, 'edit'])->name('admin.routes.edit');
    Route::put('/routes/{id}', [AdminRouteController::class, 'update'])->name('admin.routes.update');
    Route::delete('/routes/{id}', [AdminRouteController::class, 'destroy'])->name('admin.routes.destroy');

    // Route Stops Management (Assign, Order, Remove)
    Route::post('/routes/{id}/stops', [AdminRouteController::class, 'addStop'])->name('admin.routes.stops.add');
    Route::put('/routes/{id}/stops/{stopId}', [AdminRouteController::class, 'updateStopOrder'])->name('admin.routes.stops.update-order');
    Route::delete('/routes/{id}/stops/{stopId}', [AdminRouteController::class, 'removeStop'])->name('admin.routes.stops.remove');

    // Stops CRUD Management
    Route::get('/stops', [AdminStopController::class, 'index'])->name('admin.stops.index');
    Route::get('/stops/create', [AdminStopController::class, 'create'])->name('admin.stops.create');
    Route::post('/stops', [AdminStopController::class, 'store'])->name('admin.stops.store');
    Route::get('/stops/{id}', [AdminStopController::class, 'show'])->name('admin.stops.show');
    Route::get('/stops/{id}/edit', [AdminStopController::class, 'edit'])->name('admin.stops.edit');
    Route::put('/stops/{id}', [AdminStopController::class, 'update'])->name('admin.stops.update');
    Route::delete('/stops/{id}', [AdminStopController::class, 'destroy'])->name('admin.stops.destroy');

    // Trips CRUD Management
    Route::get('/trips', [AdminTripController::class, 'index'])->name('admin.trips.index');
    Route::get('/trips/create', [AdminTripController::class, 'create'])->name('admin.trips.create');
    Route::post('/trips', [AdminTripController::class, 'store'])->name('admin.trips.store');
    Route::get('/trips/{id}', [AdminTripController::class, 'show'])->name('admin.trips.show');
    Route::get('/trips/{id}/edit', [AdminTripController::class, 'edit'])->name('admin.trips.edit');
    Route::put('/trips/{id}', [AdminTripController::class, 'update'])->name('admin.trips.update');
    Route::delete('/trips/{id}', [AdminTripController::class, 'destroy'])->name('admin.trips.destroy');

    // Trip Stops Management (expected arrival times per stop)
    Route::get('/trips/{trip}/stops', [AdminTripStopController::class, 'index'])->name('admin.trips.stops.index');
    Route::post('/trips/{trip}/stops', [AdminTripStopController::class, 'store'])->name('admin.trips.stops.store');
    Route::put('/trips/{trip}/stops/{tripStop}', [AdminTripStopController::class, 'update'])->name('admin.trips.stops.update');
    Route::delete('/trips/{trip}/stops/{tripStop}', [AdminTripStopController::class, 'destroy'])->name('admin.trips.stops.destroy');

    // Bookings CRUD Management
    Route::get('/bookings', [AdminBookingController::class, 'index'])->name('admin.bookings.index');
    Route::get('/bookings/{id}', [AdminBookingController::class, 'show'])->name('admin.bookings.show');
    Route::post('/bookings/{id}/cancel', [AdminBookingController::class, 'cancel'])->name('admin.bookings.cancel');

    // Platform Modules
    Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('admin.settings.index');
});

/*
|--------------------------------------------------------------------------
| Protected Approved Driver Routes (Approved Drivers Only)
|--------------------------------------------------------------------------
*/
Route::middleware(['driver.approved'])->prefix('driver')->group(function () {
    Route::get('/dashboard', [DriverDashboardController::class, 'index'])->name('driver.dashboard');

    // Profile & Settings
    Route::get('/profile', [DriverProfileController::class, 'profile'])->name('driver.profile');
    Route::put('/profile', [DriverProfileController::class, 'updateProfile'])->name('driver.profile.update');
    Route::get('/settings', [DriverProfileController::class, 'settings'])->name('driver.settings');
    Route::put('/settings/password', [DriverProfileController::class, 'updatePassword'])->name('driver.settings.password');

    // Assigned Vehicle (read-only for own vehicle)
    Route::get('/vehicle', [DriverDashboardController::class, 'vehicle'])->name('driver.vehicle');

    // Trips (view, details with stops & timings, edit)
    Route::get('/trips', [DriverTripController::class, 'index'])->name('driver.trips');
    Route::get('/trips/{trip}', [DriverTripController::class, 'show'])->name('driver.trips.show');
    Route::get('/trips/{trip}/edit', [DriverTripController::class, 'edit'])->name('driver.trips.edit');
    Route::put('/trips/{trip}', [DriverTripController::class, 'update'])->name('driver.trips.update');

    // Bookings (view passenger reservations for own trips)
    Route::get('/bookings', [DriverBookingController::class, 'index'])->name('driver.bookings');
    Route::get('/bookings/{booking}', [DriverBookingController::class, 'show'])->name('driver.bookings.show');

    // Trip Messages (post message for driver's own trip)
    Route::get('/messages', [DriverMessageController::class, 'index'])->name('driver.messages');
    Route::post('/messages', [DriverMessageController::class, 'store'])->name('driver.messages.store');
    Route::delete('/messages/{message}', [DriverMessageController::class, 'destroy'])->name('driver.messages.destroy');
});
