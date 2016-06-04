<?php

namespace Sleimanx2\Plastic\Persistence;


class EloquentPersistence extends PersistenceAbstract
{
    /**
     * Save a model instance
     *
     * @return mixed
     * @throws \Exception
     */
    public function save()
    {
        if (!$this->model->exists) {
            throw new \Exception('Model not persisted yet');
        }
        $document = $this->model->getDocumentData();

        $params = [
            'id'   => $this->model->getKey(),
            'type' => $this->model->getType(),
            'body' => $document,
        ];

        return $this->connection->indexStatement($params);
    }

    /**
     * Update a model document
     *
     * @return mixed
     * @throws Exception
     */
    public function update()
    {
        if (!$this->model->exists) {
            throw new Exception('Model not persisted yet');
        }

        $document = $this->model->getDocumentData();

        $params = [
            'id'   => $this->model->getKey(),
            'type' => $this->model->getType(),
            'body' => [
                'doc' => $document
            ]
        ];

        return $this->connection->updateStatement($params);
    }

    /**
     * Delete a model document
     *
     * @return mixed
     * @throws Exception
     */
    public function delete()
    {
        if (!$this->model->exists) {
            throw new Exception('Model not persisted yet');
        }

        $params = [
            'id'   => $this->model->getKey(),
            'type' => $this->model->getType(),
        ];

        return $this->connection->deleteStatement($params);
    }

    /**
     * Bulk save a collection Models
     *
     * @param array $collection
     * @return mixed
     */
    public function bulkSave(array $collection = [])
    {
        $params = [];

        foreach ($collection as $item) {
            $params['body'][] = [
                'index' => [
                    '_id'    => $item->getKey(),
                    '_type'  => $item->getType(),
                    '_index' => $this->connection->getDefaultIndex(),
                ],
            ];
            $params['body'][] = $item->getDocumentData();
        }

        return $this->connection->bulkStatement($params);
    }

    /**
     * Bulk Delete a collection of Modelss
     *
     * @param array $collection
     * @return mixed
     */
    public function bulkDelete(array $collection = [])
    {
        $params = [];

        foreach ($collection as $item) {
            $params['body'][] = [
                'delete' => [
                    '_id'    => $item->getKey(),
                    '_type'  => $item->getType(),
                    '_index' => $this->connection->getDefaultIndex(),
                ],
            ];
        }

        return $this->connection->bulkStatement($params);
    }
}