<?php

namespace App\Jobs;

use App\Mail\AttendeeMessageEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use Mail;

class SendAttendeeMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $attendeeMessage;

    public function __construct($attendeeMessage)
    {
        $this->attendeeMessage = $attendeeMessage;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::alert('handling email');

        $this->attendeeMessage->withChunkedRecipients(20, function ($recipients) {
            Log::alert('in chucked recipients');

            $recipients->each(function ($recipient) {
                Log::alert('for each recipient');
                Mail::to($recipient)->queue(new AttendeeMessageEmail($this->attendeeMessage));
            });
        });

    }

    public function failed()
    {
        // Called when the job is failing...
        Log::alert('error in queue mail');

    }
}
