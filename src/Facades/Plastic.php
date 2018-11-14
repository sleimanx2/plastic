<?php

namespace Sleimanx2\Plastic\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Sleimanx2\Plastic\Connection   connection()       Get an Elasticsearch connection instance.
 * @method static string                          getDefaultIndex()  Get the default elastic index.
 * @method static \Sleimanx2\Plastic\Map\Builder  getMapBuilder()    Get map builder instance for this connection.
 * @method static \Sleimanx2\Plastic\Map\Grammar  getMapGrammar()    Get map grammar instance for this connection.
 * @method static \ONGR\ElasticsearchDSL\Search   getDSLQuery()      Get DSL grammar instance for this connection.
 * @method static \Elasticsearch\Client           getClient()        Get the Elasticsearch client instance.
 *
 * @method static             setClient(\Elasticsearch\Client $client)  Set a custom elastic client.
 * @method static             setDefaultIndex(string $index)            Set the default index.
 * @method static array       mapStatement(array $mappings)             Execute a map statement on index.
 * @method static array       searchStatement(array $search)            Execute a search statement on index.
 * @method static array       suggestStatement(array $suggestions)      Execute a suggest statement on index.
 * @method static array       indexStatement(array $params)             Execute an insert statement on index.
 * @method static array       updateStatement(array $params)            Execute an update statement on index.
 * @method static array       deleteStatement(array $params)            Execute a delete statement on index.
 * @method static array|bool  existsStatement(array $params)            Execute an exists statement on index.
 * @method static array       bulkStatement(array $params)              Execute a bulk statement on index.
 *
 * @method static \Sleimanx2\Plastic\DSL\SearchBuilder                search()   Begin a fluent search query builder.
 * @method static \Sleimanx2\Plastic\DSL\SuggestionBuilder            suggest()  Begin a fluent suggest query builder.
 * @method static \Sleimanx2\Plastic\Persistence\EloquentPersistence  persist()  Create a new elastic persistence handler.
 */
class Plastic extends Facade
{
    /**
     * Get a plastic manager instance for the default connection.
     *
     * @return \Sleimanx2\Plastic\PlasticManager
     */
    protected static function getFacadeAccessor()
    {
        return static::$app['plastic'];
    }
}
