<?php

namespace App\Models;

use Exception;

class Helper
{
    /**
     * post request
     */
    public static function PostRequest(string $url, array $headers, string $body, array $options = [])
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        if (!empty($options)) {
            curl_setopt_array($ch, $options);
        }

        $response = curl_exec($ch);
        return $response;
    }

    /**
     * get request
     */
    public static function GetRequest($url)
    {
        $ch = curl_init();
        $timeout = 10;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/0 (Windows; U; Windows NT 0; zh-CN; rv:3)");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        return $response;
    }

    /**
     * get ip address
     */
    public static function getIpAddress(): string
    {
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        } else {
            $ipaddress = 'UNKNOWN IP';
        }

        return $ipaddress;
    }

    /**
     * encrypt data with public key
     */
    public static function encryptDataWithPublicKey(string $data, string $pgPublicKey)
    {
        $publicKey = "-----BEGIN PUBLIC KEY-----\n" . $pgPublicKey . "\n-----END PUBLIC KEY-----";
        $keyResource = openssl_get_publickey($publicKey);
        $status = openssl_public_encrypt($data, $cryptoText, $keyResource);
        if (!$status) {
            throw new Exception("Invalid Public key. Check Public Key in Configuration");
        }

        return base64_encode($cryptoText);
    }

    /**
     * signature generate
     */
    public static function signatureGenerate(string $data, string $merchantPrivateKey)
    {
        $privateKey = "-----BEGIN RSA PRIVATE KEY-----\n" . $merchantPrivateKey . "\n-----END RSA PRIVATE KEY-----";
        $status = openssl_sign($data, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        if (!$status) {
            throw new Exception("Invalid Private key. Check Private Key in Configuration");
        }

        return base64_encode($signature);
    }

    /**
     * decrypt data with private key
     */
    public static function decryptDataWithPrivateKey(string $cryptoText, string $merchantPrivateKey)
    {
        $private_key = "-----BEGIN RSA PRIVATE KEY-----\n" . $merchantPrivateKey . "\n-----END RSA PRIVATE KEY-----";
        openssl_private_decrypt(base64_decode($cryptoText), $plain_text, $private_key);
        return $plain_text;
    }

    /**
     * nagad exception
     */
    public static function NagadException($response)
    {
        $errorMap = [
            "16_0006_004" => "Provided merchant ID is invalid",
            "16_0006_052" => "Invalid Merchant",
            "16_0006_053" => "Inactive Merchant",
            "16_0006_056" => "Encryption failed",
            "16_0006_057" => "Decryption failed",
            "16_0006_058" => "Failed to verify signature",
            "16_0006_059" => "Invalid Sensitive Data",
            "16_0006_060" => "Error processing sensitive data",
            "16_0006_061" => "Invalid merchant key",
            "16_0006_064" => "Mandatory Header Missing",
            "16_0006_068" => "Invalid Order Id",
            "16_0006_075" => "Could not persist data to storage",
            "16_0006_076" => "Transaction Date Time Not in allowed window",
            "16_0006_080" => "Invalid Currency Code",
            "16_0006_081" => "Invalid Date Time Format",
            "16_0006_083" => "Duplicate Order ID in same day",
            "16_0006_999" => "Invalid Request",
            "16_0006_017" => "Purchase information state is invalid",
            "16_0006_040" => "Invalid encrypted request type",
            "16_0006_050" => "Provided merchant ID is invalid",
            "16_0006_055" => "Invalid Payment Reference Id",
            "16_0006_069" => "Data not encoded",
        ];

        if (isset($response['reason']) && array_key_exists($response['reason'], $errorMap)) {
            $message = $errorMap[$response['reason']];
            $status = $response['reason'];
            throw new Exception("[$status] $message");
        }
    }
}
