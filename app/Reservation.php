<?php


namespace App;


class Reservation
{


    protected $tickets, $customerEmail;

    public function __construct($tickets, $email)
    {
        $this->tickets = $tickets;
        $this->customerEmail = $email;
    }

    public function totalCost()
    {
        return $this->tickets->sum('price');
    }

    public function tickets()
    {
        return $this->tickets;
    }

    public function email()
    {
        return $this->email;
    }

    public function complete($paymentGateway, $paymentToken)
    {
        $paymentGateway->charge($this->totalCost(), $paymentToken);

        return Order::forTickets($this->tickets(), $this->email(), $this->totalCost());
    }


    public function cancel()
    {
        foreach ($this->tickets as $ticket) {
            $ticket->release();
        }
    }


}