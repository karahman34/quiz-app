<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Check user in session
        $session= Auth::user()->sessions()->wherePivot('status', 'on_going')->first();
        if (!is_null($session)) {
            session(['session.working' => true]);
            session(['session.code' => $session->code]);
        }

        return view('dashboard');
    }
}
