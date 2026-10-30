<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\UserContoller;
use App\Http\Controllers\api\DriverController;
use App\Http\Controllers\api\ConversationApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/conversations', [ConversationApiController::class, 'index']);
    Route::get('/conversations/{conversation}/messages', [ConversationApiController::class, 'messages']);
    Route::post('/conversations/{conversation}/messages', [ConversationApiController::class, 'sendMessage']);
});

Route::get('get/user', [UserContoller::class, 'index']);
Route::get('get/driver', [DriverController::class, 'index']);