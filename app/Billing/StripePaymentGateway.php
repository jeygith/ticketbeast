<?php


namespace App\Billing;


use Stripe\Error\InvalidRequest;
use Stripe\Token;

class StripePaymentGateway implements PaymentGateway
{

    private $apiKey;
    const TEST_CARD_NUMBER = 4242424242424242;

    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    function charge($amount, $token, $destinationAccountId)
    {


        try {
            $stripeCharge = \Stripe\Charge::create([
                "amount" => $amount,
                "currency" => "USD",
                "source" => $token, // obtained with Stripe.js
                "description" => "",
                'destination' => [
                    'account' => $destinationAccountId,
                    'amount' => $amount * .9
                ],
            ], ['api_key' => $this->apiKey]);

            return new Charge([
                'amount' => $stripeCharge['amount'],
                'card_last_four' => $stripeCharge['source']['last4'],
                'destination' => $destinationAccountId
            ]);

        } catch (InvalidRequest $e) {
            throw new PaymentFailedException;
        }
    }

    public function getValidTestToken($cardNumber = self::TEST_CARD_NUMBER)
    {
        return Token::create([
            'card' => [
                'number' => $cardNumber,
                'exp_month' => 1,
                'exp_year' => date('Y') + 1,
                'cvc' => '123'
            ]
        ], ['api_key' => $this->apiKey])->id;
    }


    public function newChargesDuring($callback)
    {
        $latestCharge = $this->lastCharge();


        $callback($this);


        return $this->newChargesSince($latestCharge)->map(function ($stripeCharge) {
            return new Charge([
                'amount' => $stripeCharge['amount'],
                'card_last_four' => $stripeCharge['source']['last4'],
            ]);
        });

    }

    private function lastCharge()
    {
        return \Stripe\Charge::all(
            ['limit' => 1],
            ['api_key' => $this->apiKey]
        )['data'][0];
    }

    private function newChargesSince($charge = null)
    {
        $newCharges = \Stripe\Charge::all(
            [
                'limit' => 1,
                'ending_before' => $charge ? $charge->id : null
            ],
            ['api_key' => $this->apiKey]
        )['data'];


        return collect($newCharges);
    }

}