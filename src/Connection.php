<?php

namespace Sleimanx2\Plastic;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Database\Eloquent\Model;
use ONGR\ElasticsearchDSL\Search as DSLQuery;
use Sleimanx2\Plastic\DSL\SearchBuilder;
use Sleimanx2\Plastic\DSL\SuggestionBuilder;
use Sleimanx2\Plastic\Map\Builder as MapBuilder;
use Sleimanx2\Plastic\Map\Grammar as MapGrammar;
use Sleimanx2\Plastic\Persistence\EloquentPersistence;

class Connection
{
    /**
     * Elastic Search default index.
     *
     * @var string
     */
    public $index;

    /**
     * Elasticsearch client instance.
     *
     * @var Client
     */
    public $elastic;

    /**
     * Connection constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->elastic = $this->buildClient($config['connection']);

        $this->index = $config['index'];
    }

    /**
     * Get map builder instance for this connection.
     *
     * @return MapBuilder
     */
    public function getMapBuilder()
    {
        return new MapBuilder($this);
    }

    /**
     * Get map grammar instance for this connection.
     *
     * @return MapBuilder
     */
    public function getMapGrammar()
    {
        return new MapGrammar();
    }

    /**
     * Get DSL grammar instance for this connection.
     *
     * @return DSLGrammar
     */
    public function getDSLQuery()
    {
        return new DSLQuery();
    }

    /**
     * Get the elastic search client instance.
     *
     * @return Client
     */
    public function getClient()
    {
        return $this->elastic;
    }

    /**
     * Set a custom elastic client.
     *
     * @param Client $client
     */
    public function setClient(Client $client)
    {
        $this->elastic = $client;
    }

    /**
     * Get the default elastic index.
     *
     * @return string
     */
    public function getDefaultIndex()
    {
        return $this->index;
    }

    /**
     * Execute a map statement on index;.
     *
     * @param array $mappings
     *
     * @return array
     */
    public function mapStatement(array $mappings)
    {
        return $this->elastic->indices()->putMapping(array_merge(['index' => $this->index], $mappings));
    }

    /**
     * Execute a map statement on index;.
     *
     * @param array $search
     *
     * @return array
     */
    public function searchStatement(array $search)
    {
        return $this->elastic->search(array_merge(['index' => $this->index], $search));
    }

    /**
     * Execute a map statement on index;.
     *
     * @param array $suggestions
     *
     * @return array
     */
    public function suggestStatement(array $suggestions)
    {
        return $this->elastic->suggest(array_merge(['index' => $this->index], $suggestions));
    }

    /**
     * Execute a insert statement on index;.
     *
     * @param $params
     *
     * @return array
     */
    public function indexStatement(array $params)
    {
        return $this->elastic->index(array_merge(['index' => $this->index], $params));
    }

    /**
     * Execute a update statement on index;.
     *
     * @param $params
     *
     * @return array
     */
    public function updateStatement(array $params)
    {
        return $this->elastic->update(array_merge(['index' => $this->index], $params));
    }

    /**
     * Execute a update statement on index;.
     *
     * @param $params
     *
     * @return array
     */
    public function deleteStatement(array $params)
    {
        return $this->elastic->delete(array_merge(['index' => $this->index], $params));
    }

    /**
     * Execute a exist statement on index.
     *
     * @param array $params
     *
     * @return array|bool
     */
    public function existStatement(array $params)
    {
        return $this->elastic->exists(array_merge(['index' => $this->index], $params));
    }

    /**
     * Execute a bulk statement on index;.
     *
     * @param $params
     *
     * @return array
     */
    public function bulkStatement(array $params)
    {
        return $this->elastic->bulk($params);
    }

    /**
     * Begin a fluent search query builder.
     *
     * @return SearchBuilder
     */
    public function search()
    {
        return new SearchBuilder($this, $this->getDSLQuery());
    }

    /**
     * Begin a fluent suggest query builder.
     *
     * @return SuggestionBuilder
     */
    public function suggest()
    {
        return new SuggestionBuilder($this, $this->getDSLQuery());
    }

    /**
     * Create a new elastic persistence handler.
     *
     * @param Model $model
     *
     * @return EloquentPersistence
     */
    public function persist(Model $model)
    {
        return new EloquentPersistence($this, $model);
    }

    /**
     * Create an elastic search instance.
     *
     * @param array $config
     *
     * @return Client
     */
    private function buildClient(array $config)
    {
        $client = ClientBuilder::create()
            ->setHosts($config['hosts']);

        if (isset($config['retries'])) {
            $client->setRetries($config['retries']);
        }

        if (isset($config['logging']) and $config['logging']['enabled'] == true) {
            $logger = ClientBuilder::defaultLogger($config['logging']['path'], $config['logging']['level']);
            $client->setLogger($logger);
        }

        return $client->build();
    }
}
