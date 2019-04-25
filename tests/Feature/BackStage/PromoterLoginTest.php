<?php

namespace Tests\Feature\BackStage;

use App\User;
use Auth;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\TestResponse;
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

        $response->assertRedirect('/backstage/concerts/new');

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
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));


        $this->assertFalse(Auth::check());

    }

    /** @test */
    public function logging_out_an_authenticated_user()
    {
        Auth::login(factory(User::class)->create());

        $response = $this->post('/logout');

        $response->assertStatus(302)
            ->assertRedirect('/login');
        $this->assertFalse(Auth::check());
    }
}
