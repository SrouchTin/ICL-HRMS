<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BranchMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int|string  $branchId
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $branchId)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Check if user belongs to the specified branch
        if (Auth::user()->branch_id != $branchId) {
            abort(403, 'You do not have access to this branch.');
        }

        return $next($request);
    }
}

