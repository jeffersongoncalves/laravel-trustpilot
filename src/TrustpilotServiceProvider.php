<?php

namespace JeffersonGoncalves\Trustpilot;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class TrustpilotServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('trustpilot')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(TrustpilotClient::class);
        $this->app->alias(TrustpilotClient::class, 'trustpilot');
    }
}
