<?php

namespace App\Services;

use Carbon\Carbon;

class ClinicScheduleService
{
    /**
     * Check if the RHU clinic is closed on a given date.
     *
     * @param  \Carbon\Carbon|string  $date
     * @param  string|null  $reason  Passed by reference to receive the closure description
     * @return bool
     */
    public static function isClinicClosed($date, &$reason = null): bool
    {
        $carbon = $date instanceof Carbon ? $date : Carbon::parse($date);
        $operatingDays = config('clinic.operating_days', [1, 2, 3, 4, 5]);

        // 1. Weekend / Non-operating Day Check
        if (! in_array($carbon->dayOfWeek, $operatingDays)) {
            $reason = 'Weekend / Non-Operating Day';
            return true;
        }

        // 2. Specific Date Closures (e.g. LGU Holiday, Sanitization)
        $specificClosures = config('clinic.specific_closures', []);
        $dateKey = $carbon->toDateString();
        if (isset($specificClosures[$dateKey])) {
            $reason = $specificClosures[$dateKey];
            return true;
        }

        // 3. Annual Statutory Public Holidays (MM-DD)
        $annualHolidays = config('clinic.annual_holidays', []);
        $monthDayKey = $carbon->format('m-d');
        if (isset($annualHolidays[$monthDayKey])) {
            $reason = $annualHolidays[$monthDayKey];
            return true;
        }

        $reason = null;
        return false;
    }
}
