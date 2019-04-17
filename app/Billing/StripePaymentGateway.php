<?php


namespace App\Billing;


use Stripe\Charge;

class StripePaymentGateway implements PaymentGateway
{

    private $apiKey;

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    function charge($amount, $token)
    {
        // TODO: Implement charge() method.

        Charge::create([
            "amount" => $amount,
            "currency" => "USD",
            "source" => $token, // obtained with Stripe.js
            "description" => ""
        ], ['api_key' => $this->apiKey]);
    }

}