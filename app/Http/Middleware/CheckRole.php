<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
{
    if (!Auth::check() || Auth::user()->role !== $role) {
        if (Auth::user()->role == 'manager') {
            return redirect()->route('manager.dashboard')->with('showUnauthorizedModal', true);
        } elseif (Auth::user()->role == 'employee') {
            return redirect()->route('employee.dashboard')->with('showUnauthorizedModal', true);
        } else {
            return redirect()->route('login')->with('showUnauthorizedModal', true);
        }
    }

    return $next($request);
}

}
