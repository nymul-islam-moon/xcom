<?php

namespace App\Http\Controllers\Shop\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ShopAuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('backend.shop.auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (auth()->guard('shop')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // go to intended shop page or dashboard
            return redirect()->intended(route('shop.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ])->onlyInput('email');
    }

    public function destroy(Request $request)
    {
        Auth::guard('shop')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('shop.login');
    }
}
