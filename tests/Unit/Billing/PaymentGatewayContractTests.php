<?php


namespace Tests\Unit\Billing;

use App\Billing\PaymentFailedException;

trait PaymentGatewayContractTests
{
    abstract protected function getPaymentGateway();


    /** @test */
    function charges_with_a_valid_token_are_successful()
    {

        // Create a new gateway
        $paymentGateway = $this->getPaymentGateway();

        // create new charge with a valid stripe token

        $newCharges = $paymentGateway->newChargesDuring(function ($paymentGateway) {
            $paymentGateway->charge(2500, $paymentGateway->getValidTestToken());
        });

        // verify the charge was completed successfully

        $this->assertCount(1, $newCharges);

        $this->assertEquals(2500, $newCharges->sum());

    }


    /** @test */
    function charges_with_an_invalid_payment_token_fail()
    {
        $paymentGateway = $this->getPaymentGateway();
        $newCharges = $paymentGateway->newChargesDuring(function ($paymentGateway) {

            try {

                $paymentGateway->charge(2500, 'invalid-payment-token');

            } catch (PaymentFailedException $e) {
                return;
            }

            $this->fail('Charging with an invalid payment token did not throw a PaymentFailedException');


        });

        $this->assertCount(0, $newCharges);


    }

}