<?php


namespace App\Billing;


use Str;

class FakePaymentGateway implements PaymentGateway
{

    private $charges;

    private $tokens;
    private $beforeFirstChargeCallback;

    public function __construct()
    {
        $this->charges = collect();
        $this->tokens = collect();

    }


    public function getValidTestToken($cardNumber = null)
    {
        $token = 'fake-tok_' . Str::random(24);

        $this->tokens[$token] = $cardNumber;

        return $token;
    }

    public function charge($amount, $token)
    {

        if ($this->beforeFirstChargeCallback !== null) {
            $callback = $this->beforeFirstChargeCallback;

            $this->beforeFirstChargeCallback = null;
            $callback->__invoke($this);
        }


        if (!$this->tokens->has($token)) {
            throw new PaymentFailedException;
        }


        return $this->charges[] = new Charge(
            [
                'amount' => $amount,
                'card_last_four' => substr($this->tokens[$token], -4),
            ]

        );

    }


    public function newChargesDuring($callback)
    {
        $chargesFrom = $this->charges->count();

        $callback($this);

        return $this->charges->slice($chargesFrom)->reverse()->values();

    }

    public function totalCharges()
    {
        return $this->charges->map->amount()->sum();
    }

    public function beforeFirstCharge($callback)
    {
        $this->beforeFirstChargeCallback = $callback;
    }
}
