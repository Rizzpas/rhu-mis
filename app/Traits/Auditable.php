<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            self::logAction($model, 'Created', null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $changes = $model->getChanges();
            $oldValues = array_intersect_key($model->getOriginal(), $changes);

            // Don't log if no meaningful changes or just updated_at
            if (empty($changes) || (count($changes) === 1 && isset($changes['updated_at']))) {
                return;
            }

            self::logAction($model, 'Updated', $oldValues, $changes);
        });

        static::deleted(function ($model) {
            self::logAction($model, 'Deleted', $model->getAttributes(), null);
        });
    }

    protected static function logAction($model, $action, $oldValues, $newValues)
    {
        // BEST PRACTICE: Exclude sensitive fields from ANY audit log
        $excluded = ['password', 'remember_token', 'two_factor_recovery_codes', 'two_factor_secret'];

        if ($oldValues) {
            $oldValues = array_diff_key($oldValues, array_flip($excluded));
        }
        if ($newValues) {
            $newValues = array_diff_key($newValues, array_flip($excluded));
        }

        // Allow logging even if not logged in (e.g. public appointment booking)
        // to maintain a complete clinical bridge.
        if (! Auth::check() && ! app()->runningInConsole() && ! in_array($action, ['Created Appointment', 'Created Patient'])) {
            // Optional: You could still log these but we might want to be selective
        }

        AuditLog::create([
            'user_id' => Auth::id() ?? null,
            'action' => $action.' '.class_basename($model),
            'model_type' => get_class($model),
            'model_id' => $model->patient_id ?? $model->id ?? null,
            'changes' => [
                'old' => $oldValues,
                'new' => $newValues,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
