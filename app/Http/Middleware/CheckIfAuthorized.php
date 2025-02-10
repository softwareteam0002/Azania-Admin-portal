<?php

namespace App\Http\Middleware;

use App\Traits\HTTPSecurity;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckIfAuthorized
{
    use HTTPSecurity;
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse) $next
     * @return \Illuminate\Http\JsonResponse
     */
     public function handle(Request $request, Closure $next)
    {
		//dd($request->data,env('encryptResponseION_PASSWORD'));
		$decryptedRequest = $this->decryptRequest($request->data, env('ENCRYPTION_PASSWORD'));
		
		//$decryptedRequest = $this->encryptResponse(json_encode($request->all()), env('ENCRYPTION_PASSWORD'));dd($decryptedRequest);
		$decryptedRequest = json_decode($decryptedRequest, true); 
		if ($request->hasHeader('Authorization')) {
			$authorizationKey = $request->header('Authorization');
			if ($authorizationKey == env('API_KEY')) {
				return $next($request);
			} else {
				Log::info("API-AUTHORIZATION-FAILED: ", ['PAYLOAD' => $decryptedRequest, 'IP-ADDRESS' => $request->ip()]);
				$encryptResponseedData = $this->encryptResponse(json_encode(['responseCode' => 403, 'responseMessage' => 'Unauthorized API Request']), env('ENCRYPTION_PASSWORD'));
				return response()->json(['data' => $encryptResponseedData]);
			}
		}
		Log::info("API-AUTHORIZATION-FAILED: ", ['PAYLOAD' => $decryptedRequest, 'IP-ADDRESS' => $request->ip()]);
		$encryptResponseedData = $this->encryptResponse(json_encode(['responseCode' => 403, 'responseMessage' => 'Unauthorized API Request']), env('ENCRYPTION_PASSWORD'));
		return response()->json(['data' => $encryptResponseedData]);
    }
}
