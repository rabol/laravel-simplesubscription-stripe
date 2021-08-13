<?php

namespace Rabol\LaravelSimplesubscriptionStripe\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Rabol\LaravelSimplesubscriptionStripe\LaravelSimplesubscriptionStripeServiceProvider;

class TestCase extends Orchestra
{
    public function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Rabol\\LaravelSimplesubscriptionStripe\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelSimplesubscriptionStripeServiceProvider::class,
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
