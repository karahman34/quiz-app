<?php

namespace App\Http\Controllers;

use App\Helpers\TestSession;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Check user in session
        $session = Auth::user()->sessions()
                                ->wherePivot('status', 'on_going')
                                ->orderByDesc('created_at')
                                ->first();
        if (!is_null($session)) {
            TestSession::setCode($session->code);
            TestSession::setWorking(true);
            TestSession::setEndAt($session);

            $now = Carbon::now();
            if ($now > TestSession::getEndAt()) {
                TestSession::setCode(null);
                TestSession::setWorking(false);
                TestSession::setEndAt(null);
            }
        }

        return view('dashboard', [
            'onTest' => TestSession::getWorking(),
            'testCode' => is_null($session) ? null : $session->code,
        ]);
    }
}
