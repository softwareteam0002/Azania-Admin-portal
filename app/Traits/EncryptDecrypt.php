<?php

namespace App\Traits;

trait EncryptDecrypt
{
      public function encryptAES($plaintext, $pw)
    {

        $iv_len = 12;
        $iv = openssl_random_pseudo_bytes($iv_len);
        $salt_len = 16;
        $salt = openssl_random_pseudo_bytes($salt_len);
        $tag="";
        // Generate key using PBKDF2
        $key = hash_pbkdf2('sha1', $pw, $salt, 65536, 128, true);

        // Encrypt using aes-128-gcm
        $encrypted = openssl_encrypt(
            $plaintext,
            "aes-128-gcm",
            $key,
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

        return $base64EncodedData;
    }

    public function decryptAES($encodedData, $pw)
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
            $key = hash_pbkdf2('sha1', $pw, $salt, 65536, 128, true);

            // Decrypt using aes-128-gcm
            $decrypted = openssl_decrypt(
                $ciphertext,
                "aes-128-gcm",
                $key,
                OPENSSL_RAW_DATA,
                $iv,
                $tag
            );

            return $decrypted;
        } catch (\Exception $e) {
            Log::error("FAILED-TO-DECRYPT: " . json_encode($e->getMessage()));
            return $e->getMessage();
        }
    }


}
