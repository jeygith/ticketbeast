<?php


namespace App;


use Hashids\Hashids;

class HashIdsTicketCodeGenerator implements TicketCodeGenerator
{

    private $hashids;

    public function __construct($salt = null)
    {
        $this->hashids = new Hashids($salt, 6, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
    }

    public function generateFor($ticket)
    {
        return $this->hashids->encode($ticket->id);
    }
}