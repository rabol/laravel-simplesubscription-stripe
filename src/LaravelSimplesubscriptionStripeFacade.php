<?php

namespace Rabol\LaravelSimplesubscriptionStripe;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Rabol\LaravelSimplesubscriptionStripe\LaravelSimplesubscriptionStripe
 */
class LaravelSimplesubscriptionStripeFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-simplesubscription-stripe';
    }
}
