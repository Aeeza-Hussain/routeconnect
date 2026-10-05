<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PassengerMiddleware
{
    /**
     * Handle an incoming request.
     * Only authenticated passengers (user_type == 0) can book.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->guest(route('login'));
        }

        if ((int) auth()->user()->user_type !== 0) {
            abort(403, 'Unauthorized access. Only passengers can book seats.');
        }

        return $next($request);
    }
}
