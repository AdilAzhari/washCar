<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBranchAccess
{
    /**
     * Handle an incoming request.
     *
     * Enforce branch scoping for Staff and Managers.
     * Admins bypass all branch restrictions.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // If user is not authenticated, let auth middleware handle it
        if (! $user) {
            return $next($request);
        }

        // Admins bypass all branch restrictions
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Customers don't need branch restrictions
        if ($user->isCustomer()) {
            return $next($request);
        }

        // Staff and Managers must have a branch assigned
        if (($user->isStaff() || $user->isManager()) && ! $user->branch_id) {
            abort(403, 'You must be assigned to a branch to access this resource.');
        }

        // Add user's branch_id to request for easy access in controllers
        $request->merge(['user_branch_id' => $user->branch_id]);

        return $next($request);
    }
}
