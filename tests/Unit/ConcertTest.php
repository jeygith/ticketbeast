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
        $concert = factory(Concert::class)->create([
            'date' => Carbon::parse('2016-12-01 8.00pm')
        ]);

        // retrieve the formatted date

        $date = $concert->formatted_date;
        // verify date is formatted as expected

        $this->assertEquals('December 1, 2016', $date);
    }
}
