<?php

namespace Sleimanx2\Plastic;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
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
    protected $index;

    /**
     * Elasticsearch client instance.
     *
     * @var Client
     */
    protected $elastic;

    /**
     * Connection constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->elastic = $this->buildClient($config['connection']);

        $this->setDefaultIndex($config['index']);
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
     * Set the default index.
     *
     * @param $index
     *
     * @return Connection
     */
    public function setDefaultIndex($index)
    {
        $this->index = $index;
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
        return $this->elastic->indices()->putMapping($this->setStatementIndex($mappings));
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
        return $this->elastic->search($this->setStatementIndex($search));
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
        return $this->elastic->suggest($this->setStatementIndex($suggestions));
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
        return $this->elastic->index($this->setStatementIndex($params));
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
        return $this->elastic->update($this->setStatementIndex($params));
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
        return $this->elastic->delete($this->setStatementIndex($params));
    }

    /**
     * Execute a exists statement on index.
     *
     * @param array $params
     *
     * @return array|bool
     */
    public function existsStatement(array $params)
    {
        return $this->elastic->exists($this->setStatementIndex($params));
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
     * @return EloquentPersistence
     */
    public function persist()
    {
        return new EloquentPersistence($this);
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

    /**
     * @param array $params
     *
     * @return array
     */
    private function setStatementIndex(array $params)
    {
        if (isset($params['index']) and $params['index']) {
            return $params;
        }

        // merge the default index with the given params if the index is not set.
        return array_merge($params, ['index' => $this->getDefaultIndex()]);
    }
}
