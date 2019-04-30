<?php

namespace Tests\Unit\Billing;

use App\Billing\StripePaymentGateway;
use Stripe\Charge;
use Stripe\Transfer;
use Tests\TestCase;

/*
 * @group integration
 */

class  StripePaymentGatewayTest extends TestCase
{
    use PaymentGatewayContractTests;


    protected function getPaymentGateway()
    {
        return new StripePaymentGateway(config('services.stripe.secret'));
    }


    /** @test */
    function ninety_percent_of_the_payment_is_transferred_to_the_destination_account()
    {
        $paymenyGateway = new StripePaymentGateway(config('services.stripe.secret'));

        $paymenyGateway->charge(5000, $paymenyGateway->getValidTestToken(), env('STRIPE_TEST_PROMOTER_ID'));


        $lastStripeCharge = Charge::all(
            ['limit' => 1],
            ['api_key' => config('services.stripe.secret')]
        )['data'][0];

        $this->assertEquals(5000, $lastStripeCharge['amount']);

        $this->assertEquals(env('STRIPE_TEST_PROMOTER_ID'), $lastStripeCharge['destination']);

        $transfer = Transfer::retrieve($lastStripeCharge['transfer'], ['api_key' => config('services.stripe.secret')]);

        $this->assertEquals(4500, $transfer['amount']);


    }


}
