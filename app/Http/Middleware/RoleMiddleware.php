<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Map a role string to the user's home URL.
     */
    public static function homeForRole(string $role): string
    {
        return match($role) {
            'super_admin',
            'admin'            => route('admin.dashboard'),
            'information_desk' => route('frontdesk.dashboard'),
            'regular_doctor',
            'pedia_doctor'     => route('doctor.dashboard'),
            'vitals_nurse'     => route('triage.dashboard'),
            'clinical_nurse'   => route('nurse.dashboard'),
            'laboratory',
            'radiology'        => route('lab.dashboard'),
            'pharmacy'         => route('pharmacy.dashboard'),
            default            => route('welcome'),
        };
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user() || !$request->user()->hasRole(...$roles)) {
            if ($request->user()) {
                $home = self::homeForRole($request->user()->role);
                return redirect($home)->with('warning', 'You do not have permission to access that page.');
            }

            return redirect()->route('login');
        }

        return $next($request);
    }
}
