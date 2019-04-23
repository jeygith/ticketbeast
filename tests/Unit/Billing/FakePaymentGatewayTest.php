<?php

namespace Tests\Unit\Billing;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentFailedException;
use App\Billing\StripePaymentGateway;
use function foo\func;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class  FakePaymentGatewayTest extends TestCase
{

    use PaymentGatewayContractTests;

    protected function getPaymentGateway()
    {
        return new FakePaymentGateway();
    }

    /** @test */
    function can_fetch_charges_created_during_a_callback()
    {
        $paymentGateway = $this->getPaymentGateway();
        $paymentGateway->charge(2000, $paymentGateway->getValidTestToken());
        $paymentGateway->charge(3000, $paymentGateway->getValidTestToken());

        $newCharges = $paymentGateway->newChargesDuring(function ($paymentGateway) {
            $paymentGateway->charge(4000, $paymentGateway->getValidTestToken());
            $paymentGateway->charge(5000, $paymentGateway->getValidTestToken());
        });


        $this->assertCount(2, $newCharges);
        $this->assertEquals([5000, 4000], $newCharges->all());

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
