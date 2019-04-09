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
          $response = $this->get('/');

          $response->assertStatus(200);
      }*/

    /** @test */
    function user_can_view_a_concert_listing()
    {
        // Arrange

        $concert = Concert::make([
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

        $response = $this->get('/concerts/.$concert->id');


        // Assert


        $response->assertSee('The Red Chord');
        $response->assertSee('with Animosity');
        $response->assertSee('December 13, 2016');
        $response->assertSee('8:00pm');
        $response->assertSee('The Mosh Pit');
        $response->assertSee('123 Example Lane');
        $response->assertSee('Laraville, ON 17916');
        $response->assertSee('For tickets, call me');
    }
}
