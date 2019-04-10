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

    private function orderTickets($concert, $params)
    {
        $this->json('POST', "/concerts/{$concert->id}/orders", $params);

    }


    private function assertValidationError($field)
    {
        $this->assertResponseStatus(422);
        $this->assertArrayHasKey($field, $this->decodeResponseJson()["errors"]);
    }

    /** @test */
    function customer_can_purchase_tickets_to_a_published_concert()
    {


        // arrange
        // create a concert

        $concert = factory(Concert::class)->states('published')->create(['ticket_price' => 3250]);

        //act
        //purchase tickets

        $this->orderTickets($concert, [
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
    function cannot_purchase_tickets_to_an_unpublished_concert()
    {

        $concert = factory(Concert::class)->states('unpublished')->create();

        $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);


        $this->assertResponseStatus(404);

        $this->assertEquals(0, $concert->orders()->count());

        $this->assertEquals(0, $this->paymentGateway->totalCharges());


    }

    /** @test */
    function an_order_is_not_created_if_payment_fails()
    {
        $this->disableExceptionHandling();
        $concert = factory(Concert::class)->states('published')->create(['ticket_price' => 3250]);

        $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 3,
            'payment_token' => 'invalid-payment-token'
        ]);


        $this->assertResponseStatus(422);

        $order = $concert->orders()->where('email', 'john@example.com')->first();

        $this->assertNull($order);

    }

    /** @test */
    function cannot_purchase_more_tickets_than_remain()
    {
        $concert = factory(Concert::class)->states('published')->create();

        $concert->addTickets(50);

        $this->orderTickets($concert, [
            'email' => 'john@example.com',
            'ticket_quantity' => 51,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);

        $this->assertResponseStatus(422);

        $order = $concert->orders()->where('email', 'john@example.com')->first();

        $this->assertNull($order);

        $this->assertEquals(0, $this->paymentGateway->totalCharges());

        $this->assertEquals(50, $concert->ticketsRemaining());
    }

    /** @test */
    function email_is_required_to_purchase_tickets()
    {

        // $this->disableExceptionHandling();


        $concert = factory(Concert::class)->states('published')->create();

        //act
        $this->orderTickets($concert, [
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);


        $this->assertValidationError('email');
    }


    /** @test */
    public function email_must_be_valid_to_purchase_tickets()
    {

        // $this->disableExceptionHandling();
        $concert = factory(Concert::class)->states('published')->create();
        $this->orderTickets($concert, [
            'email' => 'not-an-email-address',
            'ticket_quantity' => 3,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);
        $this->assertValidationError("email");
    }

    /** @test */
    public function ticket_quantity_is_required_to_purchase_tickets()
    {
        $concert = factory(Concert::class)->states('published')->create();
        $this->orderTickets($concert, [
            'email' => 'jane@example.com',
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);
        $this->assertValidationError("ticket_quantity");

    }

    /** @test */
    public function ticket_quantity_must_be_greater_than_zero()
    {

        $concert = factory(Concert::class)->states('published')->create();

        $this->orderTickets($concert, [
            'email' => 'jane@example.com',
            'ticket_quantity' => 0,
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);
        $this->assertValidationError("ticket_quantity");

    }

    /** @test */
    public function ticket_quantity_must_be_an_integer()
    {
        $concert = factory(Concert::class)->states('published')->create();

        $this->orderTickets($concert, [
            'email' => 'jane@example.com',
            'ticket_quantity' => 's',
            'payment_token' => $this->paymentGateway->getValidTestToken()
        ]);
        $this->assertValidationError("ticket_quantity");
    }

    /** @test */
    public function a_payment_token_is_required()
    {
        $concert = factory(Concert::class)->states('published')->create();

        $this->orderTickets($concert, [
            'email' => 'jane@example.com',
            'ticket_quantity' => 0,
        ]);
        $this->assertValidationError("payment_token");


    }


}
