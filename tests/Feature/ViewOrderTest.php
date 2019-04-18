<?php

namespace Tests\Feature;

use App\Concert;
use App\Order;
use App\Ticket;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewOrderTest extends TestCase
{

    use DatabaseMigrations;


    /** @test */
    function user_can_view_their_order_confirmation()
    {
        $this->disableExceptionHandling();

        // create concert

        $concert = factory(Concert::class)->create();

        // create order
        $order = factory(Order::class)->create([
            'confirmation_number' => 'ORDERCONFIRMTION1234'
        ]);

        // create  a tickets
        $ticket = factory(Ticket::class)->create([
            'concert_id' => $concert->id,
            'order_id' => $order->id
        ]);

        //visit order confirmation page


        $response = $this->get("/orders/ORDERCONFIRMTION1234");

        // assert we have correct order details
        $response->assertStatus(200);


        $response->assertViewHas('order', function ($viewOrder) use ($order) {
            return $order->id === $viewOrder->id;
        });
    }
}
