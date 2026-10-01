<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\AuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminDriverController;
use App\Http\Controllers\Driver\DriverDashboardController;

/*
|--------------------------------------------------------------------------
| Public Frontend Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/trips', [HomeController::class, 'trips'])->name('trips.index');
Route::get('/booking', [HomeController::class, 'booking'])->name('booking.index');

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
});

/*
|--------------------------------------------------------------------------
| Protected Approved Driver Routes (Approved Drivers Only)
|--------------------------------------------------------------------------
*/
Route::middleware(['driver.approved'])->prefix('driver')->group(function () {
    Route::get('/dashboard', [DriverDashboardController::class, 'index'])->name('driver.dashboard');
});
