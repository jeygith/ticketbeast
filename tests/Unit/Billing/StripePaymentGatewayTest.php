<?php

namespace Tests\Unit\Billing;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentFailedException;
use Stripe\Stripe;
use Stripe\Token;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class  StripePaymentGatewayTest extends TestCase
{
    /** @test */
    function charges_with_a_valid_token_are_successful()
    {

        // Create a new gateway
        $paymentGateway = new StripePaymentGateway();

        //  Stripe::setApiKey(config('services.stripe.secret'));

        $token = Token::create([
            'card' => [
                'number' => '4242424242424242',
                'exp_month' => 1,
                'exp_year' => date('Y') + 1,
                'cvc' => '123'
            ]
        ], ['api_key' => config('services.stripe.secret')])->id;


        // create new charge with a valid stripe token
        $paymentGateway->charge(2500, $token);

        // verify the charge was completed successfully


        $this->assertEquals(2500, $paymentGateway->totalCharges());

    }


    /** @test */
    function charges_with_an_invalid_payment_token_fail()
    {
        try {
            $paymentGateway = new FakePaymentGateway();


            $paymentGateway->charge(2500, 'invalid-payment-token');
        } catch (PaymentFailedException $e) {
            $this->assertEquals(1, 1);
            return;
        }


        $this->fail();


    }

    /** @test */
    function running_a_hook_before_the_first_charge()
    {
        $paymentGateway = new FakePaymentGateway();


        $callbackRan = false;
        $timesCallbackRan = 0;


        $paymentGateway->beforeFirstCharge(function ($paymentGateway) use (&$timesCallbackRan) {
            $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());

            $timesCallbackRan++;

            $this->assertEquals(2500, $paymentGateway->totalCharges());
        });


        $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());

        $this->assertEquals(1, $timesCallbackRan);

        $this->assertEquals(5000, $paymentGateway->totalCharges());


    }
}
