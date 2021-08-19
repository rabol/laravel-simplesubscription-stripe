<?php

namespace Rabol\LaravelSimpleSubscriptionStripe;

use Rabol\LaravelSimpleSubscriptionStripe\Commands\LaravelSimpleSubscriptionStripeCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelSimpleSubscriptionStripeServiceProvider extends PackageServiceProvider
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
            ->hasConfigFile('simplesubscription-stripe')
            ->hasViews()
            ->hasMigration('add_stripe_fields_to_users_table')
            ->hasCommand(LaravelSimpleSubscriptionStripeCommand::class);
    }
}
