<?php

namespace Rabol\LaravelSimplesubscriptionStripe;

use Illuminate\Support\Facades\Facade;
use LaravelSimplesubscriptionStripe;

/**
 * @see \Rabol\LaravelSimplesubscriptionStripe\LaravelSimplesubscriptionStripe
 */
class LaravelSimplesubscriptionStripeFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'LaravelSimplesubscriptionStripe';
    }
}
