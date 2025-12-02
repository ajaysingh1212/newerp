<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LoginLog;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers {
        login as traitLogin;
    }

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /* ============================================================
        REQUIRED LOCATION VALIDATION BEFORE LOGIN
    ============================================================ */
    public function login(Request $request)
    {
        if (!$request->latitude || !$request->longitude || !$request->location_address) {
            return back()->with('location_error', 'Location access is required to login.');
        }

        return $this->traitLogin($request);
    }

    /* ============================================================
        AFTER SUCCESSFUL LOGIN
    ============================================================ */
    protected function authenticated(Request $request, $user)
    {
        LoginLog::create([
            'use_id'        => $user->id,
            'logged_in_at'  => now(),
            'logged_in_ip'  => $request->ip(),
            'latitude'      => $request->latitude,
            'longitude'     => $request->longitude,
            'location'      => $request->location_address,
            'device'        => $request->header('User-Agent'),
            'type'          => 'login',
            'created_by_id' => $user->id,
        ]);
    }

    /* ============================================================
        LOGOUT
    ============================================================ */
    public function logout(Request $request)
    {
        $user = auth()->user();

        if ($user) {

            $log = LoginLog::where('use_id', $user->id)
                ->whereNull('logged_out_at')
                ->latest()
                ->first();

            if ($log) {
                $log->update([
                    'logged_out_at' => now(),
                    'logged_out_ip' => $request->ip(),
                    'type'          => 'logout',
                ]);
            }
        }

        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
