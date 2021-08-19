<?php

namespace Rabol\LaravelSimpleSubscriptionStripe\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Rabol\LaravelSimpleSubscriptionStripe\LaravelSimpleSubscriptionStripeServiceProvider;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Rabol\\LaravelSimpleSubscriptionStripe\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelSimpleSubscriptionStripeServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
        include_once __DIR__.'/../database/migrations/create_laravel-simplesubscription-stripe_table.php.stub';
        (new \CreatePackageTable())->up();
        */
    }
}
