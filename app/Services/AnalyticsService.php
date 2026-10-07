<?php

namespace App\Services;

use App\Models\Consultation;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Compute hourly distribution of patient visits starting from $startDate.
     */
    public function getPeakHoursData($startDate): array
    {
        $peakHours = Consultation::where('created_at', '>=', $startDate)
            ->select(DB::raw('HOUR(created_at) as hour, COUNT(id) as count'))
            ->groupBy('hour')
            ->pluck('count', 'hour');

        $minHour = 6;  // 6:00 AM
        $maxHour = 18; // 6:00 PM

        if ($peakHours->isNotEmpty()) {
            $recordedHours = $peakHours->keys()->map(fn ($h) => (int) $h)->all();
            $earliest = min($recordedHours);
            $latest = max($recordedHours);
            if ($earliest < $minHour && $earliest >= 5) {
                $minHour = $earliest;
            }
            if ($latest > $maxHour && $latest <= 21) {
                $maxHour = $latest;
            }
        }

        $labels = [];
        $counts = [];
        for ($i = $minHour; $i <= $maxHour; $i++) {
            $labels[] = $i > 12 ? ($i - 12).' PM' : ($i == 12 ? '12 PM' : $i.' AM');
            $counts[] = (int) $peakHours->get($i, 0);
        }

        return ['labels' => $labels, 'data' => $counts];
    }

    /**
     * Compute dynamic Visit Volume chart data based on timeframe filter.
     */
    public function getVisitVolumeData(string $timeFilter): array
    {
        if ($timeFilter === 'today') {
            return $this->getPeakHoursData(now()->startOfDay());
        }

        if ($timeFilter === 'yearly' || $timeFilter === 'all') {
            $monthsToLookBack = $timeFilter === 'yearly' ? 11 : 23;
            $historyStart = now()->subMonths($monthsToLookBack)->startOfMonth();
            $visits = Consultation::where('created_at', '>=', $historyStart)
                ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(id) as count'))
                ->groupBy('month')
                ->pluck('count', 'month');

            $labels = [];
            $counts = [];
            for ($i = $monthsToLookBack; $i >= 0; $i--) {
                $dateObj = now()->subMonths($i);
                $labels[] = $dateObj->format('M Y');
                $counts[] = $visits->get($dateObj->format('Y-m'), 0);
            }

            return ['labels' => $labels, 'data' => $counts];
        }

        // Daily lookback (weekly = 6 days, monthly = 29 days)
        $daysToLookBack = $timeFilter === 'weekly' ? 6 : 29;
        $historyStart = now()->subDays($daysToLookBack)->startOfDay();
        $visits = Consultation::where('created_at', '>=', $historyStart)
            ->select(DB::raw('DATE(created_at) as date, COUNT(id) as count'))
            ->groupBy('date')
            ->pluck('count', 'date');

        $labels = [];
        $counts = [];
        for ($i = $daysToLookBack; $i >= 0; $i--) {
            $dateObj = now()->subDays($i);
            $labels[] = $dateObj->format('M d');
            $counts[] = $visits->get($dateObj->format('Y-m-d'), 0);
        }

        return ['labels' => $labels, 'data' => $counts];
    }

    /**
     * Aggregated barangay consultation distribution with address fallback.
     */
    public function getBarangayData($startDate): array
    {
        $records = Consultation::join('patients', 'consultations.patient_id', '=', 'patients.patient_id')
            ->where('consultations.created_at', '>=', $startDate)
            ->select('consultations.id', 'patients.barangay', 'patients.address')
            ->get();

        $counts = [];
        foreach ($records as $r) {
            $normalized = Patient::normalizeBarangay($r->barangay, $r->address);
            if ($normalized) {
                $counts[$normalized] = ($counts[$normalized] ?? 0) + 1;
            }
        }

        arsort($counts);

        return array_slice($counts, 0, 10, true);
    }
}
