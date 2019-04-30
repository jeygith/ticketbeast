<?php

namespace Tests\Feature;

use App\Concert;
use App\Order;
use App\Ticket;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ViewOrderTest extends TestCase
{

    use DatabaseMigrations;


    /** @test */
    function user_can_view_their_order_confirmation()
    {
        $this->withoutExceptionHandling();

        // create concert

        $concert = factory(Concert::class)->create();

        // create order
        $order = factory(Order::class)->create([
            'confirmation_number' => 'ORDERCONFIRMTION1234',
            'card_last_four' => 1881,
            'amount' => 8500,
        ]);

        // create  a tickets
        $ticketA = factory(Ticket::class)->create([
            'concert_id' => $concert->id,
            'order_id' => $order->id,
            'code' => 'TICKETCODE123'
        ]);
        $ticketB = factory(Ticket::class)->create([
            'concert_id' => $concert->id,
            'order_id' => $order->id,
            'code' => 'TICKETCODE456'
        ]);

        //visit order confirmation page


        $response = $this->get("/orders/ORDERCONFIRMTION1234");

        // assert we have correct order details
        $response->assertStatus(200);


        $response->assertViewHas('order', function ($viewOrder) use ($order) {
            return $order->id === $viewOrder->id;
        });

        $response->assertSee('ORDERCONFIRMTION1234')
            ->assertSee('$85.00')
            ->assertSee('**** **** **** 1881')
            ->assertSee('TICKETCODE123')
            ->assertSee('TICKETCODE456')/* ->assertSee('The Red Chord')
            ->assertSee('with Animosity and Lethargy')
            ->assertSee('The Mosh Pit')
            ->assertSee('123 Example Lane')
            ->assertSee('Laraville, ON')
            ->assertSee('17916')
            ->assertSee('john@example.com')
            ->assertSee('2017-03-12 20:00')*/
        ;


    }
}
