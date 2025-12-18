<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBranchResource
{
    /**
     * Handle an incoming request.
     *
     * Validate that route model belongs to user's branch.
     * Prevents staff/managers from accessing other branches' resources.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $parameterName  The route parameter to check (e.g., 'branch', 'bay', 'queue')
     */
    public function handle(Request $request, Closure $next, string $parameterName = 'branch'): Response
    {
        $user = $request->user();

        // Admins and customers bypass this check
        if (!$user || $user->isAdmin() || $user->isCustomer()) {
            return $next($request);
        }

        // Get the model from route
        $model = $request->route($parameterName);

        // If model doesn't exist in route, skip check
        if (!$model) {
            return $next($request);
        }

        // Check if model has branch_id attribute
        if (!isset($model->branch_id)) {
            return $next($request);
        }

        // Staff and Manager can only access resources from their branch
        if (($user->isStaff() || $user->isManager()) && $model->branch_id !== $user->branch_id) {
            abort(403, 'You cannot access resources from other branches.');
        }

        return $next($request);
    }
}
