<?php

namespace Sleimanx2\Plastic\Mappings;

use Illuminate\Database\ConnectionResolverInterface as Resolver;

/**
 * Mapping log repository.
 */
class Mappings
{
    /**
     * The database connection resolver instance.
     *
     * @var \Illuminate\Database\ConnectionResolverInterface
     */
    protected $resolver;

    /**
     * The name of the mappings table.
     *
     * @var string
     */
    protected $table;

    /**
     * The name of the database connection to use.
     *
     * @var string
     */
    protected $connection;

    /**
     * Create a new database mapping repository instance.
     *
     * @param \Illuminate\Database\ConnectionResolverInterface $resolver
     * @param string                                           $table
     */
    public function __construct(Resolver $resolver, $table)
    {
        $this->table = $table;
        $this->resolver = $resolver;
    }

    /**
     * Get the ran mappings.
     *
     * @return array
     */
    public function getRan()
    {
        $result = $this->table()
            ->orderBy('batch', 'asc')
            ->orderBy('mapping', 'asc')
            ->pluck('mapping');

        if (is_array($result)) {
            return $result;
        }

        return $result->toArray();
    }

    /**
     * Get the last mapping batch.
     *
     * @return array
     */
    public function getLast()
    {
        $result = $this->table()
            ->where('batch', $this->getLastBatchNumber())
            ->orderBy('mapping', 'desc')
            ->get();

        if (is_array($result)) {
            return $result;
        }

        return $result->toArray();
    }

    /**
     * Log that a mapping was run.
     *
     * @param $file
     * @param $batch
     */
    public function log($file, $batch)
    {
        $record = ['mapping' => $file, 'batch' => $batch];

        $this->table()->insert($record);
    }

    /**
     * Remove mapping from the log.
     *
     * @param object $mapping
     */
    public function delete($mapping)
    {
        $this->table()->where('mapping', $mapping->mapping)->delete();
    }

    /**
     * Remove all mapping logs from the repository.
     */
    public function reset()
    {
        $this->table()->truncate();
    }

    /*
     * Get the next mapping batch number
     *
     * @return float|int
     */
    public function getNextBatchNumber()
    {
        return $this->getLastBatchNumber() + 1;
    }

    /**
     * Get the next mapping batch number.
     *
     * @return float|int
     */
    public function getLastBatchNumber()
    {
        return $this->table()->max('batch');
    }

    /**
     * Create the mapping repository data store.
     */
    public function createRepository()
    {
        $this->schema()->create($this->table, function ($table) {

            // The mappings table is responsible for keeping track of which of the
            // mappings have actually run for the application. We'll create the
            // table to hold the mapping file's path as well as the batch ID.
            $table->increments('id');

            $table->string('mapping');

            $table->integer('batch');
        });
    }

    /**
     * Check it the repository table exits.
     *
     * @return mixed
     */
    public function exits()
    {
        return $this->schema()->hasTable($this->table);
    }

    /**
     * get a schema builder instance.
     *
     * @return \Illuminate\Database\Schema\Builder
     */
    protected function schema()
    {
        return $this->getConnection()->getSchemaBuilder();
    }

    /**
     * Get a query builder for the mapping table.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    protected function table()
    {
        return $this->getConnection()->table($this->table);
    }

    /**
     * Resolve the database connection instance.
     *
     * @return \Illuminate\Database\ConnectionInterface
     */
    public function getConnection()
    {
        return $this->resolver->connection($this->connection);
    }

    /**
     * Get the connection resolver instance.
     *
     * @return \Illuminate\Database\ConnectionResolverInterface
     */
    public function getConnectionResolver()
    {
        return $this->resolver;
    }

    /**
     * Set the information source to gather data.
     *
     * @param string $name
     *
     * @return void
     */
    public function setSource($name)
    {
        $this->connection = $name;
    }
}
