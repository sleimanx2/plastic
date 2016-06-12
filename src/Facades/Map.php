<?php

namespace Sleimanx2\Plastic\Facades;

use Illuminate\Support\Facades\Facade;

class Map extends Facade
{
    /**
     * Get a map builder instance for the default connection.
     *
     * @return \Sleimanx2\Plastic\Map\Builder
     */
    protected static function getFacadeAccessor()
    {
        return static::$app['plastic']->connection()->getMapBuilder();
    }
}
