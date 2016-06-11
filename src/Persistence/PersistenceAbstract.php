<?php

namespace Sleimanx2\Plastic\Persistence;

use Illuminate\Database\Eloquent\Model;
use Sleimanx2\Plastic\Connection;

abstract class PersistenceAbstract
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var Model
     */
    protected $model;

    /**
     * PersistenceAbstract constructor.
     *
     * @param Connection $connection
     * @param Model $model
     */
    public function __construct(Connection $connection, Model $model)
    {
        $this->connection = $connection;
        $this->model = $model;
    }

    /**
     * Save a model instance
     *
     * @return mixed
     */
    abstract public function save();

    /**
     * Update a model document
     *
     * @return mixed
     */
    abstract public function update();

    /**
     * Delete a model document
     *
     * @return mixed
     */
    abstract public function delete();

    /**
     * Bulk save a collection Models
     *
     * @param array $collection
     * @return mixed
     */
    abstract public function bulkSave(array $collection = []);

    /**
     * Bulk Delete a collection of Models
     *
     * @param array $collection
     * @return mixed
     */
    abstract public function bulkDelete(array $collection = []);

    /**
     * Reindex a collection of Models
     *
     * @param array $collection
     * @return mixed
     */
    public function reindex(array $collection = [])
    {
        $this->bulkDelete($collection);

        return $this->bulkSave($collection);
    }
}