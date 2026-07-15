<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HeartbeatController extends Controller
{
    /**
     * Receive a heartbeat ping from the authenticated user's browser.
     * Updates `last_activity_at` so the auto-logout command knows
     * the user is still active.
     */
    public function ping(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            $user->update([
                'last_activity_at' => now(),
            ]);
        }

        return response()->json(['status' => 'ok', 'timestamp' => now()->toIso8601String()]);
    }
}
