<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param \Throwable $exception
     * @return void
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request * @param  \Throwable  $exception
     * @return JsonResponse|\Illuminate\Http\RedirectResponse
     */

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof \Illuminate\Auth\AuthenticationException) {
            // Check if the request expects JSON
            if ($request->expectsJson()) {
                // Return a JSON response for API or AJAX requests
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }

            // For non-JSON requests, redirect to the login page
            return redirect()->guest(route('login'));
        }

        Log::error('Error: ' . $exception->getMessage());
        Log::error('Error: ' . $exception->getTraceAsString());
        return back()->with(['notification' => "Something went wrong, Try again later!", 'color' => 'danger']);
    }

}
