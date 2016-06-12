<?php

namespace Sleimanx2\Plastic\Facades;

use Illuminate\Support\Facades\Facade;

class Plastic extends Facade
{
    /**
     * Get a plastic manager instance for the default connection.
     *
     * @return \Sleimanx2\Plastic\DSL\Builder
     */
    protected static function getFacadeAccessor()
    {
        return static::$app['plastic'];
    }
}
