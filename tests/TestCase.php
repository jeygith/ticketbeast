<?php

namespace Tests;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\TestResponse;
use Mockery;
use PHPUnit\Framework\Assert;


// use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;


abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public $baseUrl = 'http://localhost';

    protected function setUp(): void
    {
        parent::setUp();


        Mockery::getConfiguration()->allowMockingNonExistentMethods(false);


        TestResponse::macro('data', function ($key) {
            return $this->original->getData()[$key];
        });

        Collection::macro('assertContains', function ($value) {
            Assert::assertTrue($this->contains($value), "Failed asserting that the collection contained the specified value");
        });
        Collection::macro('assertNotContains', function ($value) {
            Assert::assertFalse($this->contains($value), "Failed asserting that the collection did not contain the specified value");
        });

        Collection::macro('assertEquals', function ($items) {
            Assert::assertEquals(count($this), count($items));

            $this->zip($items)->each(function ($pair) {
                list($a, $b) = $pair;

                Assert::assertTrue($a->is($b));
            });
        });

    }


    protected function disableExceptionHandling()
    {

        $this->withExceptionHandling();
        /*$this->app->instance(ExceptionHandler::class, new class extends Handler
        {
            public function __construct()
            {

            }

            public function report(Exception $e)
            {

            }

            public function render($request, Exception $e)
            {
                throw $e;
            }
        });*/
    }
}
