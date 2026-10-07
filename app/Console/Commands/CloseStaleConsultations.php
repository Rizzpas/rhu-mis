<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\Consultation;
use App\Models\Queue;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CloseStaleConsultations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consultations:close-stale';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-cancel stale consultations and queue entries from previous days that were never completed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        $cancelledConsultations = 0;
        $cancelledQueues = 0;

        // ── 1. Cancel consultations from previous days stuck in 'queued' ──────
        $staleQueued = Consultation::where('status', 'queued')
            ->whereDate('consultation_date', '<', $today)
            ->get();

        foreach ($staleQueued as $consultation) {
            $consultation->update([
                'status' => 'cancelled',
                'medical_notes' => trim(($consultation->medical_notes ?? '') . "\n[System] Auto-cancelled: Patient did not arrive for queued consultation."),
            ]);

            $cancelledConsultations++;
            $this->line("  [CANCELLED] Consultation #{$consultation->id} | Patient: {$consultation->patient_id} | Date: {$consultation->consultation_date?->format('Y-m-d')} | Was: queued");
        }

        // ── 2. Flag consultations stuck in 'active' for >12 hours ────────────
        // We don't auto-cancel active consultations (could be overnight emergencies),
        // but we log them for review.
        $staleActive = Consultation::where('status', 'active')
            ->where(function ($q) use ($today) {
                $q->whereDate('consultation_date', '<', $today)
                  ->orWhere(function ($sub) {
                      $sub->whereNotNull('consultation_start_time')
                          ->where('consultation_start_time', '<', now()->subHours(12));
                  });
            })
            ->get();

        if ($staleActive->count() > 0) {
            $this->warn("  ⚠ {$staleActive->count()} consultation(s) have been active for >12 hours and may need manual review:");
            foreach ($staleActive as $c) {
                $this->warn("    Consultation #{$c->id} | Patient: {$c->patient_id} | Started: {$c->consultation_start_time}");
            }
        }

        // ── 3. Cancel consultations stuck in 'awaiting_results' from previous days ──
        $staleAwaiting = Consultation::where('status', 'awaiting_results')
            ->whereDate('consultation_date', '<', $today->copy()->subDays(1))
            ->get();

        foreach ($staleAwaiting as $consultation) {
            $consultation->update([
                'status' => 'cancelled',
                'medical_notes' => trim(($consultation->medical_notes ?? '') . "\n[System] Auto-cancelled: Awaiting results for >1 day with no resolution."),
            ]);

            // Also cancel any pending ancillary requests for this consultation
            $consultation->ancillaryRequests()
                ->whereIn('status', ['Pending', 'Specimen Collected', 'In Progress'])
                ->update(['status' => 'Cancelled']);

            $cancelledConsultations++;
            $this->line("  [CANCELLED] Consultation #{$consultation->id} | Patient: {$consultation->patient_id} | Was: awaiting_results");
        }

        // ── 4. Cancel stale queue entries from previous days ──────────────────
        $staleQueues = Queue::whereIn('status', ['Waiting', 'Calling'])
            ->whereDate('created_at', '<', $today)
            ->get();

        foreach ($staleQueues as $queue) {
            $queue->update(['status' => 'Cancelled']);
            $cancelledQueues++;
        }

        if ($cancelledQueues > 0) {
            $this->line("  [CANCELLED] {$cancelledQueues} stale queue entries from previous days.");
        }

        // ── 5. Audit log ─────────────────────────────────────────────────────
        $totalActions = $cancelledConsultations + $cancelledQueues;

        if ($totalActions > 0) {
            AuditLog::record("Automated EOD: Closed {$cancelledConsultations} stale consultation(s) and {$cancelledQueues} queue entries");
            $this->info("Cleanup complete: {$cancelledConsultations} consultation(s), {$cancelledQueues} queue(s) cancelled.");
        } else {
            $this->info('No stale consultations or queues to clean up.');
        }

        if ($staleActive->count() > 0) {
            $this->warn("⚠ {$staleActive->count()} active consultation(s) require manual review (not auto-cancelled).");
        }

        return Command::SUCCESS;
    }
}
