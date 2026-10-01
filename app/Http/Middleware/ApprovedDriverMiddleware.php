<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApprovedDriverMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Only allow:  auth + user_type == 2 + driver_status == approved
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Must be a driver (user_type = 2)
        if ($user->user_type != 2) {
            abort(403, 'Unauthorized access. Only drivers can access this area.');
        }

        // Must be an approved driver
        if ($user->driver_status !== 'approved') {
            return redirect()->route('driver.pending');
        }

        return $next($request);
    }
}
