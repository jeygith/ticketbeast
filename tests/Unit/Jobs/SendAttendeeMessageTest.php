<?php


namespace Tests\Unit\Jobs;


use App\AttendeeMessage;
use App\Concert;
use App\Jobs\SendAttendeeMessage;
use App\Mail\AttendeeMessageEmail;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mail;
use OrderFactory;
use Tests\TestCase;

class SendAttendeeMessageTest extends TestCase
{

    use DatabaseMigrations;

    /** @test */
    function it_sends_the_message_to_all_concert_attendees()
    {
        Mail::fake();

        $concert = factory(Concert::class)->create();
        $anotherConcert = factory(Concert::class)->create();


        $message = AttendeeMessage::create([
            'concert_id' => $concert->id,
            'subject' => 'My Subject',
            'message' => 'My Message'
        ]);


        $orderA = OrderFactory::createForConcert($concert, ['email' => 'alex@example.com']);
        $otherOrder = OrderFactory::createForConcert($anotherConcert, ['email' => 'jane@example.com']);
        $orderB = OrderFactory::createForConcert($concert, ['email' => 'sam@example.com']);
        $orderC = OrderFactory::createForConcert($concert, ['email' => 'taylor@example.com']);

        /*        $job = new SendAttendeeMessage($message);

                $job->handle();*/

        SendAttendeeMessage::dispatch($message);


        Mail::assertSent(AttendeeMessageEmail::class, function ($mail) use ($message) {
            return $mail->hasTo('alex@example.com')
                && $mail->attendeeMessage->is($message);
        });
        Mail::assertSent(AttendeeMessageEmail::class, function ($mail) use ($message) {
            return $mail->hasTo('sam@example.com') && $mail->attendeeMessage->is($message);

        });
        Mail::assertSent(AttendeeMessageEmail::class, function ($mail) use ($message) {
            return $mail->hasTo('taylor@example.com')
                && $mail->attendeeMessage->is($message);

        });
        Mail::assertNotSent(AttendeeMessageEmail::class, function ($mail) {
            return $mail->hasTo('jane@example.com');

        });

    }

}