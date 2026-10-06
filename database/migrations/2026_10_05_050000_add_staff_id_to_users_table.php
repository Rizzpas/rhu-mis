<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'staff_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('staff_id', 20)->nullable()->after('id');
            });
        }

        // Backfill existing staff (including soft-deleted) without touching any other column.
        DB::table('users')->whereNull('staff_id')->orderBy('id')->get(['id', 'role', 'created_at'])
            ->each(function ($row) {
                $year = $row->created_at ? date('ym', strtotime($row->created_at)) : now()->format('ym');
                DB::table('users')->where('id', $row->id)->update([
                    'staff_id' => User::generateStaffId($row->role, $year),
                ]);
            });

        Schema::table('users', function (Blueprint $table) {
            $table->unique('staff_id');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'staff_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropUnique(['staff_id']);
                $table->dropColumn('staff_id');
            });
        }
    }
};
