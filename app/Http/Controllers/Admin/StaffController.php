<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\PractitionerSchedule;
use App\Models\User;
use App\Rules\SecureImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $query = User::whereIn('role', ['super_admin', 'admin', 'regular_doctor', 'pedia_doctor', 'laboratory', 'radiology', 'vitals_nurse', 'clinical_nurse', 'information_desk', 'pharmacy']);

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'Online') {
                $query->present();
            } elseif ($request->status === 'Offline') {
                $query->whereNotIn('status', ['Online', 'online'])
                      ->where(function ($q) {
                          $q->whereNull('last_activity_at')
                            ->orWhere('last_activity_at', '<', now()->subMinutes(10));
                      });
            } else {
                $query->where('status', $request->status);
            }
        }

        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10, 20, 50])) {
            $perPage = 10;
        }

        $total = (clone $query)->count();
        $totalPages = max(1, (int) ceil($total / $perPage));

        $rawPage = $request->input('page', 1);
        $page = filter_var($rawPage, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'default' => 1]]);
        if ($page > $totalPages && $total > 0) {
            $page = $totalPages;
        }

        $staff = $query->with('practitionerSchedules')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page)
            ->withQueryString();

        return view('admin.staff.index', compact('staff'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:super_admin,admin,regular_doctor,pedia_doctor,laboratory,radiology,vitals_nurse,clinical_nurse,information_desk,pharmacy',
            'status' => 'required|string',
            'schedule' => 'nullable|string',
            'avatar' => ['nullable', 'file', 'max:2048', new SecureImage],
        ]);

        if (in_array($validated['role'], ['admin', 'super_admin'])) {
            Gate::authorize('promote-admin');
        }

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('staff', 'uploads');
        }

        $name = ucwords(strtolower(trim(str_replace(['Dr. ', 'Dr '], '', $validated['name']))));

        $normalizedStatus = match (strtolower($validated['status'])) {
            'online', 'present' => 'Online',
            'offline', 'unavailable', 'out of office' => 'Offline',
            'occupied', 'seminar', 'in meeting' => 'Occupied',
            default => $validated['status']
        };

        $user = User::create([
            'name' => $name,
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'status' => $normalizedStatus,
            'last_activity_at' => $normalizedStatus === 'Online' ? now() : null,
            'avatar_path' => $avatarPath,
        ]);
        $user->role = $validated['role'];
        $user->save();

        if (! empty($validated['schedule'])) {
            $schedules = json_decode($validated['schedule'], true);
            if (is_array($schedules)) {
                foreach ($schedules as $sched) {
                    PractitionerSchedule::create([
                        'user_id' => $user->id,
                        'day_of_week' => $sched['day'],
                        'time_in' => $sched['time_in'],
                        'time_out' => $sched['time_out'],
                    ]);
                }
            }
        }

        return back()->with('success', 'Staff account created successfully!');
    }

    public function update(Request $request, User $user)
    {
        if (in_array($user->role, ['admin', 'super_admin']) && $user->id !== auth()->id()) {
            Gate::authorize('manage-admins');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required|in:super_admin,admin,regular_doctor,pedia_doctor,laboratory,radiology,vitals_nurse,clinical_nurse,information_desk,pharmacy',
            'status' => 'required|string',
            'schedule' => 'nullable|string',
            'password' => 'nullable|string|min:8',
            'avatar' => ['nullable', 'file', 'max:2048', new SecureImage],
        ]);

        if (in_array($validated['role'], ['admin', 'super_admin'])) {
            Gate::authorize('promote-admin');
        }

        $name = ucwords(strtolower(trim(str_replace(['Dr. ', 'Dr '], '', $validated['name']))));

        $normalizedStatus = match (strtolower($validated['status'])) {
            'online', 'present' => 'Online',
            'offline', 'unavailable', 'out of office' => 'Offline',
            'occupied', 'seminar', 'in meeting' => 'Occupied',
            default => $validated['status']
        };

        $data = [
            'name' => $name,
            'email' => $validated['email'],
            'status' => $normalizedStatus,
        ];

        if ($normalizedStatus === 'Online') {
            $data['last_activity_at'] = now();
        } elseif ($normalizedStatus === 'Offline') {
            $data['last_activity_at'] = null;
        }

        if (! empty($validated['password'])) {
            $data['password'] = bcrypt($validated['password']);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar_path) {
                $cleanPath = ltrim(str_replace(['uploads/', 'storage/'], '', $user->avatar_path), '/');
                Storage::disk('public')->delete($cleanPath);
                Storage::disk('uploads')->delete($cleanPath);
            }
            $data['avatar_path'] = $request->file('avatar')->store('staff', 'uploads');
        }

        $user->update($data);

        if ($user->role !== $validated['role']) {
            $user->role = $validated['role'];
            $user->save();
        }

        if ($request->has('schedule')) {
            $user->practitionerSchedules()->delete();
            if (! empty($validated['schedule'])) {
                $schedules = json_decode($validated['schedule'], true);
                if (is_array($schedules)) {
                    foreach ($schedules as $sched) {
                        PractitionerSchedule::create([
                            'user_id' => $user->id,
                            'day_of_week' => $sched['day'],
                            'time_in' => $sched['time_in'],
                            'time_out' => $sched['time_out'],
                        ]);
                    }
                }
            }
        }

        return back()->with('success', "{$user->name}'s profile has been updated successfully!");
    }

    public function destroy(User $user)
    {
        Gate::authorize('delete-staff');

        if (in_array($user->role, ['admin', 'super_admin'])) {
            Gate::authorize('manage-admins');
        }

        if ($user->role === 'admin' && User::where('role', 'admin')->count() === 1) {
            return back()->with('error', 'Cannot delete the only admin account.');
        }

        if ($user->role === 'super_admin' && User::where('role', 'super_admin')->count() === 1) {
            return back()->with('error', 'Cannot delete the only super admin account.');
        }

        $user->delete();

        return back()->with('success', 'Staff archived successfully!');
    }

    public function updateStatus(Request $request, User $user)
    {
        if (in_array($user->role, ['admin', 'super_admin']) && $user->id !== auth()->id()) {
            Gate::authorize('manage-admins');
        }

        $request->validate([
            'status' => 'required|string',
        ]);

        $normalizedStatus = match (strtolower($request->status)) {
            'online', 'present', 'active' => 'Online',
            'offline', 'unavailable', 'out of office' => 'Offline',
            'occupied', 'seminar', 'in meeting' => 'Occupied',
            default => $request->status
        };

        $updateData = ['status' => $normalizedStatus];

        if ($normalizedStatus === 'Offline') {
            $updateData['schedule_override'] = 'manual_offline';
            $updateData['last_activity_at'] = null;
        } elseif ($normalizedStatus === 'Online') {
            $updateData['schedule_override'] = 'manual_online';
            $updateData['last_activity_at'] = now();
        } else {
            $updateData['schedule_override'] = 'manual_online';
            $updateData['last_activity_at'] = now();
        }

        $oldStatus = $user->status;
        $user->update($updateData);

        AuditLog::record("Staff status updated: {$user->name} ({$oldStatus} -> {$normalizedStatus})", $user, [
            'old_status' => $oldStatus,
            'new_status' => $normalizedStatus,
            'schedule_override' => $updateData['schedule_override'] ?? null,
        ]);

        return back()->with('success', "Staff status updated to {$normalizedStatus} successfully!");
    }

    public function promote(User $user)
    {
        Gate::authorize('promote-admin');

        if (in_array($user->role, ['regular_doctor', 'pedia_doctor', 'laboratory', 'radiology', 'clinical_nurse', 'vitals_nurse', 'information_desk', 'pharmacy'])) {
            $user->update(['role' => 'admin']);

            return back()->with('success', "{$user->name} has been promoted to Administrator!");
        }

        return back()->with('error', 'Invalid user role for promotion.');
    }

    public function bulkDelete(Request $request)
    {
        Gate::authorize('delete-staff');

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
        ]);

        $ids = $request->ids;
        $usersToDelete = User::whereIn('id', $ids)->get();

        $hasAdminTargets = $usersToDelete->whereIn('role', ['admin', 'super_admin'])->isNotEmpty();
        if ($hasAdminTargets) {
            Gate::authorize('manage-admins');
        }

        $adminCountToDelete = $usersToDelete->where('role', 'admin')->count();
        $totalAdmins = User::where('role', 'admin')->count();
        if ($adminCountToDelete > 0 && $totalAdmins <= $adminCountToDelete) {
            return back()->with('error', 'Cannot delete all admin accounts.');
        }

        $superAdminCountToDelete = $usersToDelete->where('role', 'super_admin')->count();
        $totalSuperAdmins = User::where('role', 'super_admin')->count();
        if ($superAdminCountToDelete > 0 && $totalSuperAdmins <= $superAdminCountToDelete) {
            return back()->with('error', 'Cannot delete all super admin accounts.');
        }

        User::whereIn('id', $ids)->delete();

        return back()->with('success', count($ids).' staff members archived successfully!');
    }

    public function bulkStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
            'status' => 'required|string|in:Online,Offline,Occupied',
        ]);

        $targetAdminCount = User::whereIn('id', $request->ids)
            ->whereIn('role', ['admin', 'super_admin'])
            ->count();

        if ($targetAdminCount > 0 && ! auth()->user()->can('manage-admins')) {
            abort(403, 'Regular administrators cannot modify the status of administrative accounts.');
        }

        $normalizedStatus = match (strtolower($request->status)) {
            'online', 'present', 'active' => 'Online',
            'offline', 'unavailable', 'out of office' => 'Offline',
            'occupied', 'seminar', 'in meeting' => 'Occupied',
            default => $request->status
        };

        $updateData = ['status' => $normalizedStatus];

        if ($normalizedStatus === 'Offline') {
            $updateData['schedule_override'] = 'manual_offline';
            $updateData['last_activity_at'] = null;
        } elseif ($normalizedStatus === 'Online') {
            $updateData['schedule_override'] = 'manual_online';
            $updateData['last_activity_at'] = now();
        } else {
            $updateData['schedule_override'] = 'manual_online';
            $updateData['last_activity_at'] = now();
        }

        $targetIds = array_values(array_filter($request->ids, fn ($id) => (int) $id !== (int) auth()->id()));

        if (empty($targetIds)) {
            return back()->with('warning', 'No eligible staff members selected for status update.');
        }

        User::whereIn('id', $targetIds)->update($updateData);

        AuditLog::record("Bulk Staff Status Updated: {$normalizedStatus} for ".count($targetIds).' accounts', null, [
            'ids' => $targetIds,
            'status' => $normalizedStatus,
            'schedule_override' => $updateData['schedule_override'] ?? null,
        ]);

        return back()->with('success', count($targetIds)." staff members set to {$normalizedStatus} successfully!");
    }

    public function bulkPromote(Request $request)
    {
        Gate::authorize('promote-admin');

        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
        ]);

        $promotableRoles = ['regular_doctor', 'pedia_doctor', 'laboratory', 'radiology', 'clinical_nurse', 'vitals_nurse', 'information_desk', 'pharmacy'];

        $promoted = User::whereIn('id', $request->ids)
            ->whereIn('role', $promotableRoles)
            ->update(['role' => 'admin']);

        if ($promoted === 0) {
            return back()->with('error', 'No eligible staff members found for promotion. Users who are already Admins or Super Admins cannot be promoted again.');
        }

        return back()->with('success', "{$promoted} staff member(s) promoted to Administrator successfully!");
    }
}
