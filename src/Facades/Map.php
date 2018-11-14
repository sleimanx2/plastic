<?php

namespace Sleimanx2\Plastic\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static create(string $type, \Closure $callback, string $index = null) Create a map on your Elasticsearch index.
 * @method static blueprintResolver(\Closure $resolver)                          Set the Schema Blueprint resolver callback.
 */
class Map extends Facade
{
    /**
     * Get a map builder instance for the default connection.
     *
     * @return \Sleimanx2\Plastic\Map\Builder
     */
    protected static function getFacadeAccessor()
    {
        /* @noinspection PhpUndefinedMethodInspection */
        return static::$app['plastic']->connection()->getMapBuilder();
    }
}
