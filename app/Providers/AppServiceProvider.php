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
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            try {
                $facilities = \Illuminate\Support\Facades\Cache::remember('global_facility_units', 3600, function () {
                    return \App\Models\FacilityUnit::active()->ordered()->get();
                });
                $view->with('globalFacilityUnits', $facilities);
            } catch (\Throwable $e) {
                $view->with('globalFacilityUnits', collect());
            }
        });

        try {
            \Illuminate\Support\Facades\View::share('globalServices', \App\Models\Service::all());
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\View::share('globalServices', collect());
        }

        \Illuminate\Support\Facades\Gate::define('view-audit-logs', function (\App\Models\User $user) {
            return $user->hasRole('super_admin');
        });

        \Illuminate\Support\Facades\Gate::define('force-delete', function (\App\Models\User $user) {
            return $user->hasRole('admin', 'super_admin');
        });

        \Illuminate\Support\Facades\Gate::define('truncate-archive', function (\App\Models\User $user) {
            return $user->hasRole('super_admin');
        });

        \Illuminate\Support\Facades\Gate::define('promote-admin', function (\App\Models\User $user) {
            return $user->hasRole('super_admin');
        });

        // Only super_admin can modify or delete admin/super_admin accounts
        \Illuminate\Support\Facades\Gate::define('manage-admins', function (\App\Models\User $user) {
            return $user->hasRole('super_admin');
        });

        // Allow admin and super_admin to edit landing page / system content settings
        \Illuminate\Support\Facades\Gate::define('manage-content', function (\App\Models\User $user) {
            return $user->hasRole('admin', 'super_admin');
        });

        \Illuminate\Support\Facades\RateLimiter::for('otp', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perHour(3)->by($request->email ?: $request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('booking', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perDay(50)->by($request->ip());
        });
    }
}
