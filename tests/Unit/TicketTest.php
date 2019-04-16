<?php

namespace Tests\Unit;

use App\Concert;
use App\Order;
use App\Ticket;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TicketTest extends TestCase
{

    use DatabaseMigrations;


    /** @test */
    function a_ticket_can_be_reserved()
    {

        $ticket = factory(Ticket::class)->create();
        $this->assertNull($ticket->reserved_at);


        $ticket->reserve();


        $this->assertNotNull($ticket->fresh()->reserved_at);
    }


    /** @test */
    function tickets_are_released_when_order_is_cancelled()
    {
        $concert = factory(Concert::class)->create();
        $concert->addTickets(10);

        $order = $concert->orderTickets('jane@example.com', 5);


        $this->assertEquals(5, $concert->ticketsRemaining());


        $order->cancel();


        $this->assertEquals(10, $concert->ticketsRemaining());

        $this->assertNull(Order::find($order->id));


    }


    /** @test */
    function a_ticket_can_be_released()
    {
        $concert = factory(Concert::class)->create();
        $concert->addTickets(1);
        $order = $concert->orderTickets('jane@example.com', 1);

        $ticket = $order->tickets()->first();

        $this->assertEquals($order->id, $ticket->order_id);


        $ticket->release();


        $this->assertNull($ticket->fresh()->order_id);

    }
}
