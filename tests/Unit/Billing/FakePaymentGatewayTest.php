<?php

namespace Tests\Unit\Billing;

use App\Billing\FakePaymentGateway;
use Tests\TestCase;

class  FakePaymentGatewayTest extends TestCase
{

    use PaymentGatewayContractTests;

    protected function getPaymentGateway()
    {
        return new FakePaymentGateway();
    }


    /** @test */
    function can_get_total_charges_for_a_specific_account()
    {
        $paymentGateway = new FakePaymentGateway;

        $paymentGateway->charge(1000, $paymentGateway->getValidTestToken(), 'test_acc_0000');
        $paymentGateway->charge(2500, $paymentGateway->getValidTestToken(), 'test_acc_1234');
        $paymentGateway->charge(4000, $paymentGateway->getValidTestToken(), 'test_acc_1234');


        $this->assertEquals(6500, $paymentGateway->totalChargesFor('test_acc_1234'));


    }


    /** @test */
    function running_a_hook_before_the_first_charge()
    {
        $paymentGateway = new FakePaymentGateway();


        $callbackRan = false;
        $timesCallbackRan = 0;


        $paymentGateway->beforeFirstCharge(function ($paymentGateway) use (&$timesCallbackRan) {
            $paymentGateway->charge(2500, $paymentGateway->getValidTestToken(), 'test_acc_1234');

            $timesCallbackRan++;

            $this->assertEquals(2500, $paymentGateway->totalCharges());
        });


        $paymentGateway->charge(2500, $paymentGateway->getValidTestToken(), 'test_acc_1234');

        $this->assertEquals(1, $timesCallbackRan);

        $this->assertEquals(5000, $paymentGateway->totalCharges());


    }


    /** @test */
    function can_fetch_charges_created_during_a_callback()
    {
        $paymentGateway = $this->getPaymentGateway();
        $paymentGateway->charge(2000, $paymentGateway->getValidTestToken(), 'test_acc_1234');
        $paymentGateway->charge(3000, $paymentGateway->getValidTestToken(), 'test_acc_1234');

        $newCharges = $paymentGateway->newChargesDuring(function ($paymentGateway) {
            $paymentGateway->charge(4000, $paymentGateway->getValidTestToken(), 'test_acc_1234');
            $paymentGateway->charge(5000, $paymentGateway->getValidTestToken(), 'test_acc_1234');
        });


        $this->assertCount(2, $newCharges);
        $this->assertEquals([5000, 4000], $newCharges->map->amount()->all());

    }


}
