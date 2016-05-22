<?php

namespace Sleimanx2\Plastic;

use Elasticsearch\ClientBuilder;
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
     * Execute a map statement on index;
     *
     * @param $mappings
     * @return array
     */
    public function mapStatement($mappings)
    {
        dd(array_merge(['index' => $this->index], $mappings));

        return $this->elastic->indices()->putMapping(array_merge(['index' => $this->index], $mappings));
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