<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('patients', 'is_pwd')) {
            Schema::table('patients', function (Blueprint $table) {
                $table->boolean('is_pwd')->default(false)->after('classification');
            });
        }

        // Backfill existing patients where classification was 'PWD'
        $pwdPatients = DB::table('patients')->where('classification', 'PWD')->get();
        foreach ($pwdPatients as $patient) {
            $classification = 'Regular Adult';
            if ($patient->dob) {
                $age = Carbon::parse($patient->dob)->age;
                if ($age <= 12) {
                    $classification = 'Pediatric';
                } elseif ($age >= 60) {
                    $classification = 'Senior Citizen';
                } else {
                    $classification = 'Regular Adult';
                }
            }

            DB::table('patients')
                ->where('id', $patient->id)
                ->update([
                    'is_pwd' => true,
                    'classification' => $classification,
                ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('patients', 'is_pwd')) {
            // Revert backfilled PWD classification where is_pwd is true
            DB::table('patients')->where('is_pwd', true)->update(['classification' => 'PWD']);

            Schema::table('patients', function (Blueprint $table) {
                $table->dropColumn('is_pwd');
            });
        }
    }
};
