<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectBasedOnRole
{
    /**
     * Handle an incoming request.
     *
     * After login, redirect to appropriate dashboard based on user role.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $user = $request->user();

        // Only redirect on successful authentication (when accessing /dashboard)
        if ($user && $request->is('dashboard')) {
            if ($user->isCustomer()) {
                return redirect()->route('customer.dashboard');
            } elseif ($user->isStaff()) {
                return redirect()->route('staff.dashboard');
            } elseif ($user->isManager()) {
                return redirect()->route('manager.dashboard');
            } elseif ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }
        }

        return $response;
    }
}
