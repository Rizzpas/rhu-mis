<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\InventoryLog;
use App\Models\MedicineBatch;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ExpireBatches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'batches:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-transition active medicine batches past their expiration date to expired status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();

        // Find active batches that have expired (expiration_date is before today)
        $expiredBatches = MedicineBatch::where('status', 'active')
            ->whereDate('expiration_date', '<', $today)
            ->with('medicine')
            ->get();

        $expiredCount = 0;

        foreach ($expiredBatches as $batch) {
            $previousQty = $batch->quantity;
            $medicineName = $batch->medicine?->name ?? 'Unknown Medicine';

            $batch->update([
                'status' => 'expired',
            ]);

            // Create an inventory log entry for the auto-expiry
            if ($previousQty > 0) {
                InventoryLog::create([
                    'medicine_id' => $batch->medicine_id,
                    'batch_id' => $batch->id,
                    'action' => 'Expired',
                    'quantity_changed' => -$previousQty,
                    'remarks' => "Auto-expired: Batch #{$batch->batch_number} ({$medicineName}) — {$previousQty} unit(s) removed from active inventory. Expiration date: {$batch->expiration_date->format('M d, Y')}.",
                    'performed_by' => null, // System action
                ]);
            }

            $expiredCount++;
            $this->line("  [EXPIRED] Batch #{$batch->batch_number} | {$medicineName} | Qty: {$previousQty} | Exp: {$batch->expiration_date->format('Y-m-d')}");
        }

        if ($expiredCount > 0) {
            AuditLog::record("Automated EOD: Expired {$expiredCount} medicine batch(es) past expiration date");
            $this->info("Successfully expired {$expiredCount} medicine batch(es).");
        } else {
            $this->info('No expired batches to transition.');
        }

        return Command::SUCCESS;
    }
}
