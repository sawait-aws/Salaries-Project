<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login'); // Return the login view
    }

    public function store(LoginRequest $request)
{
    $request->authenticate(); // Authenticate the user

    $request->session()->regenerate(); // Regenerate session for security

    // Redirect based on user role
    $user = Auth::user();

    if ($user->role === 'manager') {
        return redirect()->route('manager.dashboard');
    } elseif ($user->role === 'employee') {
        return redirect()->route('employee.dashboard');
    }

    // Default redirect if role doesn't match
    return redirect()->route('login');
}


    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout(); // Log the user out

        $request->session()->invalidate(); // Invalidate the session
        $request->session()->regenerateToken(); // Regenerate the CSRF token

        return redirect('/'); // Redirect to homepage after logout
    }
}
