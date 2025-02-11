<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\PasswordPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class PasswordPolicyController extends Controller
{
    public const PASSWORD_REGEX = 'required|integer|min:1';

    public function index()
    {
        $policies = PasswordPolicy::query()->get();
        return view('admin.settings.password_policy', compact('policies'));
    }

    public function storePasswordPolicy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'min_length' => 'required|integer|min:8',
            'complexity' => 'required|string|in:strong,medium,weak',
            'max_attempts' => self::PASSWORD_REGEX,
            'expiry_period' => self::PASSWORD_REGEX,
            'pass_history' => 'required|integer|min:5',
            'otp_expiry' => self::PASSWORD_REGEX,
            'otp_attempts' => self::PASSWORD_REGEX,
            'otp_length' => 'required|integer|min:4|max:8',
        ]);

        if ($validator->fails()) {
            $notification = $validator->errors()->first();
            $color = 'danger';
            return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
        }

        try {
            $store = PasswordPolicy::create([
                'min_length' => $request->min_length,
                'otp_length' => $request->otp_length,
                'otp_duration' => $request->otp_expiry,
                'complexity' => $request->complexity,
                'password_history' => $request->pass_history,
                'initiator_id' => Auth::user()->id,
                'max_attempts' => $request->max_attempts,
                'otp_attempts' => $request->otp_attempts,
                'expiry_days' => $request->expiry_period,
            ]);

            if ($store) {
                $notification = "Password Policy created Successfully";
                $color = 'success';
            } else {
                $notification = "Failed to created password policy";
                $color = 'danger';
            }
            $this->auditLog(Auth::user()->id, 'Create Password Policy', 'System Settings',
                $notification, $request->ip());
            return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
        } catch (\Exception $e) {
            $notification = "Something went wrong. Please try again later.";
            $color = 'danger';
            $this->auditLog(Auth::user()->id, 'Store Password Policy Exception', 'System Settings',
                'Exception occurred', $request->ip());
            Log::error("Store Password Policy: " . json_encode($e->getMessage()));
            return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
        }
    }
}
