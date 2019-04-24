<?php

namespace Tests\Unit\Mail;

use App\Mail\OrderConfirmationEmail;
use App\Order;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class OrderConfirmationEmailTest extends TestCase
{

    use DatabaseMigrations;

    /** @test */
    function email_contains_a_link_to_the_order_confirmation_page()
    {
        $order = factory(Order::class)->make([
            'confirmation_number' => 'ORDERCONFIRMATION1234'
        ]);

        $email = new OrderConfirmationEmail($order);


        // $rendered = $this->render($email);

        $rendered = $email->render();

        "http://127.0.0.1:8000/orders/SJ6TVT7HR6FMHG5RCSVHB78Z";

        $this->assertContains(url('/orders/ORDERCONFIRMATION1234'), $rendered);

    }

    /** @test */
    function email_has_a_subject()
    {
        $order = factory(Order::class)->make();
        $email = new OrderConfirmationEmail($order);

        $this->assertEquals("Your TicketBeast Order", $email->build()->subject);

    }


    public function render($mailable)
    {
        $mailable->build();

        return view($mailable->view, $mailable->buildViewData())->render();
    }
}
