<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackUserActivity
{
    /**
     * Update the user's last_activity_at on every authenticated request.
     * This is the "passive" heartbeat — even normal page loads reset the idle timer.
     * The JS heartbeat is the "active" one that fires when the browser tab is open but idle.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Only update if more than 60 seconds since last update (avoid DB spam)
            if (! $user->last_activity_at || $user->last_activity_at->diffInSeconds(now()) > 60) {
                $user->update(['last_activity_at' => now()]);
            }
        }

        return $next($request);
    }
}
