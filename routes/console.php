<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

Schedule::command('model:prune')->daily();
Schedule::command('appointments:process-no-shows')->dailyAt('23:55');
Schedule::command('prescriptions:expire-stale')->dailyAt('23:57');
Schedule::command('fix:stuck-appointments')->dailyAt('23:58');
Schedule::command('app:cleanup-vitals')->dailyAt('23:59');
Schedule::command('appointments:send-reminders')->dailyAt('08:00');
Schedule::command('staff:auto-logout --minutes=30')->everyFiveMinutes();
Schedule::command('staff:sync-schedule-status')->everyMinute();
Schedule::command('patients:update-classifications')->dailyAt('00:05');

