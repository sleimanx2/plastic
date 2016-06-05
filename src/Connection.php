<?php

namespace Sleimanx2\Plastic;

use Elasticsearch\ClientBuilder;
use Illuminate\Database\Eloquent\Model;
use Sleimanx2\Plastic\DSL\SearchBuilder;
use ONGR\ElasticsearchDSL\Search as DSLQuery;
use Sleimanx2\Plastic\DSL\SuggestionBuilder;
use Sleimanx2\Plastic\Map\Builder as MapBuilder;
use Sleimanx2\Plastic\Map\Grammar as MapGrammar;
use Sleimanx2\Plastic\Persistence\EloquentPersistence;

class Connection
{

    /**
     * Elastic Search default index
     *
     * @var string
     */
    public $index;

    /**
     * Elasticsearch client instance
     *
     * @var \Elasticsearch\Client
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
     * Get map builder instance for this connection
     *
     * @return MapBuilder
     */
    public function getMapBuilder()
    {
        return new MapBuilder($this);
    }

    /**
     * Get map grammar instance for this connection
     *
     * @return MapBuilder
     */
    public function getMapGrammar()
    {
        return new MapGrammar();
    }

    /**
     * Get DSL grammar instance for this connection
     *
     * @return DSLGrammar
     */
    public function getDSLQuery()
    {
        return new DSLQuery();
    }

    /**
     * Get the elastic search client instance
     *
     * @return \Elasticsearch\Client
     */
    public function getClient()
    {
        return $this->elastic;
    }

    /**
     * Get the default elastic index
     *
     * @return string
     */
    public function getDefaultIndex()
    {
        return $this->index;
    }

    /**
     * Execute a map statement on index;
     *
     * @param array $mappings
     * @return array
     */
    public function mapStatement(array $mappings)
    {
        return $this->elastic->indices()->putMapping(array_merge(['index' => $this->index], $mappings));
    }

    /**
     * Execute a map statement on index;
     *
     * @param SearchBuilder $builder
     * @return array
     */
    public function searchStatement(SearchBuilder $builder)
    {
        $params = [
            'index' => $this->index,
            'type'  => $builder->type,
            'body'  => $builder->toDSL()
        ];

        return $this->elastic->search($params);
    }

    /**
     * Execute a map statement on index;
     *
     * @param SuggestionBuilder $builder
     * @return array
     */
    public function suggestStatement(SuggestionBuilder $builder)
    {
        $params = [
            'index' => $this->index,
            'body'  => $builder->toDSL()
        ];

        return $this->elastic->suggest($params);
    }

    /**
     * Execute a insert statement on index;
     *
     * @param $params
     * @return array
     */
    public function indexStatement(array $params)
    {
        return $this->elastic->index(array_merge(['index' => $this->index], $params));
    }

    /**
     * Execute a update statement on index;
     *
     * @param $params
     * @return array
     */
    public function updateStatement(array $params)
    {
        return $this->elastic->update(array_merge(['index' => $this->index], $params));
    }

    /**
     * Execute a update statement on index;
     *
     * @param $params
     * @return array
     */
    public function deleteStatement(array $params)
    {
        return $this->elastic->delete(array_merge(['index' => $this->index], $params));
    }

    /**
     * Execute a bulk statement on index;
     *
     * @param $params
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
        return $this->searchBuilder();
    }

    /**
     * Begin a fluent suggest query builder.
     *
     * @return SuggestionBuilder
     */
    public function suggest()
    {
        return $this->suggestionBuilder();
    }


    /**
     * Get a new dsl builder instance.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function searchBuilder()
    {
        return new SearchBuilder(
            $this, $this->getDSLQuery()
        );
    }

    /**
     * Get a new dsl builder instance.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function suggestionBuilder()
    {
        return new SuggestionBuilder(
            $this, $this->getDSLQuery()
        );
    }

    /**
     * Create a new elastic persistence handler
     *
     * @param Model $model
     * @return EloquentPersistence
     */
    public function persistence(Model $model)
    {
        return new EloquentPersistence($this, $model);
    }

    /**
     * Create an elastic search instance
     *
     * @param array $config
     * @return \Elasticsearch\Client
     */
    private function buildClient(array $config)
    {
        $client = ClientBuilder::create()
            ->setHosts($config['hosts'])
            ->setRetries($config['retries']);

        if ($config['logging']['enabled'] == true) {
            $logger = ClientBuilder::defaultLogger($config['logging']['path'], $config['logging']['level']);
            $client->setLogger($logger);
        }

        return $client->build();
    }
}