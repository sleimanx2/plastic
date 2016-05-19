<?php

namespace Sleimanx2\Plastic\Map;

use Closure;
use Illuminate\Support\Fluent;
use Sleimanx2\Plastic\Connection;

class Blueprint
{
    /**
     * The type the blueprint describes.
     *
     * @var string
     */
    protected $type;

    /**
     * The fields that should be mapped.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * The commands that should be run for the type.
     *
     * @var array
     */
    protected $commands = [];

    /**
     * Blueprint constructor.
     *
     * @param $type
     * @param Closure|null $callback
     */
    public function __construct($type, Closure $callback = null)
    {
        $this->type = $type;

        if (!is_null($callback)) {
            $callback($this);
        }
    }

    /**
     * Execute the blueprint against the database.
     *
     * @param Connection $connection
     * @param Grammar $grammar
     */
    public function build(Connection $connection, Grammar $grammar)
    {

        $this->toQueryDSL($connection, $grammar);

//        foreach ($this->toSql($connection, $grammar) as $statement) {
//            $connection->statement($statement);
//        }
    }

    /**
     * Indicate that the table needs to be created.
     *
     * @return \Illuminate\Support\Fluent
     */
    public function create()
    {
        return $this->addCommand('create');
    }

    /**
     * Add a string field to the map
     *
     * @param string $field
     * @return Fluent
     */
    public function string($field)
    {
        return $this->addField('string', $field);
    }

    /**
     * Add a long numeric field to the map
     *
     * @param string $field
     * @return Fluent
     */
    public function long($field)
    {
        return $this->addField('long', $field);
    }

    /**
     * Add an integer field to the map
     *
     * @param string $field
     * @return Fluent
     */
    public function integer($field)
    {
        return $this->addField('integer', $field);
    }

    /**
     * Add a short numeric field to the map
     *
     * @param string $field
     * @return Fluent
     */
    public function short($field)
    {
        return $this->addField('short', $field);
    }

    /**
     * Add a byte numeric field to the map
     *
     * @param string $field
     * @return Fluent
     */
    public function byte($field)
    {
        return $this->addField('byte', $field);
    }

    /**
     * Add a double field to the map
     *
     * @param string $field
     * @return Fluent
     */
    public function double($field)
    {
        return $this->addField('double', $field);
    }

    /**
     * Add a binary field to the map
     *
     * @param string $field
     * @return Fluent
     */
    public function binary($field)
    {
        return $this->addField('double', $field);
    }

    /**
     * Add a float field to the map
     *
     * @param string $field
     * @return Fluent
     */
    public function float($field)
    {
        return $this->addField('float', $field);
    }

    /**
     * Add a geo point field to the map
     *
     * @param string $field
     * @return Fluent
     */
    public function point($field)
    {
        return $this->addField('geo_point', $field);
    }

    /**
     * Add a geo shape field to the map
     *
     * @param string $field
     * @return Fluent
     */
    public function shape($field)
    {
        return $this->addField('geo_shape', $field);
    }

    /**
     * Add an IPv4 field to the map
     *
     * @param string $field
     * @return Fluent
     */
    public function ip($field)
    {
        return $this->addField('ip', $field);
    }

    /**
     * Add a completion field to the map
     *
     * @param string $field
     * @return Fluent
     */
    public function completion($field)
    {
        return $this->addField('completion', $field);
    }


    /**
     * Add a completion field to the map
     *
     * @param string $field
     * @return Fluent
     */
    public function tokenCount($field)
    {
        return $this->addField('token_count', $field);
    }

    /**
     * Add a murmur3 field to the map
     *
     * @param string $field
     * @return Fluent
     */
    public function murmur3($field)
    {
        return $this->addField('murmur3', $field);
    }

    /**
     * Add a new field to the blueprint
     *
     * @param string $type
     * @param string $name
     * @param array $attributes
     * @return Fluent
     */
    public function addField($type, $name, array $attributes = [])
    {
        $attributes = array_merge(compact($type, $name), $attributes);

        $this->fields[] = $field = new Fluent($attributes);

        return $field;
    }

    /**
     * Add a new command to the blueprint.
     *
     * @param  string $name
     * @param  array $parameters
     * @return \Illuminate\Support\Fluent
     */
    protected function addCommand($name, array $parameters = [])
    {
        $this->commands[] = $command = $this->createCommand($name, $parameters);

        return $command;
    }

    /**
     * Create a new Fluent command.
     *
     * @param  string $name
     * @param  array $parameters
     * @return \Illuminate\Support\Fluent
     */
    protected function createCommand($name, array $parameters = [])
    {
        return new Fluent(array_merge(compact('name'), $parameters));
    }


    /**
     * Get the raw DSL statements for the blueprint.
     *
     * @param Connection $connection
     * @param  Grammar $grammar
     * @return array
     */
    public function toQueryDSL(Connection $connection, Grammar $grammar)
    {
        $statements = [];

        // Each type of command has a corresponding compiler function on the schema
        // grammar which is used to build the necessary DSL statements to build
        // the blueprint element, so we'll just call that compilers function.
        foreach ($this->commands as $command) {
            $method = 'compile' . ucfirst($command->name);

            if (method_exists($grammar, $method)) {
                if (!is_null($sql = $grammar->$method($this, $command, $connection))) {
                    $statements = array_merge($statements, (array)$sql);
                }
            }
        }

        return $statements;
    }

}