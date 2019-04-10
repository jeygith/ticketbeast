<?php


use App\Concert;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ConcertTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function can_get_formatted_date()
    {
        // create concert with a known date
        $concert = factory(Concert::class)->make([
            'date' => Carbon::parse('2016-12-01 8.00pm')
        ]);

        // retrieve the formatted date

        // verify date is formatted as expected

        $this->assertEquals('December 1, 2016', $concert->formatted_date);
    }

    /** @test */

    function can_get_formatted_start_time()
    {
        $concert = factory(Concert::class)->make([
            'date' => Carbon::parse('2016-12-01 17:00:00')
        ]);

        $this->assertEquals('5:00pm', $concert->formatted_start_time);
    }

    /**
     * @test
     */
    function can_get_ticket_price_in_dollars()
    {
        $concert = factory(Concert::class)->make([
            'ticket_price' => 6750
        ]);


        $this->assertEquals('67.50', $concert->ticket_price_in_dollars);
    }

    /** @test */
    function concerts_with_a_published_at_date_are_published()
    {
        //test query scope
        $publishedConcertA = factory(Concert::class)->create([
            'published_at' => Carbon::parse('-1 week')
        ]);
        $publishedConcertB = factory(Concert::class)->create([
            'published_at' => Carbon::parse('-1 week')
        ]);
        $unpublishedConcerts = factory(Concert::class)->create([
            'published_at' => null]);


        $publishedConcerts = Concert::published()->get();

        $this->assertTrue($publishedConcerts->contains($publishedConcertA));
        $this->assertTrue($publishedConcerts->contains($publishedConcertB));
        $this->assertFalse($publishedConcerts->contains($unpublishedConcerts));

    }


    /** @test */
    function can_order_concert_tickets()
    {
        $concert = factory(Concert::class)->create();


        $order = $concert->orderTickets('jane@example.com', 3);


        $this->assertEquals('jane@example.com', $order->email);

        $this->assertEquals(3, $order->tickets()->count());

    }

}