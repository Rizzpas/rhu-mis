<?php

namespace App\Http\Controllers;

use App\Rules\SecureImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Show the profile settings page.
     */
    public function edit()
    {
        $user = Auth::user();

        return view('profile.settings', compact('user'));
    }

    /**
     * Update user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $isAdminOrSuperAdmin = in_array($user->role, ['admin', 'super_admin']);

        $rules = [
            'name' => [$isAdminOrSuperAdmin ? 'required' : 'nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:Online,Offline,Occupied,online,offline,occupied,in meeting,Present,Unavailable,Seminar'],
            'avatar' => ['nullable', 'file', 'max:2048', new SecureImage],
        ];

        if ($isAdminOrSuperAdmin) {
            $rules['email'] = ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)];
            $rules['schedule'] = ['nullable', 'string', 'max:255'];
            $rules['schedule_payload'] = ['nullable', 'string'];
            if ($user->hasRole('super_admin')) {
                $rules['role'] = ['nullable', 'string', 'in:super_admin,admin'];
            }
        }

        $validated = $request->validate($rules);

        if ($isAdminOrSuperAdmin && !empty($validated['name'])) {
            $user->name = $validated['name'];
        }

        if ($isAdminOrSuperAdmin && !empty($validated['email'])) {
            $user->email = $validated['email'];
        }

        if ($isAdminOrSuperAdmin && $request->has('schedule')) {
            $user->schedule = $validated['schedule'] ?? null;

            if ($request->filled('schedule_payload')) {
                $payload = json_decode($request->input('schedule_payload'), true);
                if (is_array($payload) && count($payload) > 0) {
                    $user->practitionerSchedules()->delete();
                    foreach ($payload as $item) {
                        if (!empty($item['day']) && !empty($item['time_in']) && !empty($item['time_out'])) {
                            \App\Models\PractitionerSchedule::create([
                                'user_id' => $user->id,
                                'day_of_week' => $item['day'],
                                'time_in' => $item['time_in'],
                                'time_out' => $item['time_out'],
                            ]);
                        }
                    }
                } else {
                    $user->practitionerSchedules()->delete();
                }
            } else {
                $user->practitionerSchedules()->delete();
            }
        }

        if ($user->hasRole('super_admin') && !empty($validated['role'])) {
            $user->role = $validated['role'];
        }

        if ($request->filled('status')) {
            $user->status = match(strtolower($validated['status'])) {
                'online', 'present' => 'Online',
                'offline', 'unavailable', 'out of office' => 'Offline',
                'occupied', 'in meeting', 'seminar' => 'Occupied',
                default => 'Offline'
            };
        }

        if ($request->hasFile('avatar')) {
            // Delete old avatar from both disks (migration cleanup)
            if ($user->avatar_path) {
                $cleanPath = ltrim(str_replace(['uploads/', 'storage/'], '', $user->avatar_path), '/');
                Storage::disk('public')->delete($cleanPath);
                Storage::disk('uploads')->delete($cleanPath);
            }
            // Upload new avatar to uploads disk (public/uploads/staff/)
            $path = $request->file('avatar')->store('staff', 'uploads');
            $user->avatar_path = $path;
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Update user's duty status instantly via direct toggle.
     */
    public function updateStatus(Request $request)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:Online,Offline,Occupied,online,offline,occupied,in meeting,Present,Unavailable,Seminar'],
        ]);

        $user = Auth::user();
        $normalizedStatus = match(strtolower($validated['status'])) {
            'online', 'present' => 'Online',
            'offline', 'unavailable', 'out of office' => 'Offline',
            'occupied', 'in meeting', 'seminar' => 'Occupied',
            default => 'Offline'
        };
        $user->status = $normalizedStatus;

        // Set schedule_override to track manual status changes
        // This tells the auto-schedule sync command to respect the user's choice
        if ($normalizedStatus === 'Offline') {
            // Manual offline: stay offline until next schedule slot starts
            $user->schedule_override = 'manual_offline';
            $user->last_activity_at = null;
        } elseif ($normalizedStatus === 'Online' || $normalizedStatus === 'Occupied') {
            // Manual online/occupied: stay active, auto-offline when schedule ends
            $user->schedule_override = 'manual_online';
            $user->last_activity_at = now();
        }

        $user->save();

        return response()->json([
            'success' => true,
            'status' => $user->status,
        ]);
    }


    /**
     * Update user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'string',
                'confirmed',
                \Illuminate\Validation\Rules\Password::min(8)
                    ->numbers(),
            ],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }

    /**
     * Send OTP for email change verification.
     */
    public function sendEmailOtp(Request $request)
    {
        $request->validate(['email' => 'required|email|unique:users,email']);
        $otp = rand(100000, 999999);
        \Illuminate\Support\Facades\Cache::put('email_change_otp_'.auth()->id(), ['email' => $request->email, 'otp' => $otp], now()->addMinutes(10));

        try {
            \Illuminate\Support\Facades\Mail::raw("Your email verification code is: $otp", function ($msg) use ($request) {
                $msg->to($request->email)->subject('Verify your new email address');
            });
        } catch (\Exception $e) {
            // Fallback for demo if mail is not configured
            \Illuminate\Support\Facades\Log::info('FALLBACK: Email OTP for '.$request->email.' is: '.$otp);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Verify the OTP for email change.
     */
    public function verifyEmailOtp(Request $request)
    {
        $request->validate(['otp' => 'required|string', 'email' => 'required|email']);
        $cached = \Illuminate\Support\Facades\Cache::get('email_change_otp_'.auth()->id());

        if (! $cached || $cached['email'] !== $request->email || $cached['otp'] != $request->otp) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired OTP.'], 422);
        }

        session(['email_otp_verified' => $request->email]);
        \Illuminate\Support\Facades\Cache::forget('email_change_otp_'.auth()->id());

        return response()->json(['success' => true]);
    }
}
