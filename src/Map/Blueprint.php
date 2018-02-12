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
     * @var elastic search index
     */
    private $index;

    /**
     * Blueprint constructor.
     *
     * @param $type
     * @param Closure|null $callback
     * @param null         $index
     */
    public function __construct($type, Closure $callback = null, $index = null)
    {
        $this->type = $type;

        if (!is_null($callback)) {
            $callback($this);
        }
        $this->index = $index;
    }

    /**
     * Execute the blueprint against the database.
     *
     * @param Connection $connection
     * @param Grammar    $grammar
     *
     * @return array
     */
    public function build(Connection $connection, Grammar $grammar)
    {
        $statement = [
            'index' => $this->index,
            'type'  => $this->type,
            'body'  => [
                $this->type => [
                    '_source'    => [
                        'enabled' => true,
                    ],
                    'properties' => $this->toDSL($grammar),
                ],
            ],
        ];

        return $connection->mapStatement($statement);
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
     * Add a string field to the map.
     *
     * @param string $field
     * @param array  $attributes
     *
     * @return Fluent
     */
    public function string($field, $attributes = [])
    {
        return $this->addField('string', $field, $attributes);
    }

    /**
     * Add a date field to the map.
     *
     * @param string $field
     * @param array  $attributes
     *
     * @return Fluent
     */
    public function date($field, $attributes = [])
    {
        return $this->addField('date', $field, $attributes);
    }

    /**
     * Add a long numeric field to the map.
     *
     * @param string $field
     * @param array  $attributes
     *
     * @return Fluent
     */
    public function long($field, $attributes = [])
    {
        return $this->addField('long', $field, $attributes);
    }

    /**
     * Add an integer field to the map.
     *
     * @param string $field
     * @param array  $attributes
     *
     * @return Fluent
     */
    public function integer($field, $attributes = [])
    {
        return $this->addField('integer', $field, $attributes);
    }

    /**
     * Add a short numeric field to the map.
     *
     * @param string $field
     * @param array  $attributes
     *
     * @return Fluent
     */
    public function short($field, $attributes = [])
    {
        return $this->addField('short', $field, $attributes);
    }

    /**
     * Add a byte numeric field to the map.
     *
     * @param string $field
     * @param array  $attributes
     *
     * @return Fluent
     */
    public function byte($field, $attributes = [])
    {
        return $this->addField('byte', $field, $attributes);
    }

    /**
     * Add a double field to the map.
     *
     * @param string $field
     * @param array  $attributes
     *
     * @return Fluent
     */
    public function double($field, $attributes = [])
    {
        return $this->addField('double', $field, $attributes);
    }

    /**
     * Add a binary field to the map.
     *
     * @param string $field
     * @param array  $attributes
     *
     * @return Fluent
     */
    public function binary($field, $attributes = [])
    {
        return $this->addField('binary', $field, $attributes);
    }

    /**
     * Add a float field to the map.
     *
     * @param string $field
     * @param array  $attributes
     *
     * @return Fluent
     */
    public function float($field, $attributes = [])
    {
        return $this->addField('float', $field, $attributes);
    }

    /**
     * Add a boolean field to the map.
     *
     * @param string $field
     * @param array  $attributes
     *
     * @return Fluent
     */
    public function boolean($field, $attributes = [])
    {
        return $this->addField('boolean', $field, $attributes);
    }

    /**
     * Add a geo point field to the map.
     *
     * @param string $field
     * @param array  $attributes
     *
     * @return Fluent
     */
    public function point($field, $attributes = [])
    {
        return $this->addField('point', $field, $attributes);
    }

    /**
     * Add a geo shape field to the map.
     *
     * @param string $field
     * @param array  $attributes
     *
     * @return Fluent
     */
    public function shape($field, $attributes = [])
    {
        return $this->addField('shape', $field, $attributes);
    }

    /**
     * Add an IPv4 field to the map.
     *
     * @param string $field
     * @param array  $attributes
     *
     * @return Fluent
     */
    public function ip($field, $attributes = [])
    {
        return $this->addField('ip', $field, $attributes);
    }

    /**
     * Add a completion field to the map.
     *
     * @param string $field
     * @param array  $attributes
     *
     * @return Fluent
     */
    public function completion($field, $attributes = [])
    {
        return $this->addField('completion', $field, $attributes);
    }

    /**
     * Add a completion field to the map.
     *
     * @param string $field
     * @param array  $attributes
     *
     * @return Fluent
     */
    public function tokenCount($field, $attributes = [])
    {
        return $this->addField('token_count', $field, $attributes);
    }

    /**
     * Add a nested map.
     *
     * @param $field
     * @param Closure $callback
     *
     * @return Fluent
     */
    public function nested($field, Closure $callback)
    {
        return $this->addField('nested', $field, ['callback' => $callback]);
    }

    /**
     * Add a object map.
     *
     * @param         $field
     * @param Closure $callback
     *
     * @return Fluent
     */
    public function object($field, Closure $callback)
    {
        return $this->addField('object', $field, ['callback' => $callback]);
    }

    /**
     * Add a new field to the blueprint.
     *
     * @param string $type
     * @param string $name
     * @param array  $attributes
     *
     * @return Fluent
     */
    public function addField($type, $name, array $attributes = [])
    {
        $attributes = array_merge(compact('type', 'name'), $attributes);

        $this->fields[] = $field = new Fluent($attributes);

        return $field;
    }

    /**
     * Get the registered fields.
     *
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * Get the command fields.
     *
     * @return array
     */
    public function getCommands()
    {
        return $this->fields;
    }

    /**
     * Add a new command to the blueprint.
     *
     * @param string $name
     * @param array  $parameters
     *
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
     * @param string $name
     * @param array  $parameters
     *
     * @return \Illuminate\Support\Fluent
     */
    protected function createCommand($name, array $parameters = [])
    {
        return new Fluent(array_merge(compact('name'), $parameters));
    }

    /**
     * Get the raw DSL statements for the blueprint.
     *
     * @param Grammar $grammar
     *
     * @return array
     */
    public function toDSL(Grammar $grammar)
    {
        $statements = [];

        // Each type of command has a corresponding compiler function on the schema
        // grammar which is used to build the necessary DSL statements to build
        // the blueprint element, so we'll just call that compilers function.
        foreach ($this->commands as $command) {
            $method = 'compile'.ucfirst($command->name);

            if (method_exists($grammar, $method)) {
                if (!is_null($dsl = $grammar->$method($this, $command))) {
                    $statements = array_merge($statements, (array) $dsl);
                }
            }
        }

        return $statements;
    }
}
