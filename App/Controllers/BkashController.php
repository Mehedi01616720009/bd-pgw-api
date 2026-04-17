<?php

namespace App\Controllers;

use App\Models\Bkash;
use Core\BaseController;
use App\Validators\PaymentValidator;
use Exception;

class BkashController extends BaseController
{
    /**
     * Paynow
     */
    public function paynow()
    {
        $Validated = $this->validateApi(
            PaymentValidator::create(),
            PaymentValidator::messages()
        );

        try {
            $result = Bkash::createPayment($Validated);

            $this->json([
                'success' => true,
                'message' => 'Payment created successfully!',
                'data' => $result
            ])->send();
        } catch (Exception $err) {
            $this->json([
                'success' => false,
                'message' => $err->getMessage(),
                'data' => null
            ], 400)->send();
        }
    }

    /**
     * Verify
     */
    public function verify($paymentId)
    {
        try {
            $result = Bkash::executePayment($paymentId);

            $this->json([
                'success' => true,
                'message' => 'Payment executed successfully!',
                'data' => $result
            ])->send();
        } catch (Exception $err) {
            $this->json([
                'success' => false,
                'message' => $err->getMessage(),
                'data' => null
            ], 400)->send();
        }
    }
}
