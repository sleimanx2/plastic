<?php

namespace Nuwber\Plastic\Facades;

use Illuminate\Support\Facades\Facade;

class Map extends Facade
{
    /**
     * Get a map builder instance for the default connection.
     *
     * @return \Nuwber\Plastic\Map\Builder
     */
    protected static function getFacadeAccessor()
    {
        return static::$app['plastic']->connection()->getMapBuilder();
    }
}
