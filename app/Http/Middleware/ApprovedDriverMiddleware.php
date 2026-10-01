<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApprovedDriverMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (!$user->isDriver()) {
            abort(403, 'Unauthorized access. Only drivers can access this area.');
        }

        if (!$user->isApprovedDriver()) {
            return redirect()->route('driver.pending');
        }

        return $next($request);
    }
}
