<?php

namespace Sleimanx2\Plastic;

use Elasticsearch\ClientBuilder;
use Illuminate\Database\Eloquent\Model;
use Sleimanx2\Plastic\DSL\Builder as DSLBuilder;
use ONGR\ElasticsearchDSL\Search as DSLGrammar;
use Sleimanx2\Plastic\Map\Builder as MapBuilder;
use Sleimanx2\Plastic\Map\Grammar as MapGrammar;

class Connection
{

    /**
     * Elasticsearch client instance
     *
     * @var
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
     * @param $mappings
     * @return array
     */
    public function mapStatement($mappings)
    {
        return $this->elastic->indices()->putMapping(array_merge(['index' => $this->index], $mappings));
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