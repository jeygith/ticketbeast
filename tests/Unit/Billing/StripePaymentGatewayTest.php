<?php

namespace Tests\Unit\Billing;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentFailedException;
use App\Billing\StripePaymentGateway;
use Stripe\Charge;
use Stripe\Stripe;
use Stripe\Token;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

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




}
