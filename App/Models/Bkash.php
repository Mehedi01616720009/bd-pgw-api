<?php

namespace App\Models;

use App\Constants\Table;
use Core\Database\DB;
use Core\Support\Config;
use Core\Support\CONSTANT;
use Exception;

class Bkash
{
    /**
     * grant token
     */
    public static function grantToken(): string
    {
        $now = date('Y-m-d H:i:s', time());

        // get token
        $Token = DB::table(Table::PAYMENT_TOKEN)
            ->select('id', 'token', 'refreshToken', 'expiredAt')
            ->where('id', 1)
            ->where('expiredAt', '>', $now)
            ->first();
        if ($Token) {
            return $Token->token;
        }

        $url = Config::get(CONSTANT::BKASH_BASE_URL) . "/" . Config::get(CONSTANT::BKASH_VERSION) . "/tokenized/checkout/token/grant";

        $headers = [
            "Content-Type: application/json",
            "Accept: application/json",
            "username: " . Config::get(CONSTANT::BKASH_USERNAME),
            "password: " . Config::get(CONSTANT::BKASH_PASSWORD)
        ];

        $body = json_encode([
            "app_key" => Config::get(CONSTANT::BKASH_APP_KEY),
            "app_secret" => Config::get(CONSTANT::BKASH_APP_SECRET)
        ]);

        $response = Helper::PostRequest($url, $headers, $body, [
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        ]);

        $result = json_decode($response, true);

        // echo "<pre>";
        // print_r($result);
        // echo "</pre>";
        // exit();

        if (!isset($result['id_token'])) {
            throw new Exception('Failed to get token', 400);
        }

        $id_token     = $result['id_token'];
        $refreshToken = $result['refresh_token'] ?? null;
        $expiredAt    = date('Y-m-d H:i:s', time() + 3000);

        $UpsertQuery = "INSERT INTO PaymentTokens 
        (id, token, refreshToken, expiredAt, createdAt, updatedAt) 
        VALUES (?, ?, ?, ?, ?, ?) 
        ON DUPLICATE KEY UPDATE 
        token = VALUES(token), 
        refreshToken = VALUES(refreshToken), 
        expiredAt = VALUES(expiredAt), 
        updatedAt = VALUES(updatedAt)";
        $UpsertData = [1, $id_token, $refreshToken, $expiredAt, $now, $now];

        DB::table(Table::PAYMENT_TOKEN)->raw($UpsertQuery, $UpsertData);

        return $id_token;
    }

    /**
     * refresh token using refresh token
     */
    public static function refreshToken(): string
    {
        $now = date('Y-m-d H:i:s', time());

        // get refresh token from database
        $Token = DB::table(Table::PAYMENT_TOKEN)
            ->select('id', 'refreshToken')
            ->where('id', 1)
            ->first();

        if (!$Token || empty($Token->refreshToken)) {
            return self::grantToken();
        }

        $url = Config::get(CONSTANT::BKASH_BASE_URL) . "/" . Config::get(CONSTANT::BKASH_VERSION) . "/tokenized/checkout/token/refresh";

        $headers = [
            "Content-Type: application/json",
            "Accept: application/json",
            "username: " . Config::get(CONSTANT::BKASH_USERNAME),
            "password: " . Config::get(CONSTANT::BKASH_PASSWORD)
        ];

        $body = json_encode([
            "app_key" => Config::get(CONSTANT::BKASH_APP_KEY),
            "app_secret" => Config::get(CONSTANT::BKASH_APP_SECRET),
            "refresh_token" => $Token->refreshToken
        ]);

        $response = Helper::PostRequest($url, $headers, $body, [
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        ]);

        $result = json_decode($response, true);

        if (!isset($result['id_token'])) {
            return self::grantToken();
        }

        $id_token     = $result['id_token'];
        $newRefreshToken = $result['refresh_token'] ?? $Token->refreshToken;
        $expiredAt    = date('Y-m-d H:i:s', time() + 3000);

        $UpsertQuery = "INSERT INTO PaymentTokens 
        (id, token, refreshToken, expiredAt, createdAt, updatedAt) 
        VALUES (?, ?, ?, ?, ?, ?) 
        ON DUPLICATE KEY UPDATE 
        token = VALUES(token), 
        refreshToken = VALUES(refreshToken), 
        expiredAt = VALUES(expiredAt), 
        updatedAt = VALUES(updatedAt)";
        $UpsertData = [1, $id_token, $newRefreshToken, $expiredAt, $now, $now];

        DB::table(Table::PAYMENT_TOKEN)->raw($UpsertQuery, $UpsertData);

        return $id_token;
    }

    /**
     * handle API call with automatic retry on authorization failure
     */
    private static function callWithRetry(callable $apiCall): array
    {
        $result = $apiCall();

        if (isset($result['statusCode']) && $result['statusCode'] === '9999') {
            self::refreshToken();
            $result = $apiCall();
        }

        return $result;
    }

    /**
     * create payment
     */
    public static function createPayment(array $payload): array
    {
        return self::callWithRetry(function () use ($payload) {
            $token = self::grantToken();

            $url = Config::get(CONSTANT::BKASH_BASE_URL) . "/" . Config::get(CONSTANT::BKASH_VERSION) . "/tokenized/checkout/create";

            $headers = [
                "Content-Type: application/json",
                "Accept: application/json",
                "Authorization: " . $token,
                "X-APP-Key: " . Config::get(CONSTANT::BKASH_APP_KEY)
            ];

            $body = json_encode([
                "mode" => "0011",
                "payerReference" => $payload['phone'],
                "callbackURL" => $payload['callbackUrl'],
                "amount" => $payload['amount'],
                "currency" => "BDT",
                "intent" => "sale",
                "merchantInvoiceNumber" => $payload['invoice']
            ]);

            $response = Helper::PostRequest($url, $headers, $body, [
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);

            $result = json_decode($response, true);

            if (isset($result['statusCode']) && $result['statusCode'] !== '0000') {
                throw new Exception('Failed create payment', 400);
            }

            return $result;
        });
    }

    /**
     * execute payment
     */
    public static function executePayment(string $paymentId): array
    {
        return self::callWithRetry(function () use ($paymentId) {
            $token = self::grantToken();

            $url = Config::get(CONSTANT::BKASH_BASE_URL) . "/" . Config::get(CONSTANT::BKASH_VERSION) . "/tokenized/checkout/execute";

            $headers = [
                "Content-Type: application/json",
                "Accept: application/json",
                "Authorization: " . $token,
                "X-APP-Key: " . Config::get(CONSTANT::BKASH_APP_KEY)
            ];

            $body = json_encode([
                "paymentID" => $paymentId,
            ]);

            $response = Helper::PostRequest($url, $headers, $body, [
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);

            $result = json_decode($response, true);

            if (isset($result['statusCode']) && $result['statusCode'] !== '0000') {
                throw new Exception('Failed execute payment', 400);
            }

            return $result;
        });
    }
}
