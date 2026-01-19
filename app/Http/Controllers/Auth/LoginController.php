<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Handle login form submission.
     */
    public function authenticate(Request $request)
    {
        // Validate input
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Attempt login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('tweets.index')->with('success', 'Logged in successfully!');
        }

        // If login fails
        return back()->withErrors([
            'email' => 'Invalid credentials provided.',
        ])->onlyInput('email');
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing')->with('success', 'You have been logged out.');
    }
}
