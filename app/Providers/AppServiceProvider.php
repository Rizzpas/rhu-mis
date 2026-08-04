<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            \Illuminate\Support\Facades\View::share('globalServices', \App\Models\Service::all());
        } catch (\Exception $e) {
            // Fails during migration if table doesn't exist yet
        }

        \Illuminate\Support\Facades\Gate::define('view-audit-logs', function (\App\Models\User $user) {
            return $user->hasRole('super_admin');
        });

        \Illuminate\Support\Facades\Gate::define('force-delete', function (\App\Models\User $user) {
            return $user->hasRole('super_admin');
        });

        \Illuminate\Support\Facades\Gate::define('promote-admin', function (\App\Models\User $user) {
            return $user->hasRole('super_admin');
        });

        // Only super_admin can modify or delete admin/super_admin accounts
        \Illuminate\Support\Facades\Gate::define('manage-admins', function (\App\Models\User $user) {
            return $user->hasRole('super_admin');
        });

        // Only super_admin can edit landing page / system content settings
        \Illuminate\Support\Facades\Gate::define('manage-content', function (\App\Models\User $user) {
            return $user->hasRole('super_admin');
        });

        \Illuminate\Support\Facades\RateLimiter::for('otp', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perHour(3)->by($request->email ?: $request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('booking', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perDay(50)->by($request->ip());
        });
    }
}
