<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
   public function handle($request, Closure $next, $role)
{
    if (!Auth::check()) {
        return redirect('/login');
    }

    $userRole = Auth::user()->role?->name;

    if ($userRole !== $role) {
        abort(403, 'អ្នកមិនមានសិទ្ធិចូលទំព័រនេះទេ។');
    }

    return $next($request);
}
}

