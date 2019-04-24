<?php

namespace Tests\Feature;

use App\User;
use Auth;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class PromoterLoginTest extends TestCase
{

    use DatabaseMigrations;

    /** @test */
    function showing_the_login_form()
    {
        $this->disableExceptionHandling();

        $response = $this->get('/login');


        $response->assertStatus(200);

        $response->assertSee('form');
    }

    /** @test */
    function loggin_in_with_valid_credentials()
    {
        $this->disableExceptionHandling();

        $user = factory(User::class)->create([

            'email' => 'jane@example.com',
            'password' => bcrypt('super-secret-password')
        ]);


        $response = $this->post('/login', [
            'email' => 'jane@example.com',
            'password' => 'super-secret-password'
        ]);

        $response->assertRedirect('/backstage/concerts');

        $this->assertTrue(Auth::check());


        $this->assertTrue(Auth::user()->is($user));

    }

    /** @test */
    function loggin_in_with_invalid_credentials()
    {
        $this->disableExceptionHandling();

        $user = factory(User::class)->create([

            'email' => 'jane@example.com',
            'password' => bcrypt('super-secret-password')
        ]);


        $response = $this->post('/login', [
            'email' => 'jane@example.com',
            'password' => 'wrong-password'
        ]);

        $response->assertRedirect('/login');

        $response->assertSessionHasErrors('email');

        $this->assertFalse(Auth::check());

    }

    /** @test */
    function loggin_in_with_account_that_does_not_exist()
    {
        $this->disableExceptionHandling();

        $response = $this->post('/login', [
            'email' => 'nobody@example.com',
            'password' => 'wrong-password'
        ]);

        $response->assertRedirect('/login');

        $response->assertSessionHasErrors('email');

        $this->assertFalse(Auth::check());

    }
}
