<?php

namespace Jeffersongoncalves\Trustpilot\Tests;

use Jeffersongoncalves\Trustpilot\TrustpilotServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            TrustpilotServiceProvider::class,
        ];
    }
}
