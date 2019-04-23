<?php

namespace Tests\Unit;

use App\RandomOrderConfirmationNumberGenerator;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;


class RandomOrderConfirmationNumberGeneratorTest extends TestCase
{

    use DatabaseMigrations;


    // can only contain uppercase letters and numbers

    // cannot contain ambiguous characters

    // length of 24 characters
    // all confirmation numbers must be unique

    /** @test */
    function must_be_24_characters_long()
    {
        $generator = new RandomOrderConfirmationNumberGenerator();

        $confirmationNumber = $generator->generate();

        $this->assertEquals(24, strlen($confirmationNumber));
    }


    /** @test */
    function can_only_contain_uppercase_letters_and_numbers()
    {
        $generator = new RandomOrderConfirmationNumberGenerator();

        $confirmationNumber = $generator->generate();

        $this->assertRegExp('/^[A-Z0-9]+$/', $confirmationNumber);
    }

    /** @test */
    function can_only_contain_ambiguous_characters()
    {
        $generator = new RandomOrderConfirmationNumberGenerator();

        $confirmationNumber = $generator->generate();

        $this->assertFalse(strpos($confirmationNumber, '1'));
        $this->assertFalse(strpos($confirmationNumber, 'I'));
        $this->assertFalse(strpos($confirmationNumber, '0'));
        $this->assertFalse(strpos($confirmationNumber, 'O'));

    }

    /** @test */
    function confirmation_numbers_should_be_unique()
    {
        $generator = new RandomOrderConfirmationNumberGenerator();

        $confirmationNumbers = array_map(function ($i) use ($generator) {
            return $generator->generate();
        },
            (range(1, 100)));

        $this->assertCount(100, array_unique($confirmationNumbers));

    }

}
