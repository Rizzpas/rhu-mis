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
        
        // Find all records from today that are still waiting or claimed
        $count = \App\Models\PreTriage::whereDate('created_at', $today)
            ->whereIn('status', ['waiting', 'claimed'])
            ->delete();

        if ($count > 0) {
            $this->info("Quietly removed $count unfulfilled vitals entries from today's queue.");
            
            // Record a system audit log
            \App\Models\AuditLog::create([
                'action' => 'End-of-Day Vitals Cleanup',
                'model_type' => \App\Models\PreTriage::class,
                'changes' => ['deleted_count' => $count],
                'ip_address' => '127.0.0.1',
                'user_agent' => 'System Scheduler'
            ]);
        } else {
            $this->info("No unfulfilled vitals entries to clean up today.");
        }
    }
}
