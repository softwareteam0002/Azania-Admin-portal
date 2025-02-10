<?php

namespace App\Http\Controllers;

use App\AuditTrailLogs;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    function encrypt($data, $key)
    {
        $iv_len = 12;
        $iv = openssl_random_pseudo_bytes($iv_len);
        $salt_len = 16;
        $salt = openssl_random_pseudo_bytes($salt_len);
        $tag = "";

        // Generate key using PBKDF2
        $keyGenerated = hash_pbkdf2('sha1', $key, $salt, 10000, 128, true);

        // Encrypt using aes-128-gcm
        $encrypted = openssl_encrypt(
            $data,
            "aes-128-gcm",
            $keyGenerated,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            "",
            16
        );

        // Combine IV, salt, tag, and ciphertext
        $encodedData = $iv . $salt . $encrypted . $tag;

        // Base64 encode the combined data
        $base64EncodedData = base64_encode($encodedData);
        $base64EncodedData = str_replace("\\\\", "", $base64EncodedData);
        /* Log::error("ENC: " . $base64EncodedData);
          Log::error("KEY2: " . $key);
        $result = $this->decrypt($base64EncodedData,$key);
           Log::error("Result: " . $result);*/
        return $base64EncodedData;
    }


    function decrypt($encodedData, $pw)
    {
        try {
            $decodedData = base64_decode($encodedData);

            // Extract IV, salt, tag, and ciphertext
            $iv_len = 12;
            $iv = substr($decodedData, 0, $iv_len);
            $salt_len = 16;
            $salt = substr($decodedData, $iv_len, $salt_len);
            $tag_len = 16;
            $ciphertext = substr($decodedData, $iv_len + $salt_len, -16); // Exclude last 16 bytes for tag
            $tag = substr($decodedData, -$tag_len); // Extract last 16 bytes for tag

            // Generate key using PBKDF2
            $key = hash_pbkdf2('sha1', $pw, $salt, 10000, 128, true);

            // Decrypt using aes-128-gcm
            $decrypted = openssl_decrypt(
                $ciphertext,
                "aes-128-gcm",
                $key,
                OPENSSL_RAW_DATA,
                $iv,
                $tag
            );

            if ($decrypted === false) {
                Log::error("FAILED-TO-DECRYPT: " . openssl_error_string());

            }

            return $decrypted;
        } catch (\Exception $e) {
            Log::error("FAILED-TO-DECRYPT: " . json_encode($e->getMessage()));
            return $e->getMessage();
        }
    }


    public function getDeviceKey($imei1, $imei2)
    {
        $key = DB::connection('sqlsrv')->table('tbl_encryption_keys')
            ->whereIn('device_id', [$imei1, $imei2])
            ->first();

        return trim($key->key_data);
    }

    public function signPayload($data, $key)
    {
        $signedData = $this->encrypt($data, $key);
        return response()->json(["data" => $signedData]);
    }

    public function validateRequest($data)
    {
        $validator = Validator::make($data, [
            'type' => 'sometimes|regex:/^[a-zA-Z\s]+$/|max:20',
            'amount' => 'sometimes|regex:/^\d+(\.\d{1,2})?$/',
        ]);
    }

    public function auditLog($user, $action, $module, $reason = null, $source = null): void
    {
        try {
            AuditTrailLogs::create(
                [
                    'user_id' => $user,
                    'action' => $action,
                    'module' => $module,
                    'action_time' => now(),
                    'reason' => $reason,
                    'source' => $source,
                ]
            );
        } catch (\Exception $e) {
            Log::error("AUDIT-TRAIL-EXCEPTION: " . json_encode($e->getMessage()));
        }

    }

}
