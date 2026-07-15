<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class StaffPasswordResetController extends Controller
{
    /**
     * Show the forgot password form.
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send OTP to the staff/admin email.
     */
    public function sendResetOtp(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);
        
        $email = $request->email;
        $otp = rand(100000, 999999);
        
        Cache::put('password_reset_otp_' . $email, $otp, now()->addMinutes(10));
        
        try {
            Mail::raw("Your password reset code is: $otp. This code will expire in 10 minutes.", function($msg) use ($email) {
                $msg->to($email)->subject('RHU MIS - Password Reset Code');
            });
        } catch (\Exception $e) {
            // Fallback for demo if mail is not configured
            Log::info("FALLBACK: Password Reset OTP for {$email} is: {$otp}");
        }
        
        return response()->json(['success' => true]);
    }

    /**
     * Verify OTP and reset password.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $cachedOtp = Cache::get('password_reset_otp_' . $request->email);
        
        if (!$cachedOtp || $cachedOtp != $request->otp) {
            throw ValidationException::withMessages([
                'otp' => ['The provided OTP is invalid or has expired.'],
            ]);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        Cache::forget('password_reset_otp_' . $request->email);

        return redirect()->route('login')->with('success', 'Password reset successfully. You can now log in.');
    }
}
