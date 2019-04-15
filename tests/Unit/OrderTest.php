<?php

namespace Tests\Unit;

use App\Concert;
use App\Order;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderTest extends TestCase
{

    use DatabaseMigrations;

    /** @test */
    function converting_to_an_array()
    {

        $concert = factory(Concert::class)->create(['ticket_price' => 1200])->addTickets(10);

        $order = $concert->orderTickets('jane@example.com', 5);


        $result = $order->toArray();

        $this->assertEquals([
            'email' => 'jane@example.com',
            'ticket_quantity' => 5,
            'amount' => 6000
        ], $result);
    }


    /** @test */
    function tickets_are_released_when_order_is_cancelled()
    {
        $concert = factory(Concert::class)->create()->addTickets(10);

        $order = $concert->orderTickets('jane@example.com', 5);


        $this->assertEquals(5, $concert->ticketsRemaining());


        $order->cancel();


        $this->assertEquals(10, $concert->ticketsRemaining());

        $this->assertNull(Order::find($order->id));


    }
}
