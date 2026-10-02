<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanupVitals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cleanup-vitals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = \Carbon\Carbon::today();

        // 1. Archive stale pre-triage records from PAST days that were left abandoned in 'waiting'
        $pastCount = \App\Models\PreTriage::whereDate('created_at', '<', $today)
            ->where('status', 'waiting')
            ->update(['status' => 'cancelled']);

        if ($pastCount > 0) {
            $this->info("Archived $pastCount abandoned waiting vitals entries as cancelled from previous days.");
            \App\Models\AuditLog::create([
                'action' => 'EOD: Abandoned Vitals Archival (Past Days)',
                'model_type' => \App\Models\PreTriage::class,
                'changes' => ['cancelled_count' => $pastCount],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'System Scheduler',
            ]);
        }

        // 2. Archive today's unfulfilled 'waiting' entries at end of day
        $todayCount = \App\Models\PreTriage::whereDate('created_at', $today)
            ->where('status', 'waiting')
            ->update(['status' => 'cancelled']);

        if ($todayCount > 0) {
            $this->info("Archived $todayCount unfulfilled waiting vitals entries as cancelled from today.");
            \App\Models\AuditLog::create([
                'action' => 'End-of-Day Vitals Archival',
                'model_type' => \App\Models\PreTriage::class,
                'changes' => ['cancelled_count' => $todayCount],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'System Scheduler',
            ]);
        }

        $totalCleaned = $pastCount + $todayCount;
        if ($totalCleaned === 0) {
            $this->info('No unfulfilled vitals entries to clean up.');
        }
    }
}
