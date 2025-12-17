<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // គាំទ្រ column ឈ្មោះអ្វីក៏បាន (name, role_name, title...)
        $roleName = $user->role?->name 
                 ?? $user->role?->role_name 
                 ?? $user->role?->title 
                 ?? null;

        if (!$roleName) {
            abort(403, 'គណនីរបស់អ្នកគ្មានតួនាទី។');
        }

        $userRole = strtolower(trim($roleName));

        // ពិនិត្យ role ដែលអនុញ្ញាត
        foreach ($roles as $allowed) {
            if ($userRole === strtolower(trim($allowed))) {
                return $next($request);
            }
        }

        abort(403, 'អ្នកមិនមានសិទ្ធិចូលទំព័រនេះទេ។');
    }
}