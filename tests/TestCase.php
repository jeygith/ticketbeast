<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;


use App\Exceptions\Handler;
use Exception;
// use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

use Illuminate\Contracts\Debug\ExceptionHandler;


abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public $baseUrl = 'http://localhost';

    protected function setUp(): void
    {
        parent::setUp();

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
