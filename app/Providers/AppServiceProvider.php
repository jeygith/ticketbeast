<?php

namespace App\Providers;

use App\Billing\PaymentGateway;
use App\Billing\StripePaymentGateway;
use App\HashIdsTicketCodeGenerator;
use App\InvitationCodeGenerator;
use App\OrderConfirmationNumberGenerator;
use App\RandomOrderConfirmationNumberGenerator;
use App\TicketCodeGenerator;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\DuskServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        if ($this->app->environment('local', 'testing', 'production')) {
            $this->app->register(DuskServiceProvider::class);
        }

        if ($this->app->environment() !== 'production') {
            $this->app->register(IdeHelperServiceProvider::class);
        }


        $this->app->bind(StripePaymentGateway::class, function () {
            return new StripePaymentGateway(config('services.stripe.secret'));
        });

        $this->app->bind(HashIdsTicketCodeGenerator::class, function () {
            return new HashIdsTicketCodeGenerator(config('app.ticket_code_salt'));
        });


        $this->app->bind(PaymentGateway::class, StripePaymentGateway::class);

        $this->app->bind(OrderConfirmationNumberGenerator::class, RandomOrderConfirmationNumberGenerator::class);

        $this->app->bind(InvitationCodeGenerator::class, RandomOrderConfirmationNumberGenerator::class);

        $this->app->bind(TicketCodeGenerator::class, HashIdsTicketCodeGenerator::class);




    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
