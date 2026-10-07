<?php

namespace App\Http\Controllers;

use App\Models\AncillaryRequest;
use App\Models\Announcement;
use App\Models\Appointment;
use App\Models\AuditLog;
use App\Models\Prescription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ArchiveController extends Controller
{
    /**
     * Get monitored archive category configuration and counts.
     */
    public function getCategories(): array
    {
        $cutoff = now()->subYear();

        return [
            'ancillary' => [
                'name' => 'Diagnostic Results (Lab & Radiology)',
                'description' => 'Archived, rejected, cancelled, or unfinished laboratory and radiology diagnostic tests and scan files.',
                'color' => 'indigo',
                'count' => AncillaryRequest::where(function ($q) {
                    $q->whereNotNull('archived_at')
                      ->orWhereIn('status', ['Rejected', 'Cancelled', 'Archived']);
                })->count(),
                'one_year_count' => AncillaryRequest::where(function ($q) use ($cutoff) {
                    $q->where(function ($sub) use ($cutoff) {
                        $sub->whereNotNull('archived_at')->where('archived_at', '<=', $cutoff);
                    })->orWhere(function ($sub) use ($cutoff) {
                        $sub->whereIn('status', ['Rejected', 'Cancelled', 'Archived'])
                            ->where(function ($dateQ) use ($cutoff) {
                                $dateQ->where('updated_at', '<=', $cutoff)->orWhere('created_at', '<=', $cutoff);
                            });
                    })->orWhere(function ($sub) use ($cutoff) {
                        $sub->whereIn('status', ['Pending', 'Processing', 'Specimen Collected', 'In Progress'])
                            ->where('created_at', '<=', $cutoff);
                    });
                })->count(),
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>',
            ],
            'appointments' => [
                'name' => 'Appointments',
                'description' => 'Cancelled and no-show patient appointment bookings.',
                'color' => 'amber',
                'count' => Appointment::whereIn('status', ['cancelled', 'no_show'])->count(),
                'one_year_count' => Appointment::whereIn('status', ['cancelled', 'no_show'])
                    ->where(function ($q) use ($cutoff) {
                        $q->where('cancelled_at', '<=', $cutoff)
                          ->orWhere('updated_at', '<=', $cutoff)
                          ->orWhere('created_at', '<=', $cutoff);
                    })->count(),
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>',
            ],
            'prescriptions' => [
                'name' => 'Prescriptions',
                'description' => 'Cancelled or expired physician prescription orders.',
                'color' => 'purple',
                'count' => Prescription::whereIn('status', ['cancelled', 'expired'])->count(),
                'one_year_count' => Prescription::whereIn('status', ['cancelled', 'expired'])
                    ->where(function ($q) use ($cutoff) {
                        $q->where('cancelled_at', '<=', $cutoff)
                          ->orWhere('expired_at', '<=', $cutoff)
                          ->orWhere('updated_at', '<=', $cutoff)
                          ->orWhere('created_at', '<=', $cutoff);
                    })->count(),
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>',
            ],
            'announcements' => [
                'name' => 'Announcements',
                'description' => 'Soft-deleted health advisories, news, and events.',
                'color' => 'teal',
                'count' => Announcement::onlyTrashed()->count(),
                'one_year_count' => Announcement::onlyTrashed()->where('deleted_at', '<=', $cutoff)->count(),
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>',
            ],
            'staff' => [
                'name' => 'Staff Accounts',
                'description' => 'Soft-deleted accounts for doctors, nurses, and staff.',
                'color' => 'blue',
                'count' => User::onlyTrashed()->count(),
                'one_year_count' => User::onlyTrashed()->where('deleted_at', '<=', $cutoff)->count(),
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />',
            ],
        ];
    }

    /**
     * Display the archive dashboard showing categories.
     */
    public function index()
    {
        $categories = $this->getCategories();
        $totalOneYearRecords = collect($categories)->sum('one_year_count');

        return view('admin.archive.index', compact('categories', 'totalOneYearRecords'));
    }

    /**
     * Display soft-deleted / archived records for a specific category.
     */
    public function show(Request $request, $type)
    {
        $categories = $this->getCategories();
        if (! isset($categories[$type])) {
            abort(404, 'Category not found.');
        }

        $categoryMeta = $categories[$type];
        $title = $categoryMeta['name'];
        $oneYearCount = $categoryMeta['one_year_count'] ?? 0;

        $perPage = $request->input('per_page', 10);

        switch ($type) {
            case 'announcements':
                $records = Announcement::onlyTrashed()->latest('deleted_at')->paginate($perPage)->withQueryString();
                $nameField = 'title';
                break;
            case 'staff':
                $records = User::onlyTrashed()->latest('deleted_at')->paginate($perPage)->withQueryString();
                $nameField = 'name';
                break;
            case 'ancillary':
                $records = AncillaryRequest::with(['consultation.patient'])
                    ->where(function ($q) {
                        $q->whereNotNull('archived_at')
                          ->orWhereIn('status', ['Rejected', 'Cancelled', 'Archived']);
                    })
                    ->latest('updated_at')
                    ->paginate($perPage)
                    ->withQueryString();
                $nameField = 'test_name';
                break;
            case 'appointments':
                $records = Appointment::whereIn('status', ['cancelled', 'no_show'])
                    ->latest('updated_at')
                    ->paginate($perPage)
                    ->withQueryString();
                $nameField = 'reference_number';
                break;
            case 'prescriptions':
                $records = Prescription::with(['patient', 'doctor', 'items'])
                    ->whereIn('status', ['cancelled', 'expired'])
                    ->latest('updated_at')
                    ->paginate($perPage)
                    ->withQueryString();
                $nameField = 'id';
                break;
            default:
                abort(404, 'Category not found.');
        }

        return view('admin.archive.show', compact('records', 'type', 'title', 'nameField', 'oneYearCount'));
    }

    /**
     * Restore a specific archived record.
     */
    public function restore($type, $id)
    {
        $record = $this->getRecord($type, $id);

        switch ($type) {
            case 'announcements':
            case 'staff':
                $record->restore();
                break;
            case 'ancillary':
                $record->update([
                    'archived_at' => null,
                    'archived_reason' => null,
                    'status' => 'Pending',
                    'rejection_reason' => null,
                    'rejected_by' => null,
                    'rejected_at' => null,
                    'cancellation_reason' => null,
                    'cancelled_by' => null,
                    'cancelled_at' => null,
                ]);
                break;
            case 'appointments':
                $record->update([
                    'status' => 'pending',
                    'cancelled_at' => null,
                    'cancelled_by' => null,
                    'cancellation_reason' => null,
                ]);
                break;
            case 'prescriptions':
                $record->update([
                    'status' => 'pending',
                    'cancelled_at' => null,
                    'cancelled_by' => null,
                    'cancellation_reason' => null,
                    'expired_at' => null,
                ]);
                break;
        }

        AuditLog::record('Archived Record Restored: '.ucfirst($type), $record);

        return back()->with('success', 'Record successfully restored.');
    }

    /**
     * Permanently delete an archived record.
     * Allowed for both admin and super_admin.
     */
    public function forceDelete($type, $id)
    {
        Gate::authorize('force-delete');

        $record = $this->getRecord($type, $id);
        $this->purgeSingleRecord($type, $record);

        AuditLog::record('Archived Record Permanently Purged: '.ucfirst($type));

        return back()->with('success', 'Record and associated files permanently deleted.');
    }

    /**
     * Helper to get the correct model instance.
     */
    private function getRecord($type, $id)
    {
        switch ($type) {
            case 'announcements':
                return Announcement::onlyTrashed()->findOrFail($id);
            case 'staff':
                return User::onlyTrashed()->findOrFail($id);
            case 'ancillary':
                return AncillaryRequest::findOrFail($id);
            case 'appointments':
                return Appointment::findOrFail($id);
            case 'prescriptions':
                return Prescription::findOrFail($id);
            default:
                abort(404, 'Category not found.');
        }
    }

    /**
     * Bulk restore soft-deleted records.
     */
    public function bulkRestore(Request $request, $type)
    {
        $request->validate([
            'ids' => 'required|array',
        ]);

        $ids = $request->ids;
        $count = count($ids);

        switch ($type) {
            case 'announcements':
                Announcement::onlyTrashed()->whereIn('id', $ids)->restore();
                break;
            case 'staff':
                User::onlyTrashed()->whereIn('id', $ids)->restore();
                break;
            case 'ancillary':
                AncillaryRequest::whereIn('id', $ids)->update([
                    'archived_at' => null,
                    'archived_reason' => null,
                    'status' => 'Pending',
                    'rejection_reason' => null,
                    'rejected_by' => null,
                    'rejected_at' => null,
                    'cancellation_reason' => null,
                    'cancelled_by' => null,
                    'cancelled_at' => null,
                ]);
                break;
            case 'appointments':
                Appointment::whereIn('id', $ids)->update([
                    'status' => 'pending',
                    'cancelled_at' => null,
                    'cancelled_by' => null,
                    'cancellation_reason' => null,
                ]);
                break;
            case 'prescriptions':
                Prescription::whereIn('id', $ids)->update([
                    'status' => 'pending',
                    'cancelled_at' => null,
                    'cancelled_by' => null,
                    'cancellation_reason' => null,
                    'expired_at' => null,
                ]);
                break;
            default:
                abort(404, 'Category not found.');
        }

        AuditLog::record("Bulk Restored: $count records in ".ucfirst($type));

        return back()->with('success', "$count records successfully restored.");
    }

    /**
     * Bulk permanently delete soft-deleted records.
     * Allowed for both admin and super_admin.
     */
    public function bulkForceDelete(Request $request, $type)
    {
        Gate::authorize('force-delete');

        $request->validate([
            'ids' => 'required|array',
        ]);

        $ids = $request->ids;
        $count = 0;

        switch ($type) {
            case 'announcements':
                $records = Announcement::onlyTrashed()->whereIn('id', $ids)->get();
                break;
            case 'staff':
                $records = User::onlyTrashed()->whereIn('id', $ids)->get();
                break;
            case 'ancillary':
                $records = AncillaryRequest::whereIn('id', $ids)->get();
                break;
            case 'appointments':
                $records = Appointment::whereIn('id', $ids)->get();
                break;
            case 'prescriptions':
                $records = Prescription::whereIn('id', $ids)->get();
                break;
            default:
                abort(404, 'Category not found.');
        }

        foreach ($records as $record) {
            $this->purgeSingleRecord($type, $record);
            $count++;
        }

        AuditLog::record("Bulk Purged: $count records and associated files in ".ucfirst($type));

        return back()->with('success', "$count records permanently deleted.");
    }

    /**
     * Permanently purge records older than 1 year for a specific category.
     * Strictly Super Admin only.
     */
    public function truncateCategoryOneYear(Request $request, $type)
    {
        Gate::authorize('truncate-archive');

        $cutoff = now()->subYear();
        $purged = $this->purgeOneYearForType($type, $cutoff);

        AuditLog::record("1-Year Truncate Executed for $type: $purged records purged.");

        return back()->with('success', "1-Year Truncation Complete: $purged archived/rejected/unfinished records older than 1 year were permanently deleted.");
    }

    /**
     * Permanently purge records older than 1 year across ALL categories.
     * Strictly Super Admin only.
     */
    public function truncateAllOneYear(Request $request)
    {
        Gate::authorize('truncate-archive');

        $cutoff = now()->subYear();
        $categories = array_keys($this->getCategories());
        $totalPurged = 0;

        foreach ($categories as $cat) {
            $totalPurged += $this->purgeOneYearForType($cat, $cutoff);
        }

        AuditLog::record("System-wide 1-Year Archive Truncate Executed: $totalPurged records purged.");

        return back()->with('success', "System-wide 1-Year Truncate Complete: $totalPurged archived/rejected/unfinished records older than 1 year and their attached files were permanently purged.");
    }

    /**
     * Execute purging of records older than 1 year for a given category.
     */
    private function purgeOneYearForType(string $type, Carbon $cutoff): int
    {
        $count = 0;

        switch ($type) {
            case 'ancillary':
                $records = AncillaryRequest::where(function ($q) use ($cutoff) {
                    $q->where(function ($sub) use ($cutoff) {
                        $sub->whereNotNull('archived_at')->where('archived_at', '<=', $cutoff);
                    })->orWhere(function ($sub) use ($cutoff) {
                        $sub->whereIn('status', ['Rejected', 'Cancelled', 'Archived'])
                            ->where(function ($dateQ) use ($cutoff) {
                                $dateQ->where('updated_at', '<=', $cutoff)
                                      ->orWhere('created_at', '<=', $cutoff);
                            });
                    })->orWhere(function ($sub) use ($cutoff) {
                        $sub->whereIn('status', ['Pending', 'Processing', 'Specimen Collected', 'In Progress'])
                            ->where('created_at', '<=', $cutoff);
                    });
                })->get();

                foreach ($records as $record) {
                    $this->purgeSingleRecord('ancillary', $record);
                    $count++;
                }
                break;

            case 'appointments':
                $records = Appointment::whereIn('status', ['cancelled', 'no_show'])
                    ->where(function ($q) use ($cutoff) {
                        $q->where('cancelled_at', '<=', $cutoff)
                          ->orWhere('updated_at', '<=', $cutoff)
                          ->orWhere('created_at', '<=', $cutoff);
                    })->get();

                foreach ($records as $record) {
                    $this->purgeSingleRecord('appointments', $record);
                    $count++;
                }
                break;

            case 'prescriptions':
                $records = Prescription::whereIn('status', ['cancelled', 'expired'])
                    ->where(function ($q) use ($cutoff) {
                        $q->where('cancelled_at', '<=', $cutoff)
                          ->orWhere('expired_at', '<=', $cutoff)
                          ->orWhere('updated_at', '<=', $cutoff)
                          ->orWhere('created_at', '<=', $cutoff);
                    })->get();

                foreach ($records as $record) {
                    $this->purgeSingleRecord('prescriptions', $record);
                    $count++;
                }
                break;

            case 'announcements':
                $records = Announcement::onlyTrashed()->where('deleted_at', '<=', $cutoff)->get();
                foreach ($records as $record) {
                    $this->purgeSingleRecord('announcements', $record);
                    $count++;
                }
                break;

            case 'staff':
                $records = User::onlyTrashed()->where('deleted_at', '<=', $cutoff)->get();
                foreach ($records as $record) {
                    $this->purgeSingleRecord('staff', $record);
                    $count++;
                }
                break;
        }

        return $count;
    }

    /**
     * Purge a single record and its associated physical storage files.
     */
    private function purgeSingleRecord(string $type, $record): void
    {
        switch ($type) {
            case 'announcements':
                if ($record->image_path) {
                    $path = str_replace('uploads/', '', $record->image_path);
                    if (Storage::disk('uploads')->exists($path)) {
                        Storage::disk('uploads')->delete($path);
                    }
                }
                foreach ($record->images()->get() as $image) {
                    if ($image->image_path) {
                        $path = str_replace('uploads/', '', $image->image_path);
                        if (Storage::disk('uploads')->exists($path)) {
                            Storage::disk('uploads')->delete($path);
                        }
                    }
                    $image->delete();
                }
                $record->forceDelete();
                break;

            case 'staff':
                if ($record->avatar_path) {
                    $cleanPath = ltrim(str_replace(['uploads/', 'storage/'], '', $record->avatar_path), '/');
                    Storage::disk('uploads')->delete($cleanPath);
                    Storage::disk('public')->delete($cleanPath);
                }
                $record->forceDelete();
                break;

            case 'ancillary':
                if ($record->result_file_path) {
                    Storage::disk('local')->delete($record->result_file_path);
                    Storage::disk('public')->delete($record->result_file_path);
                    Storage::disk('uploads')->delete($record->result_file_path);
                }
                $record->delete();
                break;

            case 'appointments':
                $record->delete();
                break;

            case 'prescriptions':
                $record->items()->delete();
                $record->delete();
                break;
        }
    }
}
