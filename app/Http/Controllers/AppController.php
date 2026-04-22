<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AppController extends Controller
{
    public function version()
    {
        return response()->json([
            "version" => env('APP_VERSION'),
            "apk_url" => "https://five.myprisma.in/apk/app-release.apk",
            "force_update" => true,
            "message" => "New update available. Please update the app."
        ]);
    }
}
