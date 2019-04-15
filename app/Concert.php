<?php

namespace App;

use App\Exceptions\NotEnoughTicketsException;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Concert
 *
 * @property-read mixed $formatted_date
 * @property-read mixed $formatted_start_time
 * @property-read mixed $ticket_price_in_dollars
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Order[] $orders
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Ticket[] $tickets
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Concert newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Concert newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Concert published()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Concert query()
 * @mixin \Eloquent
 */
class Concert extends Model
{
    //
    protected $guarded = [];

    protected $dates = ['date'];


    public function getFormattedDateAttribute()
    {
        return $this->date->format('F j, Y');
    }


    public function getFormattedStartTimeAttribute()
    {
        return $this->date->format('g:ia');
    }

    public function getTicketPriceInDollarsAttribute()
    {
        return number_format($this->ticket_price / 100, 2);
    }


    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }


    public function hasOrderFor($email)
    {
        return $this->orders()->where('email', $email)->count() > 0;

    }

    public function ordersFor($email)
    {
        return $this->orders()->where('email', $email)->get();

    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }


    public function orderTickets($email, $ticketQuantity)
    {


        $tickets = $this->tickets()->available()->take($ticketQuantity)->get();


        if ($tickets->count() < $ticketQuantity) {
            throw new NotEnoughTicketsException;
        }

        $order = $this->orders()->create(['email' => $email]);

        foreach ($tickets as $ticket) {
            $order->tickets()->save($ticket);
        }

        return $order;
    }


    /**
     * @param $quantity
     */
    public function addTickets($quantity)
    {
        foreach (range(1, $quantity) as $i) {
            $this->tickets()->create([]);
        }

        return $this;

    }

    /**
     *
     */
    public function ticketsRemaining()
    {
        return $this->tickets()->available()->count();
    }
}
