<?php

namespace App\Http\Controllers\agency\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SplashController extends Controller
{
    public function splash(Request $request): \Illuminate\Http\JsonResponse
    {
        Log::channel('agency')->info("SPLASH-ENCRYPTED-REQUEST: " . json_encode($request->all()));
        $decryptRequest = json_decode($this->decryptAsymmetric($request->data), true);
        Log::channel('agency')->info("SPLASH-DECRYPTED-REQUEST: " . json_encode($decryptRequest));
		
		if (!isset($decryptRequest['key'])) {
            $response = ['code' => 103, 'error' => true, 'message' => 'Key not found'];
			//$data = $this->encrypt(json_encode($response),'$key');
            //return response()->json(['data' => $data]);
            return response()->json($response);
        }

		$key = trim($decryptRequest['key']);
		
        $validator = Validator::make($decryptRequest, [
            'deviceImei1' => 'required',
            'deviceImei2' => 'required',
        ], [
            'deviceImei1.required' => 'Device imei required',
            'deviceImei2.required' => 'Device imei required'
        ]);

        if ($validator->fails()) {
			Log::channel('agency')->info("SPLASH-VALIDATION-ERROR: " . json_encode($validator->errors()));
            $response = ['code' => 100, 'error' => true, 'message' => $validator->errors()->first()];
            $data = $this->encrypt(json_encode($response),$key);
            return response()->json(['data' => $data]);
        }

        $imei1 = trim($decryptRequest['deviceImei1']);
        $imei2 = trim($decryptRequest['deviceImei2']);

		Log::channel('agency')->info("IMEI1: " . json_encode($imei1));
		Log::channel('agency')->info("IMEI2: " . json_encode($imei2));

        $checkDevice = DB::connection('sqlsrv4')->table('tbl_agency_banking_device')
            ->where('device_imei1', $imei1)
            ->Where('device_imei2', $imei2)
            ->first();

        if (!$checkDevice) {
            $response = ['code' => 101, 'error' => true, 'message' => 'Device not registered'];
            $data = $this->encrypt(json_encode($response),$key);
            return response()->json(['data' => $data]);
        }

        if ($checkDevice->device_status != 1) {
            $response = ['code' => 102, 'error' => true, 'message' => 'Device is inactive'];
            $data = $this->encrypt(json_encode($response),$key);
            return response()->json(['data' => $data]);
        }

        try {
            $storeKey = DB::connection('sqlsrv')->table('tbl_encryption_keys')->upsert(
                [
                    'key_data' => $key,
                    'device_id' => $imei1,
                    'uuid' => Str::uuid()
                ],
                ['device_id'],
                ['key_data']
            );

            if ($storeKey) {
                $response = ['code' => 200, 'error' => false, 'message' => 'successful'];
                $data = $this->encrypt(json_encode($response),$key);
                return response()->json(['data' => $data]);
            }
            $response = ['code' => 103, 'error' => true, 'message' => 'Failed to generate key'];
            $data = $this->encrypt(json_encode($response),$key);
            return response()->json(['data' => $data]);
        } catch (\Exception $e) {
            Log::error("KEY-ENCRYPTION-EXCEPTION: " . json_encode($e->getMessage()));
            $response = ['code' => 103, 'error' => true, 'message' => 'Failed to get splash, something went wrong'];
            $data = $this->encrypt(json_encode($response),$key);
            return response()->json(['data' => $data]);
        }

    }

    private function decryptAsymmetric($encryptedData): string
    {
        $privateKeyPath = Storage::path('keys/private.key');
        $privateKey = openssl_pkey_get_private(file_get_contents($privateKeyPath));
        openssl_private_decrypt(base64_decode($encryptedData), $decryptedData, $privateKey);
        return $decryptedData;
    }
	function opensslPublicEncrypt($data){
		$publicKey = Storage::path('keys/public.key');
		openssl_public_encrypt($data, $encrypted, openssl_pkey_get_public(file_get_contents($publicKey)));
		return base64_encode($encrypted);
	}
}
