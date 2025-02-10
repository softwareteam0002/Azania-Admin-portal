<?php

namespace App\Http\Controllers\agency\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SplashController
{
    public function splash(Request $request): \Illuminate\Http\JsonResponse
    {
        Log::channel('agency')->info("SPLASH-REQUEST: " . json_encode($request->all()));
        $validator = Validator::make($request->all(), [
            'deviceImei1' => 'required',
            'deviceImei2' => 'required',
        ], [
            'deviceImei1.required' => 'Device imei required',
            'deviceImei2.required' => 'Device imei required'
        ]);

        if ($validator->fails()) {
            return response()->json(['code' => 100, 'error' => true, 'message' => $validator->errors()->first()]);
        }
        $imei1 = trim($request->deviceImei1);
        $imei2 = trim($request->deviceImei2);

        $checkDevice = DB::connection('sqlsrv4')->table('tbl_agency_banking_device')
            ->where('device_imei1', $imei1)
            ->Where('device_imei2', $imei2)
            ->first();

        if (!$checkDevice) {
            $response = ['code' => 101, 'error' => true, 'message' => 'Device not registered'];
            return response()->json($response);
        }

        if ($checkDevice->device_status != 1) {
            $response = ['code' => 102, 'error' => true, 'message' => 'Device is inactive'];
            return response()->json($response);
        }

        $key = bin2hex(random_bytes(16));
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
                $response = ['code' => 200, 'error' => false, 'message' => 'successful', 'key' => base64_encode($key)];
                return response()->json($response);
            }
            $response = ['code' => 103, 'error' => true, 'message' => 'Failed to generate key'];
            return response()->json($response);
        } catch (\Exception $e) {
            Log::error("KEY-ENCRYPTION-EXCEPTION: " . json_encode($e->getMessage()));
            $response = ['code' => 103, 'error' => true, 'message' => 'Failed to get splash, something went wrong'];
            return response()->json($response);
        }

    }
}
