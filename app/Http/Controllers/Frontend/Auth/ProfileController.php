<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('frontend.auth.profile');
    }
}
