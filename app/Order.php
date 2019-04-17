<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Order
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Ticket[] $tickets
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Order query()
 * @mixin \Eloquent
 */
class Order extends Model
{
    //

    protected $guarded = [];

    public static function forTickets($tickets, $email, $amount)
    {
        $order = self::create([
            // 'concert_id' => $this->id,
            'email' => $email,
            'amount' => $amount
        ]);

        foreach ($tickets as $ticket) {
            $order->tickets()->save($ticket);
        }

        return $order;

    }

    public static function fromReservation($reservation)
    {
        $order = self::create([
            // 'concert_id' => $this->id,
            'email' => $reservation->email(),
            'amount' => $reservation->totalCost()
        ]);

        $order->tickets()->saveMany($reservation->tickets());

        return $order;

    }

    public function concert()
    {
        return $this->belongsTo(Concert::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }


    /* public function cancel()
     {
         foreach ($this->tickets as $ticket) {
             $ticket->release();
         }

         $this->delete();
     }*/

    /**
     * @return int
     */
    public function ticketQuantity()
    {
        return $this->tickets()->count();
    }


    public function toArray()
    {
        return [
            'email' => $this->email,
            'ticket_quantity' => $this->ticketQuantity(),
            'amount' => $this->amount,
        ];
    }
}
