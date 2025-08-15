<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminAuthenticatedSessionController extends Controller
{
    public function create()
    {
        // Show the admin login form
        return view('admin.auth.login');
    }

    public function store(Request $request)
    {
        // Handle the admin login logic
        $credentials = $request->only('email', 'password');

        if (auth()->guard('admin')->attempt($credentials)) {
            return redirect()->route('admin.index');
        }

        return redirect()->back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }
}
