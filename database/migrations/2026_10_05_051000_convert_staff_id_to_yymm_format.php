<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Convert existing Staff IDs from {ROLE}-{YY}-{XXXX} to {ROLE}-{YYMM}-{XXXX},
     * keeping each staff member's random suffix so IDs stay recognisable.
     */
    public function up(): void
    {
        DB::table('users')->whereNotNull('staff_id')->get(['id', 'staff_id', 'created_at'])
            ->each(function ($row) {
                if (! preg_match('/^([A-Z]{3})-(\d{2})-([A-Z0-9]{4})$/', $row->staff_id, $m)) {
                    return; // already YYMM or custom
                }
                $yymm = $row->created_at ? date('ym', strtotime($row->created_at)) : $m[2] . now()->format('m');
                DB::table('users')->where('id', $row->id)->update([
                    'staff_id' => "{$m[1]}-{$yymm}-{$m[3]}",
                ]);
            });
    }

    public function down(): void
    {
        DB::table('users')->whereNotNull('staff_id')->get(['id', 'staff_id'])
            ->each(function ($row) {
                if (preg_match('/^([A-Z]{3})-(\d{2})\d{2}-([A-Z0-9]{4})$/', $row->staff_id, $m)) {
                    DB::table('users')->where('id', $row->id)->update([
                        'staff_id' => "{$m[1]}-{$m[2]}-{$m[3]}",
                    ]);
                }
            });
    }
};
