<?php

namespace Tests;

use App\Exceptions\Handler;
use Exception;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Mockery;


// use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;


abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public $baseUrl = 'http://localhost';

    protected function setUp(): void
    {
        parent::setUp();


        Mockery::getConfiguration()->allowMockingNonExistentMethods(false);

    }


    protected function disableExceptionHandling()
    {
        $this->app->instance(ExceptionHandler::class, new class extends Handler
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
        });
    }
}
