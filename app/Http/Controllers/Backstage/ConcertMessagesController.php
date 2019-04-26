<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\Controller;
use Auth;

class ConcertMessagesController extends Controller
{
    public function create($id)
    {
        $concert = Auth::user()->concerts()->findOrFail($id);

        return view('backstage.concert-messages.create', compact('concert'));
    }


    public function store($id)
    {
        $concert = Auth::user()->concerts()->findOrFail($id);

        $message = $concert->attendeeMessage()->create(request(['subject', 'message']));

        return redirect()->route('backstage.concert-messages.create', $concert)
            ->with(['flash' => 'Your message has been sent.']);

    }
}
