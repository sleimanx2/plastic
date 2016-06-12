<?php

namespace Sleimanx2\Plastic;

use Illuminate\Foundation\Application;

class PlasticManager
{
    /**
     * @var Application
     */
    private $app;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * PlasticManager constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
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
