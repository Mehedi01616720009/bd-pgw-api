<?php

namespace App\Models;

use Core\Support\Config;
use Core\Support\CONSTANT;
use Exception;

class Nagad
{
    /**
     * initialize payment
     */
    public static function initialize(string $invoice): array
    {
        $SensitiveData = [
            'merchantId' => Config::get(CONSTANT::NAGAD_MERCHANTID),
            'datetime' => date('YmdHis', time()),
            'orderId' => $invoice,
            'challenge' => date('ymdHis', time()),
        ];

        $url = Config::get(CONSTANT::NAGAD_BASE_URL) . "/check-out/initialize/" . Config::get(CONSTANT::NAGAD_MERCHANTID) . '/' . $invoice;

        $headers = [
            "Content-Type: application/json",
            "X-KM-Api-Version:v-0.2.0",
            "X-KM-IP-V4:" . Helper::getIpAddress(),
            "X-KM-Client-Type:PC_WEB"
        ];

        $body = json_encode([
            'accountNumber' => Config::get(CONSTANT::NAGAD_ACCOUNT),
            'dateTime' => date('YmdHis', time()),
            'sensitiveData' => Helper::encryptDataWithPublicKey(json_encode($SensitiveData), Config::get(CONSTANT::NAGAD_MERCHANT_PG_PUBLIC_KEY)),
            'signature' => Helper::signatureGenerate(json_encode($SensitiveData), Config::get(CONSTANT::NAGAD_MERCHANT_PRIVATE_KEY)),
        ]);

        $response = Helper::PostRequest($url, $headers, $body, [
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $result = json_decode($response, true);
        Helper::NagadException($result);

        return $result;
    }

    /**
     * create payment
     */
    public static function createPayment(array $payload): array
    {
        $initialize = self::initialize($payload['invoice']);
        if (!isset($initialize['sensitiveData'])) {
            throw new Exception("Initialization Failed!");
        }

        $initializedResult = json_decode(Helper::decryptDataWithPrivateKey($initialize['sensitiveData'], Config::get(CONSTANT::NAGAD_MERCHANT_PRIVATE_KEY)), true);
        if (!isset($initializedResult['paymentReferenceId'], $initializedResult['challenge'])) {
            throw new Exception("Initialization Failed, Decryption failed");
        }

        $paymentReferenceId = $initializedResult['paymentReferenceId'];
        $challenge = $initializedResult['challenge'];

        $SensitiveData = [
            'merchantId' => Config::get(CONSTANT::NAGAD_MERCHANTID),
            'orderId' => $payload['invoice'],
            'currencyCode' => '050',
            'amount' => $payload['amount'],
            'challenge' => $challenge,
        ];

        $AdditionalMerchantInf = [
            'serviceName' => Config::get(CONSTANT::APP_NAME),
            'serviceLogoURL' => Config::get(CONSTANT::APP_LOGO),
            'additionalFieldNameEN' => 'Type',
            'additionalFieldNameBN' => 'টাইপ',
            'additionalFieldValue' => 'Payment',
        ];

        $url = Config::get(CONSTANT::NAGAD_BASE_URL) . "/check-out/complete/" . $paymentReferenceId;

        $headers = [
            "Content-Type: application/json",
            "X-KM-Api-Version:v-0.2.0",
            "X-KM-IP-V4:" . Helper::getIpAddress(),
            "X-KM-Client-Type:PC_WEB"
        ];

        $body = json_encode([
            'merchantCallbackURL' => $payload['callbackUrl'],
            'additionalMerchantInf' => $AdditionalMerchantInf,
            'sensitiveData' => Helper::encryptDataWithPublicKey(json_encode($SensitiveData), Config::get(CONSTANT::NAGAD_MERCHANT_PG_PUBLIC_KEY)),
            'signature' => Helper::signatureGenerate(json_encode($SensitiveData), Config::get(CONSTANT::NAGAD_MERCHANT_PRIVATE_KEY)),
        ]);

        $response = Helper::PostRequest($url, $headers, $body, [
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $result = json_decode($response, true);
        Helper::NagadException($result);

        return $result;
    }

    /**
     * execute payment
     */
    public static function executePayment(string $paymentId): array
    {
        $url = Config::get(CONSTANT::NAGAD_BASE_URL) . "/verify/payment/" . $paymentId;

        $response = Helper::GetRequest($url);

        $result = json_decode($response, true);
        Helper::NagadException($result);

        return $result;
    }
}
