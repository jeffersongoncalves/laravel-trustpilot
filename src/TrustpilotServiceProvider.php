<?php

namespace Jeffersongoncalves\Trustpilot;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class TrustpilotServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-trustpilot')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations();
    }
}
