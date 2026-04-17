<?php

namespace App\Controllers;

use App\Models\Nagad;
use Core\BaseController;
use App\Validators\PaymentValidator;
use Exception;

class NagadController extends BaseController
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
            $result = Nagad::createPayment($Validated);

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
            $result = Nagad::executePayment($paymentId);

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
