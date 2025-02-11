<?php

namespace App\Helper;

use App\AuditTrailLogs;
use App\PasswordPolicy;
use Illuminate\Support\Carbon;
use Random\RandomException;

class Helper
{
    public function auditTrack($request, $notification, $color)
    {
        try {
            $db_action = AuditTrailLogs::insert([
                'user_id' => $request->user_id,
                'module' => $request->module,
                'action' => $request->action,
                'action_time' => $request->action_time,
                'reason' => $request->reasson,
                'old_details' => $request->old_details,
                'new_details' => $request->new_details
            ]);

            if ($db_action) {
                return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
            }

        } catch (\Exception $e) {
            return redirect()->back()->with(['notification' => $e->getMessage(), 'color' => $color]);
        }

    }


    public function auditTrail($action, $module, $notification, $url, $user_id): \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        //Audit trail details
        $request['user_id'] = $user_id;
        $request['module'] = $module;
        $request['action'] = $action;
        $request['action_time'] = Carbon::now()->setTimezone('Africa/Nairobi');
        $request['reason'] = "NULL";

        return $this->changeTracker($request, $notification, $url);

    }

    public function changeTracker($request, $notification, $end_point): \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        try {

            AuditTrailLogs::insert([
                'user_id' => $request['user_id'],
                'module' => $request['module'],
                'action' => $request['action'],
                'action_time' => $request['action_time'],
                'reason' => $request['reason']
            ]);


            return redirect($end_point)->with(['notification' => $notification, 'color' => "success"]);


        } catch (\Exception $e) {
            return redirect()->back()->with(['notification' => $e->getMessage(), 'color' => "danger"]);
        }
    }

    /**
     * @throws RandomException
     */
    public static function randomNumberGenerator($length): int
    {
        if (env('APP_ENV') === 'production') {
            $min = 10 ** ($length - 1);
            $max = (10 ** $length) - 1;

            return random_int($min, $max);
        }
        return 123456;
    }

    public static function maskEmail($email): string
    {
        // Split the email into username and domain parts
        [$username, $domain] = explode('@', $email);

        // Mask the username except for the first character
        $maskedUsername = $username[0] . str_repeat('*', strlen($username) - 1);

        // Construct the masked email
        return $maskedUsername . '@' . $domain;
    }

    /**
     * @throws RandomException
     * @throws RandomException
     */
    public static function generatePassword(): string|\Illuminate\Http\RedirectResponse
    {
        $policy = PasswordPolicy::query()->where('status', 1)->first();
        if (!$policy) {
            $notification = 'Password Policy not set. Please contact administrator.';
            $color = 'danger';
            return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
        }
        // Define the characters that can be used in the password
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        $specialChars = '!@#$%&?';

        // Ensure the password is strong by including at least one character from each category
        $password = '';
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $specialChars[random_int(0, strlen($specialChars) - 1)];

        // Fill up the remaining characters with random selections from all categories
        $allCharacters = $uppercase . $lowercase . $numbers . $specialChars;
        for ($i = strlen($password); $i < $policy->min_length; $i++) {
            $password .= $allCharacters[random_int(0, strlen($allCharacters) - 1)];
        }

        // Shuffle the password to ensure randomness
        return str_shuffle($password);
    }

}
