<?php
namespace Sleimanx2\Plastic\Facades;

use Illuminate\Support\Facades\Facade;

class Map extends Facade
{
    /**
     * Get a schema builder instance for the default connection.
     *
     * @return \Illuminate\Database\Schema\Builder
     */
    protected static function getFacadeAccessor()
    {
        return static::$app['plastic']->connection()->getMapBuilder();
    }
}