<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class ArchiveController extends Controller
{
    /**
     * Display the archive dashboard showing categories.
     */
    public function index()
    {
        $categories = [
            'announcements' => [
                'name' => 'Announcements',
                'description' => 'Soft-deleted health advisories, news, and events.',
                'color' => 'teal',
                'count' => Announcement::onlyTrashed()->count(),
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>',
            ],
            'staff' => [
                'name' => 'Staff Accounts',
                'description' => 'Soft-deleted accounts for doctors, nurses, and staff.',
                'color' => 'blue',
                'count' => \App\Models\User::onlyTrashed()->count(),
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />',
            ],
        ];

        return view('admin.archive.index', compact('categories'));
    }

    /**
     * Display soft-deleted records for a specific category.
     */
    public function show($type)
    {
        switch ($type) {
            case 'announcements':
                $records = Announcement::onlyTrashed()->latest('deleted_at')->get();
                $title = 'Archived Announcements';
                $nameField = 'title';
                break;
            case 'staff':
                $records = \App\Models\User::onlyTrashed()->latest('deleted_at')->get();
                $title = 'Archived Staff Accounts';
                $nameField = 'name';
                break;
            default:
                abort(404, 'Category not found.');
        }

        return view('admin.archive.show', compact('records', 'type', 'title', 'nameField'));
    }

    /**
     * Restore a specific soft-deleted record.
     */
    public function restore($type, $id)
    {
        $record = $this->getRecord($type, $id);
        $record->restore();

        return back()->with('success', 'Record successfully restored.');
    }

    /**
     * Permanently delete a soft-deleted record.
     */
    public function forceDelete($type, $id)
    {
        // Only super_admin can permanently delete archived records
        \Illuminate\Support\Facades\Gate::authorize('force-delete');

        $record = $this->getRecord($type, $id);

        // Handle physical file deletions before force deleting the record
        if ($type === 'announcements') {
            if ($record->image_path) {
                $path = str_replace('uploads/', '', $record->image_path);
                if (\Illuminate\Support\Facades\Storage::disk('uploads')->exists($path)) {
                    \Illuminate\Support\Facades\Storage::disk('uploads')->delete($path);
                }
            }
            // Delete associated gallery images physically
            foreach ($record->images()->get() as $image) {
                if ($image->image_path) {
                    $path = str_replace('uploads/', '', $image->image_path);
                    if (\Illuminate\Support\Facades\Storage::disk('uploads')->exists($path)) {
                        \Illuminate\Support\Facades\Storage::disk('uploads')->delete($path);
                    }
                }
                $image->delete();
            }
        } elseif ($type === 'staff') {
            if ($record->avatar_path) {
                $cleanPath = ltrim(str_replace(['uploads/', 'storage/'], '', $record->avatar_path), '/');
                \Illuminate\Support\Facades\Storage::disk('uploads')->delete($cleanPath);
                \Illuminate\Support\Facades\Storage::disk('public')->delete($cleanPath);
            }
        }

        $record->forceDelete();

        return back()->with('success', 'Record permanently deleted.');
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
                return \App\Models\User::onlyTrashed()->findOrFail($id);
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

        $count = count($request->ids);

        if ($type === 'announcements') {
            Announcement::onlyTrashed()->whereIn('id', $request->ids)->restore();
        } elseif ($type === 'staff') {
            \App\Models\User::onlyTrashed()->whereIn('id', $request->ids)->restore();
        } else {
            abort(404, 'Category not found.');
        }

        return back()->with('success', "$count records successfully restored.");
    }

    /**
     * Bulk permanently delete soft-deleted records.
     */
    public function bulkForceDelete(Request $request, $type)
    {
        // Only super_admin can permanently delete archived records
        \Illuminate\Support\Facades\Gate::authorize('force-delete');

        $request->validate([
            'ids' => 'required|array',
        ]);

        $ids = $request->ids;
        $count = count($ids);

        if ($type === 'announcements') {
            $records = Announcement::onlyTrashed()->whereIn('id', $ids)->get();
            foreach ($records as $record) {
                if ($record->image_path) {
                    $path = str_replace('uploads/', '', $record->image_path);
                    if (\Illuminate\Support\Facades\Storage::disk('uploads')->exists($path)) {
                        \Illuminate\Support\Facades\Storage::disk('uploads')->delete($path);
                    }
                }
                foreach ($record->images()->get() as $image) {
                    if ($image->image_path) {
                        $path = str_replace('uploads/', '', $image->image_path);
                        if (\Illuminate\Support\Facades\Storage::disk('uploads')->exists($path)) {
                            \Illuminate\Support\Facades\Storage::disk('uploads')->delete($path);
                        }
                    }
                    $image->delete();
                }
            }
            Announcement::onlyTrashed()->whereIn('id', $ids)->forceDelete();
        } elseif ($type === 'staff') {
            $records = \App\Models\User::onlyTrashed()->whereIn('id', $ids)->get();
            foreach ($records as $record) {
                if ($record->avatar_path) {
                    $cleanPath = ltrim(str_replace(['uploads/', 'storage/'], '', $record->avatar_path), '/');
                    \Illuminate\Support\Facades\Storage::disk('uploads')->delete($cleanPath);
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($cleanPath);
                }
            }
            \App\Models\User::onlyTrashed()->whereIn('id', $ids)->forceDelete();
        } else {
            abort(404, 'Category not found.');
        }

        return back()->with('success', "$count records permanently deleted.");
    }
}
