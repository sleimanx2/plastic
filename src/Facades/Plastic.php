<?php

namespace LoRDFM\Plastic\Facades;

use Illuminate\Support\Facades\Facade;

class Plastic extends Facade
{
    /**
     * Get a plastic manager instance for the default connection.
     *
     * @return \LoRDFM\Plastic\DSL\Builder
     */
    protected static function getFacadeAccessor()
    {
        return static::$app['plastic'];
    }
}
