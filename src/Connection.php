<?php

namespace Sleimanx2\Plastic;

use Elasticsearch\ClientBuilder;
use Illuminate\Database\Eloquent\Model;
use Sleimanx2\Plastic\DSL\Builder as DSLBuilder;
use ONGR\ElasticsearchDSL\Search as DSLGrammar;
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
    protected $elastic;

    /**
     * Default elastic config
     *
     * @var array
     */
    private $config = [
        'hosts'   => ['localhost:9200'],
        'retries' => 2,
        'index'   => 'default_index'
    ];

    /**
     * Connection constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = empty($config) ? $this->config : $config;

        $this->elastic = $this->buildClient($this->config);

        $this->index = $this->config['index'];
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
    public function getDSLGrammar()
    {
        return new DSLGrammar();
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
     * Get the default elastic index
     *
     * @return mixed
     */
    public function getDefaultIndex()
    {
        return $this->index;
    }

    /**
     * Execute a map statement on index;
     *
     * @param $query
     * @return array
     */
    public function queryStatement(DSLBuilder $query)
    {
        $params = [
            'index' => $this->index,
            'type'  => $query->type,
            'body'  => $query->toDSL()
        ];

        return $this->elastic->search($params);
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
     * Begin a fluent query builder against an elastic type.
     *
     * @param  string $type
     * @return \Illuminate\Database\Query\Builder
     */
    public function type($type)
    {
        return $this->dsl()->type($type);
    }

    /**
     * Begin a fluent query builder using a model.
     *
     * @param  Model $type
     * @return \Illuminate\Database\Query\Builder
     */
    public function model(Model $model)
    {
        return $this->dsl()->model($model);
    }

    /**
     * Get a new dsl builder instance.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function dsl()
    {
        return new DSLBuilder(
            $this, $this->getDSLGrammar()
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
        return new EloquentPersistence($this,$model);
    }

    /**
     * Create an elastic search instance
     *
     * @param array $config
     * @return \Elasticsearch\Client
     */
    private function buildClient(array $config)
    {
        return ClientBuilder::create()
            ->setHosts($config['hosts'])
            ->setRetries($config['retries'])
            ->build();
    }
}