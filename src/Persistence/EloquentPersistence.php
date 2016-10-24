<?php

namespace Sleimanx2\Plastic\Persistence;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Sleimanx2\Plastic\Connection;
use Sleimanx2\Plastic\Exception\InvalidArgumentException;
use Sleimanx2\Plastic\Exception\MissingArgumentException;
use Sleimanx2\Plastic\Searchable;

class EloquentPersistence
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
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Get the model to persist.
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Set the model to persist.
     *
     * @param Model $model
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function model(Model $model)
    {
        // Check if the model is searchable before setting the query builder model
        $traits = class_uses($model);

        if (!isset($traits[Searchable::class])) {
            throw new InvalidArgumentException(get_class($model).' does not use the searchable trait');
        }

        $this->model = $model;

        return $this;
    }

    /**
     * Save a model instance.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function save()
    {
        $this->exitIfModelNotSet();

        if (!$this->model->exists) {
            throw new \Exception('Model not persisted yet');
        }
        $document = $this->model->getDocumentData();

        $params = [
            'id'    => $this->model->getKey(),
            'type'  => $this->model->getDocumentType(),
            'index' => $this->model->getDocumentIndex(),
            'body'  => $document,
        ];

        return $this->connection->indexStatement($params);
    }

    /**
     * Update a model document.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function update()
    {
        $this->exitIfModelNotSet();

        if (!$this->model->exists) {
            throw new \Exception('Model not persisted yet');
        }

        $document = $this->model->getDocumentData();

        $params = [
            'id'    => $this->model->getKey(),
            'type'  => $this->model->getDocumentType(),
            'index' => $this->model->getDocumentIndex(),
            'body'  => [
                'doc' => $document,
            ],
        ];

        return $this->connection->updateStatement($params);
    }

    /**
     * Delete a model document.
     *
     * @return mixed
     */
    public function delete()
    {
        $this->exitIfModelNotSet();

        $params = [
            'id'    => $this->model->getKey(),
            'type'  => $this->model->getDocumentType(),
            'index' => $this->model->getDocumentIndex(),
        ];

        // check if the document exists before deleting
        if ($this->connection->existsStatement($params)) {
            return $this->connection->deleteStatement($params);
        }

        return true;
    }

    /**
     * Bulk save a collection Models.
     *
     * @param array|Collection $collection
     *
     * @return mixed
     */
    public function bulkSave($collection = [])
    {
        $params = [];

        $defaultIndex = $this->connection->getDefaultIndex();

        foreach ($collection as $item) {
            $modelIndex = $item->getDocumentIndex();

            $params['body'][] = [
                'index' => [
                    '_id'    => $item->getKey(),
                    '_type'  => $item->getDocumentType(),
                    '_index' => $modelIndex ? $modelIndex : $defaultIndex,
                ],
            ];
            $params['body'][] = $item->getDocumentData();
        }

        return $this->connection->bulkStatement($params);
    }

    /**
     * Bulk Delete a collection of Models.
     *
     * @param array|collection $collection
     *
     * @return mixed
     */
    public function bulkDelete($collection = [])
    {
        $params = [];

        $defaultIndex = $this->connection->getDefaultIndex();

        foreach ($collection as $item) {
            $modelIndex = $item->getDocumentIndex();

            $params['body'][] = [
                'delete' => [
                    '_id'    => $item->getKey(),
                    '_type'  => $item->getDocumentType(),
                    '_index' => $modelIndex ? $modelIndex : $defaultIndex,
                ],
            ];
        }

        return $this->connection->bulkStatement($params);
    }

    /**
     * Reindex a collection of Models.
     *
     * @param array|Collection $collection
     *
     * @return mixed
     */
    public function reindex($collection = [])
    {
        $this->bulkDelete($collection);

        return $this->bulkSave($collection);
    }

    /**
     * Function called when the model value is a required.
     */
    private function exitIfModelNotSet()
    {
        if (!$this->model) {
            throw new MissingArgumentException('you should set the model first');
        }
    }
}
