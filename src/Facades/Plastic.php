<?php

namespace Nuwber\Plastic\Facades;

use Illuminate\Support\Facades\Facade;

class Plastic extends Facade
{
    /**
     * Get a plastic manager instance for the default connection.
     *
     * @return \Nuwber\Plastic\DSL\SearchBuilder
     */
    protected static function getFacadeAccessor()
    {
        return static::$app['plastic'];
    }
}
