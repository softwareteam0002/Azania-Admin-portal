<?php

namespace App\Http\Controllers\Auth;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Mail\EmailNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;


class TwoFactorAuth extends Controller
{
    public function index()
    {
        return view('auth.two-factor-auth');
    }

    public function verifyOtp(Request $request)
    {
        // Validate the input OTP
        $validate = Validator::make($request->all(), [
            'otp' => 'required|array|size:6',
            'otp.*' => 'numeric|digits:1',
        ]);

        // Return error if validation fails
        if ($validate->fails()) {
            return redirect()->back()->with(['notification' => $validate->errors()->first(), 'color' => 'danger']);
        }

        // Get the authenticated user's ID and cache key for OTP
        $userId = auth()->user()->id;
        $otpKey = "otp_$userId";

        // Retrieve the OTP from the cache
        $cachedOtp = cache()->get($otpKey);

        // If OTP is not found in the cache, notify that it has expired
        if (!$cachedOtp) {
            return redirect()->back()->with(['notification' => 'OTP has expired. Please request a new OTP.', 'color' => 'danger']);
        }

        // Convert the OTP array to a string for comparison
        $otpString = implode('', $request->input('otp'));

        // If OTP doesn't match
        if ($cachedOtp != $otpString) {
            return redirect()->back()->with(['notification' => 'Invalid OTP. Please try again.', 'color' => 'danger']);
        }

        // OTP is valid - handle successful verification
        cache()->forget($otpKey); // Remove OTP from the cache

        try {
            // Mark the user as verified
            $user = auth()->user();
            $user->is_verified = 1; // Set is_verified to true
            $user->save();
        } catch (\Exception $exception) {
            Log::error("VERIFY-OTP-EXCEPTION: " . json_encode($exception->getMessage()));
        }

        // Redirect the user to the home page or any other route
        return redirect()->route('home')->with(['notification' => 'OTP verified successfully!', 'color' => 'success']);
    }

    public function resendOtp(Request $request)
    {
        // Define the rate limit key based on the user ID
        $key = 'resend_otp:' . auth()->user()->id;

        // Check the rate limit
        if (RateLimiter::tooManyAttempts($key, 3)) {
            // Calculate remaining time until the user can try again
            $seconds = RateLimiter::availableIn($key);
            $minutes = floor($seconds / 60);
            $remainingSeconds = $seconds % 60;

            // Create a message with remaining time
            return back()->with([
                'notification' => "Too many OTP requests. Please wait {$minutes} minute(s) and {$remainingSeconds} second(s) before trying again.",
                'color' => 'danger'
            ]);
        }

        // Increment the attempt count
        RateLimiter::hit($key, 300);
        // Generate a random 6-digit OTP
        $otp = Helper::randomNumberGenerator(6);

        // Cache the OTP for 5 minutes
        $this->cacheOtp(auth()->user()->id, $otp);

        // Get the authenticated user's name and email
        $name = auth()->user()->name;
        $email = auth()->user()->email;
        $maskedEmail = Helper::maskEmail($email);
        // Compose the email message
        $message = "Dear **{$name}**,  \n\nYour Login OTP is **{$otp}**. This OTP will be valid for 5 minutes.";

        // Queue the email notification
        Mail::to($email)->queue(new EmailNotification($message));
        return back()->with(['notification' => 'OTP resent successfully!', 'color' => 'success']);
    }

    protected function cacheOtp($userId, $otp): void
    {
        cache()->put("otp_$userId", $otp, now()->addMinutes(5));
    }


}
