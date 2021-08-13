<?php

namespace Rabol\LaravelSimplesubscriptionStripe;

use Rabol\LaravelSimplesubscriptionStripe\Commands\LaravelSimplesubscriptionStripeCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelSimplesubscriptionStripeServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-simplesubscription-stripe')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-simplesubscription-stripe_table')
            ->hasCommand(LaravelSimplesubscriptionStripeCommand::class);
    }
}
