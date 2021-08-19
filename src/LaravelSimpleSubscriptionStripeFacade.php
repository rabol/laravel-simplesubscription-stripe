<?php

namespace Rabol\LaravelSimpleSubscriptionStripe;

use Illuminate\Support\Facades\Facade;

class LaravelSimpleSubscriptionStripeFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'LaravelSimpleSubscriptionStripe';
    }
}
