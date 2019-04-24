<?php

namespace Tests\Unit;

use App\Concert;
use App\Facades\TicketCode;
use App\HashIdsTicketCodeGenerator;
use App\Order;
use App\Ticket;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HashIdsTicketCodeGeneratorTest extends TestCase
{

    use DatabaseMigrations;


    /** @test */
    function ticket_codes_are_at_least_6_characters_long()
    {
        $ticketCodeGenerator = new HashIdsTicketCodeGenerator('testsalt1');

        $code = $ticketCodeGenerator->generateFor(new Ticket(['id' => 1]));


        $this->assertTrue(strlen($code) >= 6);

    }

    /** @test */
    function ticket_codes_can_only_contain_uppercase_letters()
    {
        $ticketCodeGenerator = new HashIdsTicketCodeGenerator('testsalt1');

        $code = $ticketCodeGenerator->generateFor(new Ticket(['id' => 1]));


        $this->assertRegExp('/^[A-Z]+$/', $code);

    }

    /** @test */
    function ticket_codes_for_the_same_ticket_id_are_the_same()
    {
        $ticketCodeGenerator = new HashIdsTicketCodeGenerator('testsalt1');


        $code1 = $ticketCodeGenerator->generateFor(new Ticket(['id' => 1]));
        $code2 = $ticketCodeGenerator->generateFor(new Ticket(['id' => 1]));


        self::assertEquals($code1, $code2);

    }


    /** @test */
    function ticket_codes_for_the_different_ticket_ids_are_different()
    {
        $ticketCodeGenerator = new HashIdsTicketCodeGenerator('testsalt1');


        $code1 = $ticketCodeGenerator->generateFor(new Ticket(['id' => 1]));
        $code2 = $ticketCodeGenerator->generateFor(new Ticket(['id' => 2]));

        self::assertNotEquals($code1, $code2);

    }

    /** @test */
    function ticket_codes_generated_with_different_salts_are_different()
    {
        $ticketCodeGenerator1 = new HashIdsTicketCodeGenerator('testsalt1');

        $ticketCodeGenerator2 = new HashIdsTicketCodeGenerator('testsalt2');

        $code1 = $ticketCodeGenerator1->generateFor(new Ticket(['id' => 1]));
        $code2 = $ticketCodeGenerator2->generateFor(new Ticket(['id' => 1]));

        self::assertNotEquals($code1, $code2);
    }
}
