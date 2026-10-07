<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view-audit-logs');

        $query = AuditLog::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%$search%")
                    ->orWhere('model_type', 'like', "%$search%")
                    ->orWhere('model_id', 'like', "%$search%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%$search%");
                    });
            });
        }

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('action', 'like', "%{$request->type}%");
        }

        $perPage = $request->input('per_page', 10);
        $logs = $query->latest()->paginate($perPage)->withQueryString();

        return view('admin.audit.index', compact('logs'));
    }

    public function exportCsv(Request $request)
    {
        Gate::authorize('view-audit-logs');

        $query = AuditLog::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('action', 'like', "%$search%")
                    ->orWhere('model_type', 'like', "%$search%")
                    ->orWhere('model_id', 'like', "%$search%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%$search%");
                    });
            });
        }

        if ($request->filled('type') && $request->type !== 'all') {
            $query->where('action', 'like', "%{$request->type}%");
        }

        $count = $query->count();

        if ($count === 0) {
            return response()->json(['message' => 'No audit logs found matching the filter criteria.'], 404);
        }

        $filename = 'system-audit-trail-'.now()->format('Y-m-d_His').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'no-store, no-cache',
        ];

        $csvHeaders = [
            'Log ID',
            'Timestamp',
            'Action / Event',
            'Personnel Name',
            'Personnel Role',
            'Personnel Email',
            'Target Model',
            'Target ID',
            'IP Address',
            'User Agent',
            'Changes / Details',
        ];

        return response()->stream(function () use ($query, $csvHeaders) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, $csvHeaders);

            $query->latest()->chunk(250, function ($logs) use ($handle) {
                foreach ($logs as $log) {
                    $user = $log->user;
                    $changesSummary = $this->formatAuditChangesForCsv($log->changes);

                    fputcsv($handle, [
                        $log->id,
                        $log->created_at ? $log->created_at->format('Y-m-d H:i:s') : 'N/A',
                        $log->action ?? 'N/A',
                        $user ? $user->name : 'System / Deleted User',
                        $user ? ucwords(str_replace('_', ' ', $user->role ?? '')) : 'N/A',
                        $user ? $user->email : 'N/A',
                        $log->model_type ? class_basename($log->model_type) : 'N/A',
                        $log->model_id ?? 'N/A',
                        $log->ip_address ?? 'N/A',
                        $log->user_agent ?? 'N/A',
                        $changesSummary,
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Format audit log changes array/JSON into human-friendly staff-readable text.
     */
    public function formatAuditChangesForCsv($changes): string
    {
        if (empty($changes)) {
            return 'No specific property changes recorded';
        }

        if (is_string($changes)) {
            $decoded = json_decode($changes, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $changes = $decoded;
            } else {
                return trim($changes);
            }
        }

        if (! is_array($changes)) {
            return (string) $changes;
        }

        $ignoredKeys = ['id', 'created_at', 'updated_at', 'deleted_at', 'remember_token', 'password', 'email_verified_at', 'expires_at', 'philhealth_number'];

        if (array_key_exists('old', $changes) && array_key_exists('new', $changes)) {
            $old = $changes['old'];
            $new = $changes['new'];

            if (is_null($old) && is_array($new)) {
                $createdParts = [];
                foreach ($new as $k => $v) {
                    if (in_array($k, $ignoredKeys) || is_null($v) || $v === '') {
                        continue;
                    }
                    $label = ucwords(str_replace(['_', '-'], ' ', $k));
                    $valStr = is_bool($v) ? ($v ? 'Yes' : 'No') : (is_array($v) ? implode(', ', $v) : (string) $v);
                    $createdParts[] = "{$label}: {$valStr}";
                }

                return 'Initial Creation ('.implode('; ', $createdParts).')';
            }

            if (is_array($old) && is_array($new)) {
                $diffParts = [];
                $allKeys = array_unique(array_merge(array_keys($old), array_keys($new)));
                foreach ($allKeys as $k) {
                    if (in_array($k, $ignoredKeys)) {
                        continue;
                    }
                    $oldV = $old[$k] ?? null;
                    $newV = $new[$k] ?? null;
                    if ($oldV !== $newV) {
                        $label = ucwords(str_replace(['_', '-'], ' ', $k));
                        $oldStr = is_null($oldV) || $oldV === '' ? 'Empty' : (is_bool($oldV) ? ($oldV ? 'Yes' : 'No') : (string) $oldV);
                        $newStr = is_null($newV) || $newV === '' ? 'Empty' : (is_bool($newV) ? ($newV ? 'Yes' : 'No') : (string) $newV);
                        $diffParts[] = "{$label}: \"{$oldStr}\" -> \"{$newStr}\"";
                    }
                }

                return ! empty($diffParts) ? implode(' | ', $diffParts) : 'Record updated with no visible field changes';
            }
        }

        $formatted = [];
        foreach ($changes as $key => $val) {
            if (in_array($key, $ignoredKeys)) {
                continue;
            }
            $label = ucwords(str_replace(['_', '-'], ' ', $key));

            if (is_array($val)) {
                if (array_key_exists('old', $val) || array_key_exists('new', $val)) {
                    $oldVal = $val['old'] ?? null;
                    $newVal = $val['new'] ?? null;
                    $oldStr = is_null($oldVal) || $oldVal === '' ? 'Empty' : (is_bool($oldVal) ? ($oldVal ? 'Yes' : 'No') : (string) $oldVal);
                    $newStr = is_null($newVal) || $newVal === '' ? 'Empty' : (is_bool($newVal) ? ($newVal ? 'Yes' : 'No') : (string) $newVal);
                    $formatted[] = "{$label}: \"{$oldStr}\" -> \"{$newStr}\"";
                } else {
                    $subParts = [];
                    foreach ($val as $subK => $subV) {
                        if (in_array($subK, $ignoredKeys) || is_null($subV) || $subV === '') {
                            continue;
                        }
                        $subLabel = ucwords(str_replace(['_', '-'], ' ', $subK));
                        $subValStr = is_array($subV) ? implode(', ', $subV) : (is_bool($subV) ? ($subV ? 'Yes' : 'No') : (string) $subV);
                        $subParts[] = "{$subLabel}: {$subValStr}";
                    }
                    if (! empty($subParts)) {
                        $formatted[] = "{$label} (".implode('; ', $subParts).')';
                    }
                }
            } else {
                if (is_bool($val)) {
                    $val = $val ? 'Yes' : 'No';
                }
                if (is_null($val) || $val === '') {
                    continue;
                }
                $formatted[] = "{$label}: {$val}";
            }
        }

        return ! empty($formatted) ? implode(' | ', $formatted) : 'Updated';
    }
}
