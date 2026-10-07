<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Patient;
use App\Models\PreTriage;
use App\Models\User;
use App\Services\AnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function __construct(
        protected AnalyticsService $analyticsService
    ) {}

    public function index(Request $request)
    {
        $timeFilter = $request->get('time_filter', 'monthly');

        $startDate = match ($timeFilter) {
            'today' => now()->startOfDay(),
            'weekly' => now()->startOfWeek(),
            'monthly' => now()->startOfMonth(),
            default => Carbon::create(2000, 1, 1),
        };

        $visitVolumeData = $this->analyticsService->getVisitVolumeData($timeFilter);
        $peakHoursData = $this->analyticsService->getPeakHoursData($startDate);

        $topDiagnoses = Consultation::whereNotNull('diagnosis')
            ->where('diagnosis', '!=', '')
            ->where('created_at', '>=', $startDate)
            ->select(DB::raw('diagnosis, COUNT(id) as count'))
            ->groupBy('diagnosis')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        $classificationData = Consultation::join('patients', 'consultations.patient_id', '=', 'patients.patient_id')
            ->where('consultations.created_at', '>=', $startDate)
            ->whereNotNull('patients.classification')
            ->select(DB::raw('patients.classification, COUNT(DISTINCT consultations.patient_id) as count'))
            ->groupBy('patients.classification')
            ->pluck('count', 'classification');

        $severityData = Consultation::select(DB::raw('severity, COUNT(id) as count'))
            ->whereNotNull('severity')
            ->where('created_at', '>=', $startDate)
            ->groupBy('severity')
            ->pluck('count', 'severity');

        $barangayData = $this->analyticsService->getBarangayData($startDate);

        $demographics = DB::select("
            SELECT 
                p.sex,
                CASE
                    WHEN TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) BETWEEN 0 AND 12 THEN '0-12 (Pediatric)'
                    WHEN TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) BETWEEN 13 AND 17 THEN '13-17 (Teen)'
                    WHEN TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) BETWEEN 18 AND 39 THEN '18-39 (Adult)'
                    WHEN TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) BETWEEN 40 AND 59 THEN '40-59 (Middle Age)'
                    WHEN TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) >= 60 THEN '60+ (Senior)'
                    ELSE 'Unknown'
                END as age_group,
                COUNT(DISTINCT c.patient_id) as count
            FROM consultations c
            JOIN patients p ON c.patient_id = p.patient_id
            WHERE c.created_at >= ?
            AND p.sex IS NOT NULL
            AND p.dob IS NOT NULL
            GROUP BY p.sex, age_group
        ", [$startDate]);

        $demoData = [
            'labels' => ['0-12 (Pediatric)', '13-17 (Teen)', '18-39 (Adult)', '40-59 (Middle Age)', '60+ (Senior)'],
            'Male' => [0, 0, 0, 0, 0],
            'Female' => [0, 0, 0, 0, 0],
        ];

        foreach ($demographics as $d) {
            $idx = array_search($d->age_group, $demoData['labels']);
            if ($idx !== false && isset($demoData[$d->sex])) {
                $demoData[$d->sex][$idx] = $d->count;
            }
        }

        $workloadData = Consultation::select(DB::raw('
                COALESCE(doctor_id, nurse_id) as staff_id, 
                COUNT(id) as count
            '))
            ->where('created_at', '>=', $startDate)
            ->where(function ($q) {
                $q->whereNotNull('doctor_id')->orWhereNotNull('nurse_id');
            })
            ->groupBy('staff_id')
            ->get();

        $staffIds = $workloadData->pluck('staff_id')->filter();
        $staffNames = User::whereIn('id', $staffIds)->get()->pluck('formatted_name', 'id');

        $workloadFormatted = [];
        foreach ($workloadData as $row) {
            if ($row->staff_id && isset($staffNames[$row->staff_id])) {
                $workloadFormatted[$staffNames[$row->staff_id]] = $row->count;
            }
        }
        arsort($workloadFormatted);
        $workloadFormatted = array_slice($workloadFormatted, 0, 10);

        $staffList = User::whereIn('role', ['regular_doctor', 'pedia_doctor', 'nurse', 'clinical_nurse', 'vitals_nurse'])
            ->orderBy('name')
            ->get();
        $staffProductivity = $this->getStaffProductivityData('all', $startDate);

        return view('admin.analytics.index', compact(
            'timeFilter',
            'visitVolumeData',
            'peakHoursData',
            'topDiagnoses',
            'classificationData',
            'severityData',
            'barangayData',
            'demoData',
            'workloadFormatted',
            'staffList',
            'staffProductivity'
        ));
    }

    public function exportCsv(Request $request)
    {
        $timeFilter = $request->get('time_filter', 'monthly');

        $startDate = match ($timeFilter) {
            'today' => now()->startOfDay(),
            'weekly' => now()->startOfWeek(),
            'monthly' => now()->startOfMonth(),
            'yearly' => now()->startOfYear(),
            default => Carbon::create(2000, 1, 1),
        };

        $query = Consultation::query()
            ->where('consultations.created_at', '>=', $startDate)
            ->join('patients', 'consultations.patient_id', '=', 'patients.patient_id')
            ->leftJoin('users as doctors', 'consultations.doctor_id', '=', 'doctors.id')
            ->leftJoin('users as nurses', 'consultations.nurse_id', '=', 'nurses.id')
            ->leftJoin('pre_triages', 'consultations.pre_triage_id', '=', 'pre_triages.id')
            ->select([
                'consultations.id as consultation_id',
                'consultations.patient_id',
                'consultations.consultation_date',
                'consultations.created_at as consultation_created_at',
                'consultations.queue_number',
                'consultations.status',
                'consultations.severity',
                'consultations.diagnosis',
                'consultations.blood_pressure',
                'consultations.temperature',
                'consultations.weight',
                'consultations.height',
                'consultations.heart_rate',
                'consultations.respiratory_rate',
                'consultations.pulse_rate',
                'consultations.spo2',
                'consultations.consultation_start_time',
                'consultations.consultation_end_time',
                'consultations.is_followup_needed',
                'consultations.followup_date',
                'consultations.prescription',
                'consultations.medical_notes',
                'patients.first_name as patient_first_name',
                'patients.last_name as patient_last_name',
                'patients.sex as patient_sex',
                'patients.dob as patient_dob',
                'patients.classification as patient_classification',
                'patients.barangay as patient_barangay',
                'patients.address as patient_address',
                'doctors.name as doctor_name',
                'nurses.name as nurse_name',
                'pre_triages.chief_complaint',
                'pre_triages.encoding_duration_seconds as triage_encoding_seconds',
            ])
            ->orderBy('consultations.created_at', 'desc');

        $count = $query->count();

        if ($count === 0) {
            return response()->json(['message' => 'No analytics data found for the selected timeframe.'], 404);
        }

        $timeFilterLabel = match ($timeFilter) {
            'today' => now()->format('Y-m-d'),
            'weekly' => now()->startOfWeek()->format('Y-m-d').'_to_'.now()->endOfWeek()->format('Y-m-d'),
            'monthly' => now()->format('Y-m'),
            'yearly' => now()->format('Y'),
            default => 'all-time',
        };

        $filename = "detailed-analytics-{$timeFilterLabel}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'no-store, no-cache',
        ];

        $csvHeaders = [
            'Consultation ID',
            'Patient ID',
            'Patient Name',
            'Sex',
            'Date of Birth',
            'Age',
            'Classification',
            'Barangay',
            'Consultation Date',
            'Created At',
            'Queue Number',
            'Status',
            'Severity',
            'Chief Complaint',
            'Diagnosis',
            'Prescription',
            'Clinical Notes / Addenda',
            'Doctor',
            'Nurse',
            'Consultation Start',
            'Consultation End',
            'Duration (min)',
            'Wait Time (min)',
            'Blood Pressure',
            'Temperature',
            'Weight (kg)',
            'Height (cm)',
            'Heart Rate',
            'Respiratory Rate',
            'Pulse Rate',
            'SpO2',
            'Follow-up Needed',
            'Follow-up Date',
            'Triage Encoding (sec)',
        ];

        $callback = function () use ($query, $csvHeaders) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, $csvHeaders);

            $query->chunk(500, function ($rows) use ($handle) {
                foreach ($rows as $row) {
                    $age = '';
                    if ($row->patient_dob) {
                        try {
                            $age = Carbon::parse($row->patient_dob)->age;
                        } catch (\Exception $e) {
                            $age = '';
                        }
                    }

                    $durationMin = '';
                    if ($row->consultation_start_time && $row->consultation_end_time) {
                        try {
                            $durationMin = round(
                                Carbon::parse($row->consultation_start_time)
                                    ->diffInMinutes(Carbon::parse($row->consultation_end_time)),
                                1
                            );
                        } catch (\Exception $e) {
                            $durationMin = '';
                        }
                    }

                    $waitTimeMin = '';
                    if ($row->consultation_created_at && $row->consultation_start_time) {
                        try {
                            $waitTimeMin = round(
                                Carbon::parse($row->consultation_created_at)
                                    ->diffInMinutes(Carbon::parse($row->consultation_start_time)),
                                1
                            );
                        } catch (\Exception $e) {
                            $waitTimeMin = '';
                        }
                    }

                    $barangay = Patient::normalizeBarangay($row->patient_barangay, $row->patient_address);
                    $patientName = trim(($row->patient_first_name ?? '').' '.($row->patient_last_name ?? ''));

                    fputcsv($handle, [
                        $row->consultation_id,
                        $row->patient_id,
                        $patientName,
                        $row->patient_sex ?? '',
                        $row->patient_dob ? Carbon::parse($row->patient_dob)->format('Y-m-d') : '',
                        $age,
                        $row->patient_classification ?? '',
                        $barangay ?? '',
                        $row->consultation_date ? Carbon::parse($row->consultation_date)->format('Y-m-d') : '',
                        $row->consultation_created_at ? Carbon::parse($row->consultation_created_at)->format('Y-m-d H:i:s') : '',
                        $row->queue_number ?? '',
                        ucfirst($row->status ?? ''),
                        ucfirst($row->severity ?? ''),
                        $row->chief_complaint ?? '',
                        $row->diagnosis ?? '',
                        $row->prescription ?? '',
                        $row->medical_notes ?? '',
                        $row->doctor_name ?? '',
                        $row->nurse_name ?? '',
                        $row->consultation_start_time ? Carbon::parse($row->consultation_start_time)->format('Y-m-d H:i:s') : '',
                        $row->consultation_end_time ? Carbon::parse($row->consultation_end_time)->format('Y-m-d H:i:s') : '',
                        $durationMin,
                        $waitTimeMin,
                        $row->blood_pressure ?? '',
                        $row->temperature ?? '',
                        $row->weight ?? '',
                        $row->height ?? '',
                        $row->heart_rate ?? '',
                        $row->respiratory_rate ?? '',
                        $row->pulse_rate ?? '',
                        $row->spo2 ?? '',
                        $row->is_followup_needed ? 'Yes' : 'No',
                        $row->followup_date ? Carbon::parse($row->followup_date)->format('Y-m-d') : '',
                        $row->triage_encoding_seconds ?? '',
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportSummaryCsv(Request $request)
    {
        $timeFilter = $request->get('time_filter', 'monthly');

        $startDate = match ($timeFilter) {
            'today' => now()->startOfDay(),
            'weekly' => now()->startOfWeek(),
            'monthly' => now()->startOfMonth(),
            'yearly' => now()->startOfYear(),
            default => Carbon::create(2000, 1, 1),
        };

        $timeFilterLabel = match ($timeFilter) {
            'today' => now()->format('Y-m-d'),
            'weekly' => now()->startOfWeek()->format('Y-m-d').'_to_'.now()->endOfWeek()->format('Y-m-d'),
            'monthly' => now()->format('Y-m'),
            'yearly' => now()->format('Y'),
            default => 'all-time',
        };

        $humanTimeframe = match ($timeFilter) {
            'today' => 'Today ('.now()->format('F d, Y').')',
            'weekly' => 'This Week ('.now()->startOfWeek()->format('M d').' - '.now()->endOfWeek()->format('M d, Y').')',
            'monthly' => 'This Month ('.now()->format('F Y').')',
            'yearly' => 'This Year ('.now()->format('Y').')',
            default => 'All Historical Records',
        };

        $visitVolumeData = $this->analyticsService->getVisitVolumeData($timeFilter);
        $peakHoursData = $this->analyticsService->getPeakHoursData($startDate);

        $topDiagnoses = Consultation::whereNotNull('diagnosis')
            ->where('diagnosis', '!=', '')
            ->where('created_at', '>=', $startDate)
            ->select(DB::raw('diagnosis, COUNT(id) as count'))
            ->groupBy('diagnosis')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        $classificationData = Patient::join('consultations', 'patients.patient_id', '=', 'consultations.patient_id')
            ->where('consultations.created_at', '>=', $startDate)
            ->whereNotNull('patients.classification')
            ->select('patients.classification', DB::raw('COUNT(consultations.id) as count'))
            ->groupBy('patients.classification')
            ->pluck('count', 'classification');

        $severityData = Consultation::select(DB::raw('severity, COUNT(id) as count'))
            ->whereNotNull('severity')
            ->where('created_at', '>=', $startDate)
            ->groupBy('severity')
            ->pluck('count', 'severity');

        $barangayData = $this->analyticsService->getBarangayData($startDate);

        $demographics = DB::select("
            SELECT 
                p.sex,
                CASE
                    WHEN TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) BETWEEN 0 AND 12 THEN '0-12 (Pediatric)'
                    WHEN TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) BETWEEN 13 AND 17 THEN '13-17 (Teen)'
                    WHEN TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) BETWEEN 18 AND 39 THEN '18-39 (Adult)'
                    WHEN TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) BETWEEN 40 AND 59 THEN '40-59 (Middle Age)'
                    WHEN TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) >= 60 THEN '60+ (Senior)'
                    ELSE 'Unknown'
                END as age_group,
                COUNT(DISTINCT c.patient_id) as count
            FROM consultations c
            JOIN patients p ON c.patient_id = p.patient_id
            WHERE c.created_at >= ?
            AND p.sex IS NOT NULL
            AND p.dob IS NOT NULL
            GROUP BY p.sex, age_group
        ", [$startDate]);

        $demoLabels = ['0-12 (Pediatric)', '13-17 (Teen)', '18-39 (Adult)', '40-59 (Middle Age)', '60+ (Senior)'];
        $demoData = [
            'Male' => [0, 0, 0, 0, 0],
            'Female' => [0, 0, 0, 0, 0],
        ];
        foreach ($demographics as $d) {
            $idx = array_search($d->age_group, $demoLabels);
            if ($idx !== false && isset($demoData[$d->sex])) {
                $demoData[$d->sex][$idx] = $d->count;
            }
        }

        $workloadData = Consultation::select(DB::raw('
                COALESCE(doctor_id, nurse_id) as staff_id, 
                COUNT(id) as count
            '))
            ->where('created_at', '>=', $startDate)
            ->where(function ($q) {
                $q->whereNotNull('doctor_id')->orWhereNotNull('nurse_id');
            })
            ->groupBy('staff_id')
            ->get();

        $staffIds = $workloadData->pluck('staff_id')->filter();
        $staffNames = User::whereIn('id', $staffIds)->get()->pluck('formatted_name', 'id');

        $workloadFormatted = [];
        foreach ($workloadData as $row) {
            if ($row->staff_id && isset($staffNames[$row->staff_id])) {
                $workloadFormatted[$staffNames[$row->staff_id]] = $row->count;
            }
        }
        arsort($workloadFormatted);

        $staffProductivity = $this->getStaffProductivityData('all', $startDate);
        $totalConsultations = Consultation::where('created_at', '>=', $startDate)->count();

        $filename = "analytics-summary-graphs-{$timeFilterLabel}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'no-store, no-cache',
        ];

        return response()->stream(function () use (
            $humanTimeframe,
            $totalConsultations,
            $visitVolumeData,
            $peakHoursData,
            $topDiagnoses,
            $classificationData,
            $severityData,
            $barangayData,
            $demoLabels,
            $demoData,
            $workloadFormatted,
            $staffProductivity
        ) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['RHU MANAGEMENT INFORMATION SYSTEM - ANALYTICS SUMMARY REPORT']);
            fputcsv($handle, ['Report Type', 'Aggregated Dashboard Graphs & Epidemiological Indicators']);
            fputcsv($handle, ['Reporting Timeframe', $humanTimeframe]);
            fputcsv($handle, ['Generated At', now()->format('Y-m-d H:i:s')]);
            fputcsv($handle, ['Generated By', auth()->user() ? auth()->user()->name : 'System Administrator']);
            fputcsv($handle, ['Total Consultations Recorded', $totalConsultations]);
            fputcsv($handle, ['Average Consultation Duration', ($staffProductivity['avgDuration'] ?? 0).' minutes']);
            fputcsv($handle, ['Average Queue Wait Time', ($staffProductivity['avgWaitTime'] ?? 0).' minutes']);
            fputcsv($handle, ['Throughput (Patients / Hour)', ($staffProductivity['patientsPerHour'] ?? 0).' pts/hr']);
            fputcsv($handle, []);

            // SECTION 1: VISIT VOLUME
            fputcsv($handle, ['=== 1. VISIT VOLUME OVER TIME ===']);
            fputcsv($handle, ['Period / Date', 'Consultation Count', 'Share of Total']);
            $volLabels = $visitVolumeData['labels'] ?? [];
            $volCounts = $visitVolumeData['data'] ?? [];
            $volSum = array_sum($volCounts);
            foreach ($volLabels as $i => $lbl) {
                $c = $volCounts[$i] ?? 0;
                $pct = $volSum > 0 ? round(($c / $volSum) * 100, 1).'%' : '0%';
                fputcsv($handle, [$lbl, $c, $pct]);
            }
            fputcsv($handle, ['Total Period Volume', $volSum, '100%']);
            fputcsv($handle, []);

            // SECTION 2: PEAK OPERATING HOURS
            fputcsv($handle, ['=== 2. HOURLY PATIENT INFLUX & PEAK TRAFFIC ===']);
            fputcsv($handle, ['Operating Time Slot', 'Patient Check-ins', 'Share of Daily Flow', 'Traffic Density']);
            $peakLabels = $peakHoursData['labels'] ?? [];
            $peakCounts = $peakHoursData['data'] ?? [];
            $peakTotal = array_sum($peakCounts);
            $maxPeak = count($peakCounts) > 0 ? max($peakCounts) : 0;
            foreach ($peakLabels as $i => $lbl) {
                $c = $peakCounts[$i] ?? 0;
                $pct = $peakTotal > 0 ? round(($c / $peakTotal) * 100, 1).'%' : '0%';
                $density = 'Normal';
                if ($c > 0 && $c == $maxPeak) {
                    $density = 'Peak Traffic Window';
                } elseif ($c > ($maxPeak * 0.7)) {
                    $density = 'Heavy Traffic';
                } elseif ($c < ($maxPeak * 0.3)) {
                    $density = 'Light Traffic';
                }
                fputcsv($handle, [$lbl, $c, $pct, $density]);
            }
            fputcsv($handle, ['Total Clinic Check-ins', $peakTotal, '100%', '']);
            fputcsv($handle, []);

            // SECTION 3: TOP DIAGNOSES
            fputcsv($handle, ['=== 3. TOP CLINICAL DIAGNOSES (MORBIDITY RANKING) ===']);
            fputcsv($handle, ['Rank', 'Diagnosis / Morbidity', 'Diagnosed Cases', 'Prevalence Share']);
            $diagSum = $topDiagnoses->sum('count');
            if ($topDiagnoses->isEmpty()) {
                fputcsv($handle, ['—', 'No clinical diagnoses recorded for this period', 0, '0%']);
            } else {
                foreach ($topDiagnoses as $i => $row) {
                    $c = $row->count ?? 0;
                    $pct = $diagSum > 0 ? round(($c / $diagSum) * 100, 1).'%' : '0%';
                    fputcsv($handle, [$i + 1, $row->diagnosis, $c, $pct]);
                }
            }
            fputcsv($handle, ['Total Ranked Cases', $diagSum, '100%']);
            fputcsv($handle, []);

            // SECTION 4: PATIENT CLASSIFICATION
            fputcsv($handle, ['=== 4. PATIENT SOCIOECONOMIC CLASSIFICATION ===']);
            fputcsv($handle, ['Classification Group', 'Patient Count', 'Distribution Share']);
            $classSum = $classificationData->sum();
            if ($classificationData->isEmpty()) {
                fputcsv($handle, ['No classification data recorded', 0, '0%']);
            } else {
                foreach ($classificationData as $grp => $c) {
                    $pct = $classSum > 0 ? round(($c / $classSum) * 100, 1).'%' : '0%';
                    fputcsv($handle, [ucwords($grp), $c, $pct]);
                }
            }
            fputcsv($handle, ['Total Patients Classified', $classSum, '100%']);
            fputcsv($handle, []);

            // SECTION 5: TRIAGE SEVERITY
            fputcsv($handle, ['=== 5. TRIAGE SEVERITY & URGENCY DISTRIBUTION ===']);
            fputcsv($handle, ['Urgency Level', 'Triage Encounters', 'Urgency Share']);
            $sevSum = $severityData->sum();
            if ($severityData->isEmpty()) {
                fputcsv($handle, ['No triage records recorded', 0, '0%']);
            } else {
                foreach ($severityData as $sev => $c) {
                    $pct = $sevSum > 0 ? round(($c / $sevSum) * 100, 1).'%' : '0%';
                    fputcsv($handle, [ucfirst($sev), $c, $pct]);
                }
            }
            fputcsv($handle, ['Total Triaged Encounters', $sevSum, '100%']);
            fputcsv($handle, []);

            // SECTION 6: BARANGAY GEOGRAPHIC DISTRIBUTION
            fputcsv($handle, ['=== 6. BARANGAY CATCHMENT & GEOGRAPHIC INFLUX ===']);
            fputcsv($handle, ['Rank', 'Barangay Name', 'Consultation Volume', 'Catchment Share']);
            $brgySum = array_sum($barangayData);
            if (empty($barangayData)) {
                fputcsv($handle, ['—', 'No geographic data available', 0, '0%']);
            } else {
                $rank = 1;
                foreach ($barangayData as $brgy => $c) {
                    $pct = $brgySum > 0 ? round(($c / $brgySum) * 100, 1).'%' : '0%';
                    fputcsv($handle, [$rank++, $brgy, $c, $pct]);
                }
            }
            fputcsv($handle, ['Total Geocoded Volume', $brgySum, '100%']);
            fputcsv($handle, []);

            // SECTION 7: DEMOGRAPHICS (AGE & SEX)
            fputcsv($handle, ['=== 7. AGE & BIOLOGICAL SEX DEMOGRAPHIC DISTRIBUTION ===']);
            fputcsv($handle, ['Age Bracket', 'Male Patients', 'Female Patients', 'Total Patients', 'Demographic Share']);
            $totalDemoAll = 0;
            foreach ($demoLabels as $idx => $lbl) {
                $m = $demoData['Male'][$idx] ?? 0;
                $f = $demoData['Female'][$idx] ?? 0;
                $totalDemoAll += ($m + $f);
            }
            foreach ($demoLabels as $idx => $lbl) {
                $m = $demoData['Male'][$idx] ?? 0;
                $f = $demoData['Female'][$idx] ?? 0;
                $tot = $m + $f;
                $pct = $totalDemoAll > 0 ? round(($tot / $totalDemoAll) * 100, 1).'%' : '0%';
                fputcsv($handle, [$lbl, $m, $f, $tot, $pct]);
            }
            fputcsv($handle, ['Total Demographic Count', array_sum($demoData['Male']), array_sum($demoData['Female']), $totalDemoAll, '100%']);
            fputcsv($handle, []);

            // SECTION 8: CLINICAL WORKLOAD
            fputcsv($handle, ['=== 8. CLINICAL STAFF WORKLOAD (PATIENTS HANDLED) ===']);
            fputcsv($handle, ['Practitioner Name', 'Consultations Completed', 'Workload Share']);
            $workloadSum = array_sum($workloadFormatted);
            if (empty($workloadFormatted)) {
                fputcsv($handle, ['No practitioner consultations logged', 0, '0%']);
            } else {
                foreach ($workloadFormatted as $staff => $c) {
                    $pct = $workloadSum > 0 ? round(($c / $workloadSum) * 100, 1).'%' : '0%';
                    fputcsv($handle, [$staff, $c, $pct]);
                }
            }
            fputcsv($handle, ['Total Staff Encounters', $workloadSum, '100%']);
            fputcsv($handle, []);

            // SECTION 9: DOCTOR CONSULTATION DURATION
            fputcsv($handle, ['=== 9. AVERAGE CONSULTATION DURATION PER DOCTOR ===']);
            fputcsv($handle, ['Physician Name', 'Average Duration (Minutes)', 'Efficiency Standard']);
            $durLabels = $staffProductivity['durationChartLabels'] ?? [];
            $durData = $staffProductivity['durationChartData'] ?? [];
            if (empty($durLabels)) {
                fputcsv($handle, ['No physician consultation timings logged', 0, 'N/A']);
            } else {
                foreach ($durLabels as $i => $doc) {
                    $mins = $durData[$i] ?? 0;
                    $status = $mins <= 15 ? 'Optimal (<= 15 min)' : ($mins <= 30 ? 'Standard' : 'Extended (> 30 min)');
                    fputcsv($handle, [$doc, $mins.' min', $status]);
                }
            }
            fputcsv($handle, []);

            // SECTION 10: TRIAGE ENCODING SPEED
            fputcsv($handle, ['=== 10. TRIAGE NURSE VITALS ENCODING SPEED ===']);
            fputcsv($handle, ['Triage Nurse', 'Avg Encoding Time (Seconds)', 'Avg Encoding Time (Minutes)', 'Target Benchmark']);
            $encLabels = $staffProductivity['encodingChartLabels'] ?? [];
            $encData = $staffProductivity['encodingChartData'] ?? [];
            if (empty($encLabels)) {
                fputcsv($handle, ['No triage encoding timing records logged', 0, 0, 'N/A']);
            } else {
                foreach ($encLabels as $i => $nurse) {
                    $secs = $encData[$i] ?? 0;
                    $mins = round($secs / 60, 1);
                    $target = $secs <= 180 ? 'Within Target (<= 3 min)' : 'Needs Improvement (> 3 min)';
                    fputcsv($handle, [$nurse, $secs.'s', $mins.' min', $target]);
                }
            }

            fclose($handle);
        }, 200, $headers);
    }

    public function apiChartData(Request $request, $chart)
    {
        $timeFilter = $request->get('time_filter', 'monthly');

        $startDate = match ($timeFilter) {
            'today' => now()->startOfDay(),
            'weekly' => now()->startOfWeek(),
            'monthly' => now()->startOfMonth(),
            'yearly' => now()->startOfYear(),
            default => Carbon::create(2000, 1, 1),
        };

        if ($chart === 'volume') {
            return response()->json($this->analyticsService->getVisitVolumeData($timeFilter));
        }

        if ($chart === 'peak') {
            return response()->json($this->analyticsService->getPeakHoursData($startDate));
        }

        if ($chart === 'classification') {
            $data = Consultation::join('patients', 'consultations.patient_id', '=', 'patients.patient_id')
                ->where('consultations.created_at', '>=', $startDate)
                ->whereNotNull('patients.classification')
                ->select(DB::raw('patients.classification, COUNT(DISTINCT consultations.patient_id) as count'))
                ->groupBy('patients.classification')
                ->pluck('count', 'classification');

            return response()->json(['labels' => array_keys($data->toArray()), 'data' => array_values($data->toArray())]);
        }

        if ($chart === 'severity') {
            $data = Consultation::select(DB::raw('severity, COUNT(id) as count'))
                ->whereNotNull('severity')
                ->where('created_at', '>=', $startDate)
                ->groupBy('severity')
                ->pluck('count', 'severity');

            $labels = array_map(function ($l) {
                return ucfirst($l);
            }, array_keys($data->toArray()));

            return response()->json(['labels' => $labels, 'data' => array_values($data->toArray())]);
        }

        if ($chart === 'barangay') {
            $data = $this->analyticsService->getBarangayData($startDate);

            return response()->json(['labels' => array_keys($data), 'data' => array_values($data)]);
        }

        if ($chart === 'demographics') {
            $demographics = DB::select("
                SELECT 
                    p.sex,
                    CASE
                        WHEN TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) BETWEEN 0 AND 12 THEN '0-12 (Pediatric)'
                        WHEN TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) BETWEEN 13 AND 17 THEN '13-17 (Teen)'
                        WHEN TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) BETWEEN 18 AND 39 THEN '18-39 (Adult)'
                        WHEN TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) BETWEEN 40 AND 59 THEN '40-59 (Middle Age)'
                        WHEN TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) >= 60 THEN '60+ (Senior)'
                        ELSE 'Unknown'
                    END as age_group,
                    COUNT(DISTINCT c.patient_id) as count
                FROM consultations c
                JOIN patients p ON c.patient_id = p.patient_id
                WHERE c.created_at >= ?
                AND p.sex IS NOT NULL
                AND p.dob IS NOT NULL
                GROUP BY p.sex, age_group
            ", [$startDate]);

            $demoData = [
                'labels' => ['0-12 (Pediatric)', '13-17 (Teen)', '18-39 (Adult)', '40-59 (Middle Age)', '60+ (Senior)'],
                'Male' => [0, 0, 0, 0, 0],
                'Female' => [0, 0, 0, 0, 0],
            ];

            foreach ($demographics as $d) {
                $idx = array_search($d->age_group, $demoData['labels']);
                if ($idx !== false && isset($demoData[$d->sex])) {
                    $demoData[$d->sex][$idx] = $d->count;
                }
            }

            return response()->json($demoData);
        }

        if ($chart === 'workload') {
            $workloadData = Consultation::select(DB::raw('
                    COALESCE(doctor_id, nurse_id) as staff_id, 
                    COUNT(id) as count
                '))
                ->where('created_at', '>=', $startDate)
                ->where(function ($q) {
                    $q->whereNotNull('doctor_id')->orWhereNotNull('nurse_id');
                })
                ->groupBy('staff_id')
                ->get();

            $staffIds = $workloadData->pluck('staff_id')->filter();
            $staffNames = User::whereIn('id', $staffIds)->get()->pluck('formatted_name', 'id');

            $workloadFormatted = [];
            foreach ($workloadData as $row) {
                if ($row->staff_id && isset($staffNames[$row->staff_id])) {
                    $workloadFormatted[$staffNames[$row->staff_id]] = $row->count;
                }
            }
            arsort($workloadFormatted);
            $workloadFormatted = array_slice($workloadFormatted, 0, 10);

            return response()->json(['labels' => array_keys($workloadFormatted), 'data' => array_values($workloadFormatted)]);
        }

        return response()->json(['error' => 'Invalid chart'], 400);
    }

    public function apiStaffProductivityData(Request $request)
    {
        $timeFilter = $request->get('time_filter', 'monthly');
        $staffId = $request->get('staff_id', 'all');

        $startDate = match ($timeFilter) {
            'today' => now()->startOfDay(),
            'weekly' => now()->startOfWeek(),
            'monthly' => now()->startOfMonth(),
            default => Carbon::create(2000, 1, 1),
        };

        return response()->json($this->getStaffProductivityData($staffId, $startDate));
    }

    public function getStaffProductivityData($staffId, $startDate): array
    {
        $query = Consultation::where('created_at', '>=', $startDate);
        $preTriageQuery = PreTriage::where('created_at', '>=', $startDate)
            ->whereNotNull('encoding_duration_seconds');

        if ($staffId !== 'all') {
            $query->where(function ($q) use ($staffId) {
                $q->where('doctor_id', $staffId)->orWhere('nurse_id', $staffId);
            });
            $preTriageQuery->where('recorded_by', $staffId);
        }

        // 1. Avg Consultation Duration (minutes)
        $avgDuration = $query->clone()
            ->whereNotNull('consultation_start_time')
            ->whereNotNull('consultation_end_time')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, consultation_start_time, consultation_end_time)) as avg_duration'))
            ->value('avg_duration') ?? 0;

        // 2. Total Patients Handled (completed/done)
        $totalPatients = $query->clone()
            ->whereIn('status', ['completed', 'done'])
            ->count();

        // 3. Patients Per Hour Throughput
        $totalDurationMinutes = $query->clone()
            ->whereNotNull('consultation_start_time')
            ->whereNotNull('consultation_end_time')
            ->select(DB::raw('SUM(TIMESTAMPDIFF(MINUTE, consultation_start_time, consultation_end_time)) as total_duration'))
            ->value('total_duration') ?? 0;

        $patientsPerHour = $totalDurationMinutes > 0 ? round($totalPatients / ($totalDurationMinutes / 60), 1) : 0;

        // 4. Avg Queue Wait Time (minutes)
        $avgWaitTime = $query->clone()
            ->whereNotNull('consultation_start_time')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, created_at, consultation_start_time)) as avg_wait_time'))
            ->value('avg_wait_time') ?? 0;

        // 5. Cancellation Rate
        $totalForCancellation = $query->clone()->whereIn('status', ['completed', 'done', 'cancelled'])->count();
        $cancelledCount = $query->clone()->where('status', 'cancelled')->count();
        $cancellationRate = $totalForCancellation > 0 ? round(($cancelledCount / $totalForCancellation) * 100, 1) : 0;

        // Chart 1: Avg Consultation Duration per Doctor
        $durationPerStaffData = Consultation::where('created_at', '>=', $startDate)
            ->whereNotNull('consultation_start_time')
            ->whereNotNull('consultation_end_time')
            ->whereNotNull('doctor_id')
            ->select(DB::raw('
                doctor_id as staff_id, 
                AVG(TIMESTAMPDIFF(MINUTE, consultation_start_time, consultation_end_time)) as avg_duration
            '));

        if ($staffId !== 'all') {
            $durationPerStaffData->where('doctor_id', $staffId);
        }

        $durationPerStaffData = $durationPerStaffData->groupBy('staff_id')->get();

        $staffIdsForChart1 = $durationPerStaffData->pluck('staff_id')->filter();
        $staffNamesForChart1 = User::whereIn('id', $staffIdsForChart1)->get()->pluck('formatted_name', 'id');

        $durationChart = [];
        foreach ($durationPerStaffData as $row) {
            if ($row->staff_id && isset($staffNamesForChart1[$row->staff_id])) {
                $durationChart[$staffNamesForChart1[$row->staff_id]] = round($row->avg_duration, 1);
            }
        }
        arsort($durationChart);
        $durationChart = array_slice($durationChart, 0, 10);

        // Chart 2: Triage Encoding Speed per Nurse
        $encodingSpeedData = $preTriageQuery->clone()
            ->select(DB::raw('
                recorded_by as staff_id, 
                AVG(encoding_duration_seconds) as avg_speed
            '))
            ->groupBy('staff_id')
            ->get();

        $nurseIds = $encodingSpeedData->pluck('staff_id')->filter();
        $nurseNames = User::whereIn('id', $nurseIds)->get()->pluck('formatted_name', 'id');

        $encodingChart = [];
        foreach ($encodingSpeedData as $row) {
            if ($row->staff_id && isset($nurseNames[$row->staff_id])) {
                $encodingChart[$nurseNames[$row->staff_id]] = round($row->avg_speed);
            }
        }
        asort($encodingChart);
        $encodingChart = array_slice($encodingChart, 0, 10);

        return [
            'avgDuration' => round($avgDuration),
            'totalPatients' => $totalPatients,
            'patientsPerHour' => $patientsPerHour,
            'avgWaitTime' => round($avgWaitTime),
            'cancellationRate' => $cancellationRate,
            'durationChartLabels' => array_keys($durationChart),
            'durationChartData' => array_values($durationChart),
            'encodingChartLabels' => array_keys($encodingChart),
            'encodingChartData' => array_values($encodingChart),
        ];
    }
}
