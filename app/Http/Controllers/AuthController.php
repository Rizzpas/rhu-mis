<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        // Clear any lingering public appointment session data so it never
        // traps or interferes with the staff authentication flow.
        session()->forget('manage_appointment_id');

        if (Auth::check()) {
            $user = Auth::user();

            return redirect()->to(self::homeRouteForRole($user->role));
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $ipKey = 'login_ip:'.$request->ip();
        $emailKey = 'login_email:'.strtolower($request->email);

        if (RateLimiter::tooManyAttempts($emailKey, 5)) {
            $seconds = RateLimiter::availableIn($emailKey);

            return back()->withErrors(['email' => "Account locked. Please try again in {$seconds} seconds."])->onlyInput('email');
        }

        if (RateLimiter::tooManyAttempts($ipKey, 5)) {
            $seconds = RateLimiter::availableIn($ipKey);

            return back()->withErrors(['email' => "Too many attempts from this IP. Try again in {$seconds} seconds."])->onlyInput('email');
        }

        // Disable persistent remember cookies so accounts auto-logout on browser close
        if (Auth::attempt($credentials, false)) {
            RateLimiter::clear($ipKey);
            RateLimiter::clear($emailKey);

            $request->session()->regenerate();

            // Clean up any public appointment session keys to avoid collisions
            session()->forget('manage_appointment_id');

            $user = Auth::user();

            // ── Presence System: Auto-set "Online" on login ──
            $user->update([
                'status' => 'Online',
                'last_activity_at' => now(),
            ]);

            \App\Models\AuditLog::record('Login', $user);

            return redirect()->to(self::homeRouteForRole($user->role));
        }

        RateLimiter::hit($ipKey, 300); // 5 minutes
        RateLimiter::hit($emailKey, 600); // 10 minutes

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        // ── Presence System: Auto-set "Offline" on logout ──
        if ($user) {
            $isDemoMode = \App\Models\SiteSetting::get('demo_mode') === '1';

            // Only force them offline if we are NOT in demo mode.
            // In demo mode, we want them to stay "Online" so the presenter can log in/out freely.
            if (! $isDemoMode) {
                $user->update([
                    'status' => 'Offline',
                    'last_activity_at' => null,
                ]);
            }

            \App\Models\AuditLog::record('Logout', $user);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public static function homeRouteForRole(string $role): string
    {
        return match ($role) {
            'admin',
            'super_admin' => route('admin.dashboard'),
            'information_desk' => route('frontdesk.dashboard'),
            'regular_doctor',
            'pedia_doctor' => route('doctor.dashboard'),
            'vitals_nurse' => route('triage.dashboard'),
            'clinical_nurse' => route('nurse.dashboard'),
            'laboratory',
            'radiology' => route('lab.dashboard'),
            'pharmacy' => route('pharmacy.dashboard'),
            default => route('welcome'),
        };
    }
}
