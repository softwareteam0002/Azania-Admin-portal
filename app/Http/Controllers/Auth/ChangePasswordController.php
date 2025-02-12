<?php

namespace App\Http\Controllers\Auth;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Mail\EmailNotification;
use App\PasswordHistory;
use App\PasswordPolicy;
use App\Rules\MediumPassword;
use App\Rules\StrongPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ChangePasswordController extends Controller
{
    public const ACTIVE = 1;

    public function index()
    {
        $policy = PasswordPolicy::query()->where('status', self::ACTIVE)->first();

        if (!$policy) {
            Auth::logout();
            return redirect()->route('login')->withErrors('Password Policy Not Configured, Please Contact Administrator');
        }

        return view('auth.change_password', compact('policy'));
    }

    public function updatePassword(Request $request)
    {
        try {
            $request->merge([
                'password' => trim($request->input('password')),
                'confirm_password' => trim($request->input('confirm_password')),
            ]);

            if ($request->password !== $request->confirm_password) {
                $notification = "Passwords don't match!";
                $color = 'danger';
                return back()->with(['notification' => $notification, 'color' => $color]);
            }

            $policy = PasswordPolicy::query()->where('status', 1)->first();
            if (!$policy) {
                Auth::logout();
                return redirect()->route('login')->withErrors('Password Policy Not Configured, Please Contact Administrator');
            }

            $rules = [
                'confirm_password' => ['required', 'min:' . $policy->min_length],
                'password' => ['required', 'min:' . $policy->min_length]
            ];

            switch ($policy->complexity) {
                case 'strong':
                    $rules['password'][] = new StrongPassword();
                    $rules['confirm_password'][] = new StrongPassword();
                    break;
                case 'medium':
                    $rules['password'][] = new MediumPassword();
                    $rules['confirm_password'][] = new MediumPassword();
                    break;
                default:
                    break;
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $notification = $validator->errors()->first();
                $color = 'danger';
                return back()->with(['notification' => $notification, 'color' => $color]);
            }

            $user = Auth::user();

            if ($this->checkHistory($user, $policy, $request->confirm_password)) {
                $notification = "You cannot reuse any of your last $policy->password_history passwords.";
                $color = 'danger';
                return back()->with(['notification' => $notification, 'color' => $color]);
            }
            $user->password = Hash::make($request->confirm_password);
            $user->first_login = 0;
            $user->save();

            $this->auditLog($user->id, 'Change Password', 'User Management', 'Successfully Changed Password', $request->ip());
            $this->sendNotification($user, $policy);
            return redirect('/two_factor_authentication');
        } catch (\Exception $e) {
            Log::error("Update Password Exception: ", ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
            $notification = "Something went wrong while processing your request.";
            $color = 'danger';
            return back()->with(['notification' => $notification, 'color' => $color]);
        }
    }

    protected function sendNotification($user, $policy): void
    {
        try {
            $otp = Helper::randomNumberGenerator($policy->otp_length);

            Cache::put("otp_$user->id", $otp, now()->addMinutes($policy->otp_duration));

            $name = $user->name;
            $maskedEmail = Helper::maskEmail($user->email);
            $message = "Dear **{$name}**,  \n\nYour Login OTP is **{$otp}**. This OTP will be valid for $policy->otp_duration minutes.";

            session()->put('message', "Login OTP has been sent to $maskedEmail");
            session()->put('color', "success");

            // Queue the email notification
            Mail::to($user->email)->queue(new EmailNotification($message));
        } catch (\Exception $e) {
            Log::error('Send Notification Error: ', ['message' => $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
        }
    }

    private function checkHistory($user, $policy, $newPassword)
    {
        try {
            // Check if the new password is in the last 10 passwords
            $passwordHistory = PasswordHistory::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->limit($policy->password_history)
                ->get();

            foreach ($passwordHistory as $history) {
                if (Hash::check($newPassword, $history->password)) {
                    return true;
                }
            }
            // Store the new password in the history
            PasswordHistory::create([
                'user_id' => $user->id,
                'password' => Hash::make($newPassword),
            ]);
            return false;
        } catch (\Exception $exception) {
            Log::error("Change password Exception: " . json_encode($exception->getMessage()));
            $notification = "Something went wrong";
            $color = 'danger';
            return back()->with(['notification' => $notification, 'color' => $color]);
        }
    }
}
