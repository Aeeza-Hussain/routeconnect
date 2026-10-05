<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'driver.approved' => \App\Http\Middleware\ApprovedDriverMiddleware::class,
            'passenger' => \App\Http\Middleware\PassengerMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            $redirectUrl = $request->is('login') ? route('login') : (url()->previous() ?: route('login'));
            return redirect($redirectUrl)
                ->withErrors(['email' => 'Your session expired due to inactivity. Please try logging in again.'])
                ->withInput($request->except('password', '_token'));
        });
    })->create();
