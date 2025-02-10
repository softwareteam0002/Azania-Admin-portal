<?php

namespace App\Http\Controllers\Auth;

use App\AdminUser;
use App\AuditTrailLogs;
use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Mail\EmailNotification;
use App\PasswordHistory;
use App\PasswordPolicy;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/two_factor_authentication';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function reloadCaptcha()
    {
        return response()->json(['captcha' => captcha_img()]);
    }

    /**
     * Handle a login request to the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        $policy = $this->checkPasswordPolicy();
        $user = $this->checkUser($request->email);

        // Check if too many login attempts
        if ($this->hasTooManyLoginAttempts($user->id, $policy)) {
            $user->status = 1;
            $user->save();

            return redirect()->back()
                ->withErrors([
                    'error' => 'Maximum login attempts exceeded. Account is suspended.',
                ]);
        }

        if ($user->status != 0) {
            return redirect()->back()
                ->withErrors([
                    'error' => 'Account is Inactive. Please contact administrator.',
                ]);
        }

        // If login attempt is successful
        if ($this->attemptLogin($request)) {
            if ($request->hasSession()) {
                $request->session()->put('auth.password_confirmed_at', time());
            }

            $user = Auth::user();
            $this->checkLastUpdatedPassword($user);
            $user->is_verified = 0;
            $user->save();

            //check for first login
            if ($user->first_login || is_null($user->first_login)) {
                return redirect()->route('change_password');
            }

            $this->auditLog($user->id, 'Login', 'User Management', 'Login Successfully', $request->ip());
            $this->clearLoginAttempts($user->id);
            //send otp for two factor
            $this->sendNotification();
            return $this->sendLoginResponse($request);
        }

        // Increment login attempts and return back with a generalized error message
        $this->incrementLoginAttempts($user->id);

        return redirect()->back()
            ->withErrors([
                'error' => 'Incorrect email, password, or captcha. Please try again.',
            ]);
    }

    protected function incrementLoginAttempts($id)
    {
        $key = 'attempt_by_' . $id;
        // Retrieve the current attempts, defaulting to 0 if the key doesn't exist
        $attempts = Cache::get($key, 0);

        // Increment the attempts
        $attempts++;
        // Store the updated attempts with no expiration
        Cache::forever($key, $attempts);
    }

    protected function checkPasswordPolicy()
    {
        $policy = PasswordPolicy::where('status', 1)->first();

        if (!$policy) {
            return redirect()->back()
                ->withErrors([
                    'error' => 'Password policy not set. Please contact administrator.',
                ]);
        }

        return $policy;
    }

    protected function checkUser($email)
    {
        $user = AdminUser::where('email', $email)->first();
        if (!$user) {
            return redirect()->back()
                ->withErrors([
                    'error' => 'Incorrect email, password, or captcha. Please try again.',
                ]);
        }
        return $user;
    }

    protected function hasTooManyLoginAttempts($id, $policy)
    {
        $key = 'attempt_by_' . $id;

        // Retrieve the current attempts, defaulting to 0 if the key doesn't exist
        $attempts = Cache::get($key, 0);
        // Check if the attempts exceed the allowed maximum
        return $attempts >= $policy->max_attempts;
    }

    protected function clearLoginAttempts($id)
    {
        $key = 'attempt_by_' . $id;

        // Clear the login attempts for the given ID
        Cache::forget($key);
    }

    /**
     * Validate the user login request.
     *
     * @param \Illuminate\Http\Request $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'captcha' => 'required|captcha',
        ], ['captcha.captcha' => 'Incorrect Captcha. Please try again!']);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request), $request->boolean('remember')
        );
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        if ($response = $this->authenticated($request, $this->guard()->user())) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect()->intended($this->redirectPath());
    }

    /**
     * Log the user out of the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('/');
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }

    protected function sendNotification()
    {
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

        session()->put('message', "Login OTP has been sent to $maskedEmail");
        session()->put('color', "success");

        // Queue the email notification
        Mail::to($email)->queue(new EmailNotification($message));
        AuditTrailLogs::create(
            [
                'user_id' => auth()->user()->id,
                'action' => 'OTP',
                'module' => 'User Management',
                'action_time' => now(),
                'reason' => "OTP sent to $email",
            ]
        );
    }

    protected function cacheOtp($userId, $otp): void
    {
        Cache::put("otp_$userId", $otp, now()->addMinutes(5));
    }

    protected function checkLastUpdatedPassword($user)
    {
        $history = PasswordHistory::where('user_id', $user->id)->latest()->first();
        $policy = PasswordPolicy::where('status', 1)->first();
        if ($history) {
            // Check if the last password update was more than 90 days ago
            $passwordAge = Carbon::parse($history->created_at);
            $passwordAgeInDays = $passwordAge->diffInDays(Carbon::now());

            if ($passwordAgeInDays >= $policy->expiry_days) {
                $notification = "Your password is older than $policy->expiry_days days. Please change your password.";
                $color = 'warning';
                return redirect()->route('change_password')->with(['notification' => $notification, 'color' => $color]);
            }
        }

    }
}
