<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('consultations')) {
            Schema::table('consultations', function (Blueprint $table) {
                if (! Schema::hasIndex('consultations', 'consultations_patient_status_idx')) {
                    $table->index(['patient_id', 'status'], 'consultations_patient_status_idx');
                }
                if (! Schema::hasIndex('consultations', 'consultations_doctor_status_idx')) {
                    $table->index(['doctor_id', 'status'], 'consultations_doctor_status_idx');
                }
                if (! Schema::hasIndex('consultations', 'consultations_nurse_status_idx')) {
                    $table->index(['nurse_id', 'status'], 'consultations_nurse_status_idx');
                }
                if (! Schema::hasIndex('consultations', 'consultations_created_status_idx')) {
                    $table->index(['created_at', 'status'], 'consultations_created_status_idx');
                }
            });
        }

        if (Schema::hasTable('appointments')) {
            Schema::table('appointments', function (Blueprint $table) {
                if (! Schema::hasIndex('appointments', 'appointments_preferred_status_idx')) {
                    $table->index(['preferred_date', 'status'], 'appointments_preferred_status_idx');
                }
                if (! Schema::hasIndex('appointments', 'appointments_email_status_idx')) {
                    $table->index(['email', 'status'], 'appointments_email_status_idx');
                }
            });
        }

        if (Schema::hasTable('pre_triages')) {
            Schema::table('pre_triages', function (Blueprint $table) {
                if (! Schema::hasIndex('pre_triages', 'pre_triages_status_created_idx')) {
                    $table->index(['status', 'created_at'], 'pre_triages_status_created_idx');
                }
            });
        }

        if (Schema::hasTable('queues')) {
            Schema::table('queues', function (Blueprint $table) {
                if (! Schema::hasIndex('queues', 'queues_status_created_idx')) {
                    $table->index(['status', 'created_at'], 'queues_status_created_idx');
                }
            });
        }

        if (Schema::hasTable('prescriptions')) {
            Schema::table('prescriptions', function (Blueprint $table) {
                if (! Schema::hasIndex('prescriptions', 'prescriptions_status_expires_idx')) {
                    $table->index(['status', 'expires_at'], 'prescriptions_status_expires_idx');
                }
            });
        }

        if (Schema::hasTable('medicine_batches')) {
            Schema::table('medicine_batches', function (Blueprint $table) {
                if (! Schema::hasIndex('medicine_batches', 'batches_med_status_exp_idx')) {
                    $table->index(['medicine_id', 'status', 'expiration_date'], 'batches_med_status_exp_idx');
                }
            });
        }

        if (Schema::hasTable('audit_logs')) {
            Schema::table('audit_logs', function (Blueprint $table) {
                if (! Schema::hasIndex('audit_logs', 'audit_logs_model_idx')) {
                    $table->index(['model_type', 'model_id'], 'audit_logs_model_idx');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('consultations')) {
            Schema::table('consultations', function (Blueprint $table) {
                $table->dropIndex('consultations_patient_status_idx');
                $table->dropIndex('consultations_doctor_status_idx');
                $table->dropIndex('consultations_nurse_status_idx');
                $table->dropIndex('consultations_created_status_idx');
            });
        }

        if (Schema::hasTable('appointments')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->dropIndex('appointments_preferred_status_idx');
                $table->dropIndex('appointments_email_status_idx');
            });
        }

        if (Schema::hasTable('pre_triages')) {
            Schema::table('pre_triages', function (Blueprint $table) {
                $table->dropIndex('pre_triages_status_created_idx');
            });
        }

        if (Schema::hasTable('queues')) {
            Schema::table('queues', function (Blueprint $table) {
                $table->dropIndex('queues_status_created_idx');
            });
        }

        if (Schema::hasTable('prescriptions')) {
            Schema::table('prescriptions', function (Blueprint $table) {
                $table->dropIndex('prescriptions_status_expires_idx');
            });
        }

        if (Schema::hasTable('medicine_batches')) {
            Schema::table('medicine_batches', function (Blueprint $table) {
                $table->dropIndex('batches_med_status_exp_idx');
            });
        }

        if (Schema::hasTable('audit_logs')) {
            Schema::table('audit_logs', function (Blueprint $table) {
                $table->dropIndex('audit_logs_model_idx');
            });
        }
    }
};
