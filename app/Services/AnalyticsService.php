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
    public function getPeakHoursData($startDate, $endDate = null): array
    {
        $query = Consultation::where('created_at', '>=', $startDate);
        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        $peakHours = $query->select(DB::raw('HOUR(created_at) as hour, COUNT(id) as count'))
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
    public function getVisitVolumeData(string $timeFilter, $startDate = null, $endDate = null): array
    {
        if ($timeFilter === 'custom' && $startDate && $endDate) {
            $diffDays = $startDate->diffInDays($endDate);
            if ($diffDays <= 1) {
                return $this->getPeakHoursData($startDate, $endDate);
            } elseif ($diffDays <= 35) {
                $visits = Consultation::whereBetween('created_at', [$startDate, $endDate])
                    ->select(DB::raw('DATE(created_at) as date, COUNT(id) as count'))
                    ->groupBy('date')
                    ->pluck('count', 'date');

                $labels = [];
                $counts = [];
                $curr = clone $startDate;
                while ($curr <= $endDate) {
                    $labels[] = $curr->format('M d');
                    $counts[] = (int) $visits->get($curr->format('Y-m-d'), 0);
                    $curr->addDay();
                }
                return ['labels' => $labels, 'data' => $counts];
            } else {
                $visits = Consultation::whereBetween('created_at', [$startDate, $endDate])
                    ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(id) as count'))
                    ->groupBy('month')
                    ->pluck('count', 'month');

                $labels = [];
                $counts = [];
                $curr = (clone $startDate)->startOfMonth();
                while ($curr <= $endDate) {
                    $labels[] = $curr->format('M Y');
                    $counts[] = (int) $visits->get($curr->format('Y-m'), 0);
                    $curr->addMonth();
                }
                return ['labels' => $labels, 'data' => $counts];
            }
        }

        if ($timeFilter === 'today') {
            return $this->getPeakHoursData(now()->startOfDay(), now()->endOfDay());
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

    /**
     * Compute comprehensive patient census metrics.
     */
    public function getPatientCensus($startDate, $previousStartDate = null, $previousEndDate = null): array
    {
        $totalRegistered = Patient::count();
        $newRegistered = Patient::where('created_at', '>=', $startDate)->count();

        $uniqueServed = Consultation::where('created_at', '>=', $startDate)
            ->distinct('patient_id')
            ->count('patient_id');

        $totalConsultations = Consultation::where('created_at', '>=', $startDate)->count();

        $maleCount = Patient::where('sex', 'Male')->count();
        $femaleCount = Patient::where('sex', 'Female')->count();
        $philhealthCount = Patient::whereNotNull('philhealth_number')->count();

        $malePercentage = $totalRegistered > 0 ? round(($maleCount / $totalRegistered) * 100, 1) : 0;
        $femalePercentage = $totalRegistered > 0 ? round(($femaleCount / $totalRegistered) * 100, 1) : 0;
        $philhealthPercentage = $totalRegistered > 0 ? round(($philhealthCount / $totalRegistered) * 100, 1) : 0;

        $previousConsultations = 0;
        if ($previousStartDate && $previousEndDate) {
            $previousConsultations = Consultation::whereBetween('created_at', [$previousStartDate, $previousEndDate])->count();
        }

        $trendPercentage = 0;
        if ($previousConsultations > 0) {
            $trendPercentage = round((($totalConsultations - $previousConsultations) / $previousConsultations) * 100, 1);
        } elseif ($totalConsultations > 0) {
            $trendPercentage = 100;
        }

        return [
            'total_registered' => $totalRegistered,
            'new_registered' => $newRegistered,
            'unique_served' => $uniqueServed,
            'unique_patients_served' => $uniqueServed,
            'total_consultations' => $totalConsultations,
            'male_registered' => $maleCount,
            'female_registered' => $femaleCount,
            'male_percentage' => $malePercentage,
            'female_percentage' => $femalePercentage,
            'philhealth_registered' => $philhealthCount,
            'philhealth_count' => $philhealthCount,
            'philhealth_percentage' => $philhealthPercentage,
            'trend_percentage' => $trendPercentage,
        ];
    }

    /**
     * Get detailed classification breakdown for both all-time registered and period served.
     */
    public function getClassificationBreakdown($startDate): array
    {
        $totalRegistered = Patient::count();

        $registeredCounts = [
            'pediatric' => Patient::where('classification', 'Pediatric')->count(),
            'regular_adult' => Patient::where('classification', 'Regular Adult')->count(),
            'senior_citizen' => Patient::where('classification', 'Senior Citizen')->count(),
        ];
        $registeredPwd = Patient::where('is_pwd', true)->count();

        $servedByClass = Consultation::join('patients', 'consultations.patient_id', '=', 'patients.patient_id')
            ->where('consultations.created_at', '>=', $startDate)
            ->groupBy('patients.classification')
            ->selectRaw('patients.classification, COUNT(DISTINCT consultations.patient_id) as count')
            ->pluck('count', 'classification');

        $uniqueServedTotal = Consultation::where('created_at', '>=', $startDate)
            ->distinct('patient_id')
            ->count('patient_id');

        $servedCounts = [
            'pediatric' => (int) ($servedByClass['Pediatric'] ?? 0),
            'regular_adult' => (int) ($servedByClass['Regular Adult'] ?? 0),
            'senior_citizen' => (int) ($servedByClass['Senior Citizen'] ?? 0),
        ];

        $servedPwd = (int) Consultation::join('patients', 'consultations.patient_id', '=', 'patients.patient_id')
            ->where('consultations.created_at', '>=', $startDate)
            ->where('patients.is_pwd', true)
            ->distinct('consultations.patient_id')
            ->count('consultations.patient_id');

        $categories = [];
        $labels = [
            'pediatric' => 'Pediatric',
            'regular_adult' => 'Regular Adult',
            'senior_citizen' => 'Senior Citizen',
        ];

        foreach (['pediatric', 'regular_adult', 'senior_citizen'] as $key) {
            $regCount = $registeredCounts[$key];
            $regPct = $totalRegistered > 0 ? round(($regCount / $totalRegistered) * 100, 1) : 0;
            $srvCount = $servedCounts[$key];
            $srvPct = $uniqueServedTotal > 0 ? round(($srvCount / $uniqueServedTotal) * 100, 1) : 0;

            $data = [
                'label' => $labels[$key],
                'registered_count' => $regCount,
                'registered_percentage' => $regPct,
                'registered_pct' => $regPct,
                'served_count' => $srvCount,
                'served_percentage' => $srvPct,
                'served_pct' => $srvPct,
            ];

            $categories[$key] = $data;
        }

        $pwdData = [
            'registered_count' => $registeredPwd,
            'registered_percentage' => $totalRegistered > 0 ? round(($registeredPwd / $totalRegistered) * 100, 1) : 0,
            'served_count' => $servedPwd,
            'served_percentage' => $uniqueServedTotal > 0 ? round(($servedPwd / $uniqueServedTotal) * 100, 1) : 0,
        ];

        return [
            'total_registered' => $totalRegistered,
            'unique_served_total' => $uniqueServedTotal,
            'categories' => $categories,
            'pwd' => $pwdData,
            'donut_chart' => [
                'labels' => ['Pediatric', 'Regular Adult', 'Senior Citizen'],
                'data' => [$servedCounts['pediatric'], $servedCounts['regular_adult'], $servedCounts['senior_citizen']],
                'total' => $uniqueServedTotal,
            ],
        ];
    }

    /**
     * Get Top Diagnoses (Leading Causes of Morbidity) with counts and percentages.
     */
    public function getTopDiagnoses($startDate, int $limit = 10)
    {
        $top = Consultation::whereNotNull('diagnosis')
            ->where('diagnosis', '!=', '')
            ->where('created_at', '>=', $startDate)
            ->select(DB::raw('diagnosis, COUNT(id) as count'))
            ->groupBy('diagnosis')
            ->orderByDesc('count')
            ->limit($limit)
            ->get();

        $totalDiagnosed = Consultation::whereNotNull('diagnosis')
            ->where('diagnosis', '!=', '')
            ->where('created_at', '>=', $startDate)
            ->count();

        return $top->map(function ($item) use ($totalDiagnosed) {
            $item->percentage = $totalDiagnosed > 0 ? round(($item->count / $totalDiagnosed) * 100, 1) : 0;
            return $item;
        });
    }

    /**
     * Inventory status and restocking demand alerts.
     */
    public function getInventoryAlerts(): array
    {
        $totalActive = \App\Models\Medicine::active()->count();

        $medicinesWithStock = \App\Models\Medicine::active()
            ->withSum(['batches as active_stock' => function ($q) {
                $q->where('quantity', '>', 0)
                  ->where('status', 'active')
                  ->whereDate('expiration_date', '>=', today());
            }], 'quantity')
            ->get();

        $outOfStock = $medicinesWithStock->filter(fn ($m) => ($m->active_stock ?? 0) <= 0)->count();
        $lowStock = $medicinesWithStock->filter(fn ($m) => ($m->active_stock ?? 0) > 0 && ($m->active_stock ?? 0) <= 20)->count();

        $expiringSoon = \App\Models\MedicineBatch::where('quantity', '>', 0)
            ->where('status', 'active')
            ->whereDate('expiration_date', '>', today())
            ->whereDate('expiration_date', '<=', today()->addDays(30))
            ->count();

        $expiredBatches = \App\Models\MedicineBatch::where(function ($q) {
            $q->where('status', 'expired')
              ->orWhere(function ($sub) {
                  $sub->where('status', 'active')
                      ->whereDate('expiration_date', '<=', today());
              });
        })->count();

        $pendingPrescriptions = \App\Models\Prescription::whereIn('status', ['pending', 'partially_dispensed'])->count();

        return [
            'total_medicines' => $totalActive,
            'out_of_stock' => $outOfStock,
            'low_stock' => $lowStock,
            'expiring_soon' => $expiringSoon,
            'expired_batches' => $expiredBatches,
            'pending_prescriptions' => $pendingPrescriptions,
        ];
    }

    /**
     * Summary of patient follow-ups.
     */
    public function getFollowupStats(): array
    {
        return [
            'overdue' => Consultation::overdueFollowups()->count(),
            'due_today' => Consultation::dueTodayFollowups()->count(),
            'upcoming_7_days' => Consultation::upcomingFollowups(7)->count(),
        ];
    }

    /**
     * Summary of staff headcount and duty status.
     */
    public function getStaffSummary(): array
    {
        $totalActive = \App\Models\User::count();
        $present = \App\Models\User::present()->count();
        $totalDoctors = \App\Models\User::whereIn('role', ['regular_doctor', 'pedia_doctor'])->count();
        $presentDoctors = \App\Models\User::whereIn('role', ['regular_doctor', 'pedia_doctor'])->present()->count();
        $totalNurses = \App\Models\User::whereIn('role', ['nurse', 'clinical_nurse', 'vitals_nurse'])->count();
        $presentNurses = \App\Models\User::whereIn('role', ['nurse', 'clinical_nurse', 'vitals_nurse'])->present()->count();
        $trashedCount = \App\Models\User::onlyTrashed()->count();

        return [
            'total' => $totalActive,
            'on_duty' => $present,
            'total_doctors' => $totalDoctors,
            'doctors_total' => $totalDoctors,
            'doctors_present' => $presentDoctors,
            'total_nurses' => $totalNurses,
            'nurses_total' => $totalNurses,
            'nurses_present' => $presentNurses,
            'deactivated' => $trashedCount,
        ];
    }

    /**
     * Summary of security audit events and logins.
     */
    public function getSecurityAuditStats($startDate): array
    {
        $totalEvents = \App\Models\AuditLog::where('created_at', '>=', $startDate)->count();
        $successfulLogins = \App\Models\AuditLog::where('action', 'login')->where('created_at', '>=', $startDate)->count();
        $failedLogins = \App\Models\AuditLog::where('action', 'failed_login')->where('created_at', '>=', $startDate)->count();
        $deletions = \App\Models\AuditLog::where('action', 'like', 'deleted%')->where('created_at', '>=', $startDate)->count();
        $patientUpdates = \App\Models\AuditLog::where('model_type', 'like', '%Patient%')->where('action', 'like', 'updated%')->where('created_at', '>=', $startDate)->count();

        return [
            'total_events' => $totalEvents,
            'successful_logins' => $successfulLogins,
            'logins' => $successfulLogins,
            'failed_logins' => $failedLogins,
            'critical_actions' => $deletions,
            'deletions' => $deletions,
            'patient_updates' => $patientUpdates,
        ];
    }

    /**
     * Summary of announcements.
     */
    public function getAnnouncementsSummary(): array
    {
        return [
            'published' => \App\Models\Announcement::where('status', 'published')->count(),
            'pending' => \App\Models\Announcement::where('status', 'pending')->count(),
            'draft' => \App\Models\Announcement::where('status', 'draft')->count(),
            'upcoming_events' => \App\Models\Announcement::whereNotNull('event_date')
                ->whereBetween('event_date', [today(), today()->addDays(7)])
                ->count(),
        ];
    }
}

