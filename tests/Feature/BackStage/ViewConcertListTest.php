<?php

namespace Tests\Feature\BackStage;


use App\Concert;
use App\User;
use ConcertFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
use PHPUnit\Framework\Assert;
use Tests\TestCase;

class ViewConcertListTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function guest_cannot_view_a_promoters_concert_list()
    {

        $response = $this->get('/backstage/concerts');


        $response->assertStatus(302);

        $response->assertRedirect('/login');
    }

    /** @test */
    function promoters_can_only_view_a_list_of_their_own_concerts()
    {
        $this->disableExceptionHandling();
        $user = factory(User::class)->create();
        $otherUser = factory(User::class)->create();


        /* $concerts = factory(Concert::class, 3)->create(['user_id' => $user->id]);*/
        $publishedConcertA = ConcertFactory::createPublished(['user_id' => $user->id]);
        $publishedConcertB = ConcertFactory::createPublished(['user_id' => $otherUser->id]);
        $publishedConcertC = ConcertFactory::createPublished(['user_id' => $user->id]);

        $unpublishedConcertA = ConcertFactory::createUnpublished(['user_id' => $user->id]);
        $unpublishedConcertB = ConcertFactory::createUnpublished(['user_id' => $otherUser->id]);
        $unpublishedConcertC = ConcertFactory::createUnpublished(['user_id' => $user->id]);


        $concertD = factory(Concert::class)->create(['user_id' => $user->id]);

        $otherUsersConcert = factory(Concert::class)->create([
            'user_id' => $otherUser->id
        ]);

        $response = $this->actingAs($user)->get('/backstage/concerts');

        $response->assertStatus(200);

        $response->data('publishedConcerts')->assertContains($publishedConcertA);
        $response->data('publishedConcerts')->assertContains($publishedConcertC);
        $response->data('publishedConcerts')->assertNotContains($publishedConcertB);
        $response->data('publishedConcerts')->assertNotContains($unpublishedConcertA);
        $response->data('publishedConcerts')->assertNotContains($unpublishedConcertB);
        $response->data('publishedConcerts')->assertNotContains($unpublishedConcertC);


        $response->data('unpublishedConcerts')->assertContains($unpublishedConcertA);
        $response->data('unpublishedConcerts')->assertContains($unpublishedConcertC);
        $response->data('unpublishedConcerts')->assertNotContains($unpublishedConcertB);
        $response->data('unpublishedConcerts')->assertNotContains($publishedConcertA);
        $response->data('unpublishedConcerts')->assertNotContains($publishedConcertB);
        $response->data('unpublishedConcerts')->assertNotContains($publishedConcertC);


    }

    protected function setUp(): void
    {
        parent::setUp();

        TestResponse::macro('data', function ($key) {
            return $this->original->getData()[$key];
        });

        Collection::macro('assertContains', function ($value) {
            Assert::assertTrue($this->contains($value), "Failed asserting that the collection contained the specified value");
        });
        Collection::macro('assertNotContains', function ($value) {
            Assert::assertFalse($this->contains($value), "Failed asserting that the collection did not contain the specified value");
        });

    }
}
