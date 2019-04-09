<?php

namespace Tests\Feature;

use App\Concert;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ViewConcertListingTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    /*  public function testBasicTest()
      {
          $this = $this->get('/');

          $this->assertStatus(200);
      }*/
    use DatabaseMigrations;

    /** @test */
    function user_can_view_a_concert_listing()
    {
        // Arrange

        $concert = Concert::create([
            'title' => 'The Red Chord',
            'subtitle' => 'with Animosity',
            'date' => Carbon::parse('December 13, 2016 8:00pm'),
            'ticket_price' => 3250,
            'venue' => 'The Mosh Pit',
            'venue_address' => '123 Example Lane',
            'city' => 'Laraville',
            'state' => 'ON',
            'zip' => '17916',
            'additional_information' => 'For tickets, call me'
        ]);

        // act
        $this->visit('/concerts/' . $concert->id);



        // Assert


        $this->see('The Red Chord');
        $this->see('with Animosity');
        $this->see('December 13, 2016');
        $this->see('8:00pm');
        $this->see('32.50');
        $this->see('The Mosh Pit');
        $this->see('123 Example Lane');
        $this->see('Laraville, ON 17916');
        $this->see('For tickets, call me');

    }
}
