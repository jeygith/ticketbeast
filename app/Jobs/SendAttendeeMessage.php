<?php

namespace App\Jobs;

use App\Mail\AttendeeMessageEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
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


        $this->attendeeMessage->recipients()->each(function ($recipient) {
            Mail::to($recipient)->send(new AttendeeMessageEmail($this->attendeeMessage));
        });
    }
}