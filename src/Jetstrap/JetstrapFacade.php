<?php

namespace NascentAfrica\Jetstrap;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void updateNodePackages(callable $callback, bool $dev = true)
 *
 * @see Jetstrap
 */
class JetstrapFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'jetstrap';
    }
}