<?php

namespace App\Listeners;

use App\Models\AuditLog;
use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        if ($event->user) {
            AuditLog::create([
                'user_id' => $event->user->id,
                'action' => 'login',
                'model_type' => get_class($event->user),
                'model_id' => $event->user->id,
                'changes' => [
                    'email' => $event->user->email,
                    'role' => $event->user->role,
                    'name' => $event->user->name,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }
}
