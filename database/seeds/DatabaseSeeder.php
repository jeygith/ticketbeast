<?php

use App\Billing\FakePaymentGateway;
use App\User;
use Carbon\Carbon;
use Faker\Factory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $faker = Factory::create();


        $gateway = new FakePaymentGateway;


        $user = factory(User::class)->create([
            'email' => 'person@example.com',
            'password' => bcrypt('secret'),
            'stripe_account_id' => null,
            'stripe_access_token' => null,
        ]);

        $concert = ConcertFactory::createPublished([
            'user_id' => $user->id,
            'title' => "The Red Chord",
            'subtitle' => "with Animosity and Lethargy",
            'additional_information' => "This concert is 19+.",
            'venue' => "The Mosh Pit",
            'venue_address' => "123 Example Lane",
            'city' => "Laraville",
            'state' => "ON",
            'zip' => "17916",
            'date' => Carbon::today()->addMonths(3)->hour(20),
            'ticket_price' => 3250,
            'ticket_quantity' => 225
        ]);

        foreach (range(1, 50) as $i) {
            Carbon::setTestNow(Carbon::instance($faker->dateTimebetween('-2 months')));

            $concert->reserveTickets(rand(1, 4), $faker->safeEmail)
                ->complete($gateway, $gateway->getValidTestToken($faker->creditCardNumber), 'test_account_1234');
        }

        Carbon::setTestNow();

        factory(App\Concert::class)->create([
            'user_id' => $user->id,
            'title' => "Slayer",
            'subtitle' => "with Forbidden and Testament",
            'additional_information' => null,
            'venue' => "The Rock Pile",
            'venue_address' => "55 Sample Blvd",
            'city' => "Laraville",
            'state' => "ON",
            'zip' => "19276",
            'date' => Carbon::today()->addMonths(4)->hour(18),
            'ticket_price' => 5500,
            'ticket_quantity' => 10,
        ]);
    }
}
