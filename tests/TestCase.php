<?php

namespace JeffersonGoncalves\Trustpilot\Tests;

use JeffersonGoncalves\Trustpilot\TrustpilotServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            TrustpilotServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('trustpilot.api_key', 'fake-api-key');
        $app['config']->set('trustpilot.api_secret', 'fake-api-secret');
        $app['config']->set('trustpilot.business_unit_id', 'fake-business-unit');
        $app['config']->set('trustpilot.base_url', 'https://api.trustpilot.com/v1');
    }
}
