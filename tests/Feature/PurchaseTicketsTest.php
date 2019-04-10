<?php

namespace Tests\Feature;

use App\Billing\FakePaymentGateway;
use App\Billing\PaymentGateway;
use App\Concert;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseTicketsTest extends TestCase
{
    use DatabaseMigrations;
    protected $paymentGateway;

    protected function setUp(): void
    {
        parent::setUp();

        $this->paymentGateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $this->paymentGateway);

    }

    /** @test */
    function customer_can_purchase_concert_tickets()
    {


        // arrange
        // create a concert

        $concert = factory(Concert::class)->create([
            'ticket_price' => 3250
        ]);

        //act
        //purchase tickets

        $this->json('POST', "/concerts/{$concert->id}/orders", [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        //assert

        $this->assertResponseStatus(201);
        // make sure customer was charged correct amount

        $this->assertEquals(9750, $this->paymentGateway->totalCharges());
        // make sure order exists
        $order = $concert->orders()->where('email', 'john@example.com')->first();

        $this->assertNotNull($order);
        /*$this->assertTrue($concert->orders->contains(function ($order) {
            return $order->email === 'john@example.com';
        }));*/


        $this->assertEquals(3, $order->tickets()->count());
    }


    /** @test */
    function email_is_required_to_purchase_tickets()
    {

        // $this->disableExceptionHandling();


        $concert = factory(Concert::class)->create();

        //act

        $this->json('POST', "/concerts/{$concert->id}/orders", [
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);


        $this->assertResponseStatus(422);
        $this->assertArrayHasKey("email", $this->decodeResponseJson()["errors"]);


    }

}
