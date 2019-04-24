<?php

namespace Tests\Unit;

use App\Facades\TicketCode;
use App\Order;
use App\Ticket;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

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
    function a_ticket_can_be_released()
    {
        $ticket = factory(Ticket::class)->states('reserved')->create();

        $this->assertNotNull($ticket->reserved_at);

        $ticket->release();

        $this->assertNull($ticket->fresh()->reserved_at);

    }

    /** @test */
    function a_ticket_can_be_claimed_for_an_order()
    {
        $order = factory(Order::class)->create();

        $ticket = factory(Ticket::class)->create(['code' => null]);

        TicketCode::shouldReceive('generateFor')->with($ticket)->andReturn('TICKETCODE1');

        $this->assertNull($ticket->code);

        $ticket->claimFor($order);


        //assert ticket is saved to the order

        $this->assertEquals($order->id, $ticket->order_id);

        $this->assertContains($ticket->id, $order->tickets->pluck('id'));
        // assert that the ticket has the expected ticket code generated
        $this->assertEquals('TICKETCODE1', $ticket->code);

    }
}
