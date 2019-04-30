<?php

namespace Tests\Unit\Http\Middleware;

use App\Http\Middleware\ForceStripeAccount;
use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Tests\TestCase;

class ForceStripeAccountTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    function users_without_a_stripe_account_are_forced_to_connect_with_stripe()
    {
        $user = factory(User::class)->create([
            'stripe_account_id' => null,
        ]);

        $this->be($user);

        $middleware = new ForceStripeAccount;


        $response = $middleware->handle(new Request, function ($request) {
            $this->fail("Next middleware was called when it should not have been.");
        });


        $this->assertInstanceOf(RedirectResponse::class, $response);


        $this->assertEquals(route('backstage.stripe-connect.connect'), $response->getTargetUrl());
    }
}
