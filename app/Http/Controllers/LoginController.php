<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Show login page
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt([
            'emp_id' => $request->user_id,
            'password' => $request->password,
        ])) {
            $request->session()->regenerate();

            $user = Auth::user();

            $reportingTo = trim(sprintf(
                '%s - %s_%s_%s',
                $user->mgr_level,
                $user->reporting_mgr_name,
                $user->reporting_mgr_id,
                $user->mgr_designation
            ));

            session([
                'emp_id'       => $user->emp_id,
                'emp_name'     => $user->emp_name,
                'reporting_to' => $reportingTo,
                'is_admin'     => $user->is_admin, // 👈 IMPORTANT
            ]);
            $request->session()->forget('url.intended');

            return redirect()->to(
                $user->is_admin ? route('admin.dashboard') : '/five'
            );
        }


        return back()->withErrors([
            'user_id' => 'Invalid Employee ID or Password',
        ]);
    }



    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
    // public function logout(Request $request)
    // {
    //     $userId = Auth::id();

    //     Cache::tags(["user:{$userId}"])->flush(); // if using tagged cache

    //     Auth::logout();

    //     $request->session()->invalidate();
    //     $request->session()->regenerateToken();

    //     return redirect('/login')->withHeaders([
    //         'Cache-Control' => 'no-cache, no-store, max-age=0, must-revalidate',
    //         'Pragma' => 'no-cache',
    //         'Expires' => 'Fri, 01 Jan 1990 00:00:00 GMT',
    //     ]);
    // }
}
