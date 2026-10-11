<?php

namespace App\Listeners;

use App\Models\AuditLog;
use Illuminate\Auth\Events\Failed;

class LogFailedLogin
{
    public function handle(Failed $event): void
    {
        AuditLog::create([
            'user_id' => $event->user?->id,
            'action' => 'failed_login',
            'model_type' => $event->user ? get_class($event->user) : null,
            'model_id' => $event->user?->id,
            'changes' => [
                'email' => $event->credentials['email'] ?? null,
                'reason' => 'invalid_credentials',
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
