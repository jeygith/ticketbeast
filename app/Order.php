<?php

namespace App;

use App\Facades\OrderConfirmationNumber;
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

    public static function forTickets($tickets, $email, $charge)
    {

        $order = self::create([
            // 'concert_id' => $this->id,
            'email' => $email,
            'amount' => $charge->amount(),
            'confirmation_number' => OrderConfirmationNumber::generate(),
            'card_last_four' => $charge->cardLastFour(),
        ]);

        foreach ($tickets as $ticket) {
            $order->tickets()->save($ticket);
        }

        return $order;

    }

    public static function findByConfirmationNumber($confirmationNumber)
    {
        return self::where('confirmation_number', $confirmationNumber)->firstOrFail();
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
            'confirmation_number' => $this->confirmation_number,
            'email' => $this->email,
            'ticket_quantity' => $this->ticketQuantity(),
            'amount' => $this->amount,
        ];
    }

}
