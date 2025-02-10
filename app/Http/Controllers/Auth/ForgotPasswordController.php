<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\EmailNotification;
use App\Mail\PasswordResetMail;
use App\PasswordHistory;
use App\PasswordPolicy;
use App\Rules\MediumPassword;
use App\Rules\StrongPassword;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function index()
    {
        return view('auth.forgot_password');
    }

    public function showResetForm(Request $request)
    {
        $token = $request->query('token');
        $policy = PasswordPolicy::where('status', 1)->first();
        return view('auth.set_password', compact('token', 'policy'));
    }

    public function sendResetLink(Request $request)
    {
        try {
            Log::info('----FORGOT-PASSWORD----');
            Log::info('Email: ' . $request->email);

            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                $notification = $validator->errors()->first();
                $color = 'danger';
                return redirect()->back()->with(['notification' => $notification, 'color' => $color]);
            }

            // Check if the email exists in the database
            $user = User::select('email', 'status', 'name')->where('email', $request->email)->first();

            // Rate limiting logic
            $cacheKey = 'password_reset_attempts:' . $request->email;
            $attempts = Cache::get($cacheKey, 0);

            if ($attempts >= 2) {
                $notification = 'You have reached the maximum number of password recovery attempts for today. Please try again tomorrow.';
                $color = 'danger';
                return back()->with(['notification' => $notification, 'color' => $color]);
            }

            // Create a password reset token
            $token = Str::random(90);

            // Encrypt the token before storing it
            $encryptedToken = encrypt($token);

            if ($user) {
                //Check status and type user
                if ($user->status == 1) {
                    Log::info('----Inactive User----');
                    Log::info($user->email);
                    $notification = 'Failed to recover your password. Try again later!';
                    $color = 'danger';
                    return back()->with(['notification' => $notification, 'color' => $color]);
                }
                // Store the token in the database
                $success = DB::connection('sqlsrv')
                    ->table('password_resets')
                    ->updateOrInsert(
                        ['email' => trim($request->email)],
                        [
                            'token' => $token,
                            'created_at' => Carbon::now()
                        ]
                    );

                if ($success) {
                    $resetUrl = URL::temporarySignedRoute('set-credentials', now()->addMinutes(30), ['token' => $encryptedToken]);
                    Mail::to($user->email)->queue(new PasswordResetMail($user->name, $resetUrl));
                    Cache::increment($cacheKey);
                    Cache::put($cacheKey, $attempts + 1, now()->addHours(24));
                }
            }
            $notification = 'We have emailed your password reset link!';
            $color = 'success';
            return back()->with(['notification' => $notification, 'color' => $color]);
        } catch (\Exception $e) {
            Log::info('----SEND RECOVER LINK EXCEPTION----');
            Log::error($e->getMessage());
            $notification = 'Failed to recover your password. Try again later!';
            $color = 'danger';
            return back()->with(['notification' => $notification, 'color' => $color]);
        }
    }

    public function updatePassword(Request $request)
    {
        Log::info('----UPDATE-PASSWORD----');
        try {
            //check if the token has expired
            $decryptedToken = decrypt($request->token);
            Log::info("Decrypted Token: " . json_encode($decryptedToken));
            $token = DB::connection('sqlsrv')->table('password_resets')
                ->select('created_at', 'email')
                ->where('token', $decryptedToken)
                ->first();

            if (!$token) {
                $notification = 'Failed to recover password, Try again later!';
                $color = 'danger';
                return back()->with(['notification' => $notification, 'color' => $color]);
            }
            Log::info("Token: " . json_encode($token));

            if ($token->created_at < now()->subMinutes(30)) {
                //link has expired
                $notification = 'Password reset link has expired!, Please request new one';
                $color = 'danger';
                return back()->with(['notification' => $notification, 'color' => $color]);
            }

            return $this->recoverPassword($request->all(), $decryptedToken, $token->email);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Update Password Exception:" . $e->getMessage());
            Log::error("Update Password Exception:" . $e);
            $notification = 'Failed to recover password, Try again later!';
            $color = 'danger';
            return back()->with(['notification' => $notification, 'color' => $color]);
        }
    }

    public function recoverPassword($request, $token = null, $email)
    {
        $policy = PasswordPolicy::query()->where('status', 1)->first();
        if (!$policy) {
            Auth::logout();
            return redirect()->route('login')->withErrors('Password Policy Not Configured, Please Contact Administrator');
        }

        $rules = [
            'password' => ['required', 'min:' . $policy->min_length],
            'confirm_password' => ['required', 'min:' . $policy->min_length, 'same:password']
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

        $validator = Validator::make($request, $rules);

        if ($validator->fails()) {
            $notification = $validator->errors()->first();
            $color = 'danger';
            return redirect('set_credentials')->with(['notification' => $notification, 'color' => $color, 'recover_token' => encrypt($token)]);
        }

        $user = User::where('email', $email)->first();

        if ($this->checkHistory($user, $policy, $request['confirm_password'])) {
            $notification = "You cannot reuse any of your last $policy->password_history passwords.";
            $color = 'danger';
            return redirect('set_credentials')->with(['notification' => $notification, 'color' => $color, 'recover_token' => encrypt($token)]);
        }

        $user->password = Hash::make($request['confirm_password']);

        if ($user->save()) {
            $this->auditLog($user->id, 'Recover Password', 'User Management', 'Successfully Recovered Password');
            $notification = 'Password reset is successfully done';
            $color = 'success';
            $message = $this->message($user);
            Mail::to($user->email)->queue(new EmailNotification($message));
            $this->clearToken($token);
            return redirect('login')->with(['notification' => $notification, 'color' => $color]);
        }

        $notification = 'Password reset failed, Try again later!';
        $color = 'danger';

        return redirect('set_credentials')->with(['notification' => $notification, 'color' => $color, 'recover_token' => encrypt($token)]);
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
            $notification = 'Failed to recover password, Try again later!';
            $color = 'danger';
            return back()->with(['notification' => $notification, 'color' => $color]);
        }
    }

    private function message($user)
    {
        return "Dear **{$user->name}**,  \n\nYou have successfully changed your password. If this is not you, Kindly contact the administrator";
    }

    private function clearToken($decryptedToken): void
    {
        DB::connection('sqlsrv')->table('password_resets')
            ->select('created_at', 'email')
            ->where('token', $decryptedToken)
            ->delete();
    }
}
