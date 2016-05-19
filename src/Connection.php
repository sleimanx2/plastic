<?php

namespace Sleimanx2\Plastic;

use Elasticsearch\ClientBuilder;
use Sleimanx2\Plastic\Map\Builder as MapBuilder;

class Connection
{

    /**
     * @var
     */
    protected $elastic;

    /**
     * @var array
     */
    private $config = [
        'hosts'   => ['localhost:9200'],
        'retries' => 2
    ];


    /**
     * Connection constructor.
     *
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;

        $this->elastic = $this->initClient();
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
     * Create an elastic search instance
     *
     * @return \Elasticsearch\Client
     */
    private function initClient()
    {
        return ClientBuilder::create()
            ->setHosts($this->config['hosts'])
            ->setRetries($this->config['retries'])
            ->build();
    }
}