<?php

namespace App\Http\Controllers;

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

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:online,offline,in meeting,Present,Unavailable,Seminar'],
            'avatar' => ['nullable', 'image', 'max:2048'], // Max 2MB
        ]);

        $user->name = $validated['name'];

        if ($request->filled('status')) {
            $user->status = $validated['status'];
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
            'status' => ['required', 'string', 'in:online,offline,in meeting,Present,Unavailable,Seminar'],
        ]);

        $user = Auth::user();
        $user->status = $validated['status'];

        if ($validated['status'] === 'offline') {
            $user->last_activity_at = null;
        } else {
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
