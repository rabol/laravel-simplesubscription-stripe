<?php

namespace Rabol\LaravelSimplesubscriptionStripe\Commands;

use Illuminate\Console\Command;

class LaravelSimplesubscriptionStripeCommand extends Command
{
    public $signature = 'laravel-simplesubscription-stripe';

    public $description = 'My command';

    public function handle()
    {
        $this->comment('All done');
    }
}
