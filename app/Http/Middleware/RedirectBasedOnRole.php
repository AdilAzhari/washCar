<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class RedirectBasedOnRole
{
    /**
     * Handle an incoming request.
     *
     * After login, redirect to appropriate dashboard based on user role.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $user = $request->user();
        if (! $user) {
            return $response;
        }
        if (! $request->is('dashboard')) {
            return $response;
        }
        if ($user->isCustomer()) {
            return redirect()->route('customer.dashboard');
        }
        if ($user->isStaff()) {
            return redirect()->route('staff.dashboard');
        }
        if ($user->isManager()) {
            return redirect()->route('manager.dashboard');
        }
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return $response;
    }
}
