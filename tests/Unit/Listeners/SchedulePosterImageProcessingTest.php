<?php

namespace Tests\Unit\Listeners;

use App\Events\ConcertAdded;
use App\Jobs\ProcessPosterImage;
use ConcertFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Queue;
use Tests\TestCase;

class SchedulePosterImageProcessingTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    function it_queues_a_job_to_process_a_poster_image_if_a_poster_image_is_present()
    {
        $this->withoutExceptionHandling();
        Queue::fake();
        $concert = ConcertFactory::createUnpublished([
            'poster_image_path' => 'posters/optimized-poster.png'
        ]);

        ConcertAdded::dispatch($concert);

        Queue::assertPushed(ProcessPosterImage::class, function ($job) use ($concert) {
            return $job->concert->is($concert);
        });

        /*$listener = new SchedulePosterImageProcessing();
        $listener->handle($event);*/
    }

    /** @test */
    function a_job_is_not_queued_if_a_poster_image_is_not_present()
    {
        $this->withoutExceptionHandling();
        Queue::fake();
        $concert = ConcertFactory::createUnpublished([
            'poster_image_path' => null
        ]);

        ConcertAdded::dispatch($concert);

        Queue::assertNotPushed(ProcessPosterImage::class);
        /*$listener = new SchedulePosterImageProcessing();
        $listener->handle($event);*/
    }
}
