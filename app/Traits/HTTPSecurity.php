<?php

namespace App\Traits;

trait HTTPSecurity
{
    public function encryptResponse(string $data, string $password): string
    {
        $iv_len = 12;
        $iv = random_bytes($iv_len);
        $salt_len = 16;
        $salt = random_bytes($salt_len);
        $pw = $password;


        $tag = "";
        $key = hash_pbkdf2('sha256', $pw, $salt, 65536, 32, true);

        $encrypted = openssl_encrypt(
            $data,
            "aes-256-gcm",
            $key,
            $options=OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING,
            $iv,
            $tag,
            "",
            16
        );

        $result = $iv . $salt . $encrypted . $tag;
        return base64_encode($result);
    }

    public function decryptRequest(string $encodedData, string $password): string
    {
        // Decode the base64 encoded data
        $decodedData = base64_decode($encodedData);

        // Extract IV, salt, tag, and ciphertext
        $iv_len = 12;
        $iv = substr($decodedData, 0, $iv_len);
        $salt_len = 16;
        $salt = substr($decodedData, $iv_len, $salt_len);
        $tag_len = 16;
        $ciphertext = substr($decodedData, $iv_len + $salt_len, -16); // Exclude last 16 bytes for tag
        $tag = substr($decodedData, -$tag_len); // Extract last 16 bytes for tag

        $pw = $password;

        // Generate key using PBKDF2
        $key = hash_pbkdf2('sha256', $pw, $salt, 65536, 32, true);

        // Decrypt using AES-256-GCM
        $decrypted = openssl_decrypt(
            $ciphertext,
            "aes-256-gcm",
            $key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag
        );

        return $decrypted;
    }
}
