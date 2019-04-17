<?php

namespace Tests\Unit;

use App\Concert;
use App\Order;
use App\Ticket;
use Carbon\Carbon;
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
    function a_ticket_can_be_released()
    {
        $ticket = factory(Ticket::class)->states('reserved')->create();

        $this->assertNotNull($ticket->reserved_at);

        $ticket->release();

        $this->assertNull($ticket->fresh()->reserved_at);

    }
}
