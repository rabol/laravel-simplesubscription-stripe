<?php

namespace Rabol\LaravelSimpleSubscriptionStripe\Commands;

use Illuminate\Console\Command;

class LaravelSimpleSubscriptionStripeCommand extends Command
{
    public $signature = 'laravel-simplesubscription-stripe';

    public $description = 'My command';

    public function handle()
    {
        $this->comment('All done');
    }
}
