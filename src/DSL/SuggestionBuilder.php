<?php

namespace Sleimanx2\Plastic\DSL;

use ONGR\ElasticsearchDSL\Search as Query;
use ONGR\ElasticsearchDSL\Suggest\Suggest;
use Sleimanx2\Plastic\Connection;

class SuggestionBuilder
{
    /**
     * The elastic index to query against.
     *
     * @var string
     */
    public $index;

    /**
     * An instance of DSL query.
     *
     * @var Query
     */
    public $query;

    /**
     * An instance of plastic Connection.
     *
     * @var Connection
     */
    private $connection;

    /**
     * Builder constructor.
     *
     * @param Connection $connection
     * @param Query      $query
     */
    public function __construct(Connection $connection, Query $query = null)
    {
        $this->query = $query;

        $this->connection = $connection;
    }

    /**
     * Set the elastic index to query against.
     *
     * @param string $index
     *
     * @return $this
     */
    public function index($index)
    {
        $this->index = $index;

        return $this;
    }

    /**
     * Return the current elastic index.
     *
     * @return string
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Add a completion suggestion.
     *
     * @param string $name
     * @param string $text
     * @param string $field
     * @param array  $parameters
     *
     * @return $this
     *
     * @internal param $fields
     */
    public function completion($name, $text, $field = 'suggest', $parameters = [])
    {
        $suggestion = new Suggest($name, 'completion', $text, $field, $parameters);

        $this->append($suggestion);

        return $this;
    }

    /**
     * Add a term suggestion.
     *
     * @param string $name
     * @param string $text
     * @param string $field
     * @param array  $parameters
     *
     * @return $this
     */
    public function term($name, $text, $field = '_all', array $parameters = [])
    {
        $suggestion = new Suggest($name, 'term', $text, $field, $parameters);

        $this->append($suggestion);

        return $this;
    }

    /**
     * Return the DSL query.
     *
     * @return array
     */
    public function toDSL()
    {
        return $this->query->toArray()['suggest'];
    }

    /**
     * Execute the suggest query against elastic and return the raw result if model not set.
     *
     * @return array
     */
    public function get()
    {
        return $this->connection->suggestStatement(
            [
                'index' => $this->getIndex(),
                'body'  => $this->toDSL(),
            ]
        );
    }

    /**
     * Returns the connection instance.
     *
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Append a suggestion to query.
     *
     * @param \ONGR\ElasticsearchDSL\BuilderInterface $suggestion
     */
    public function append($suggestion)
    {
        $this->query->addSuggest($suggestion);
    }
}
