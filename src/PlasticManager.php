<?php

namespace Sleimanx2\Plastic;

class PlasticManager
{
    /**
     * @var \Illuminate\Foundation\Application|\Laravel\Lumen\Application
     */
    private $app;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * PlasticManager constructor.
     *
     * @param \Illuminate\Foundation\Application|\Laravel\Lumen\Application $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Get an elastic search connection instance.
     */
    public function connection()
    {
        if (!$this->connection) {
            $config = $this->app['config']['plastic'];

            $this->connection = new Connection($config);
        }

        return $this->connection;
    }

    /**
     * Dynamically pass methods to the default connection.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array([$this->connection(), $method], $parameters);
    }
}
