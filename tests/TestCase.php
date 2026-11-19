<?php

namespace DREID\LaravelSepaXml\Tests;

use DREID\LaravelSepaXml\Providers\ServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            ServiceProvider::class,
        ];
    }
}
