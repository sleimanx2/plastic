<?php

namespace Sleimanx2\Plastic\Persistence;

class EloquentPersistence extends PersistenceAbstract
{
    /**
     * Save a model instance.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function save()
    {
        if (!$this->model->exists) {
            throw new \Exception('Model not persisted yet');
        }
        $document = $this->model->getDocumentData();

        $params = [
            'id'   => $this->model->getKey(),
            'type' => $this->model->getDocumentType(),
            'body' => $document,
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
        if (!$this->model->exists) {
            throw new \Exception('Model not persisted yet');
        }

        $document = $this->model->getDocumentData();

        $params = [
            'id'   => $this->model->getKey(),
            'type' => $this->model->getDocumentType(),
            'body' => [
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
        $params = [
            'id'   => $this->model->getKey(),
            'type' => $this->model->getDocumentType(),
        ];

        return $this->connection->deleteStatement($params);
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

        $index = $this->connection->getDefaultIndex();

        foreach ($collection as $item) {
            $params['body'][] = [
                'index' => [
                    '_id'    => $item->getKey(),
                    '_type'  => $item->getDocumentType(),
                    '_index' => $index,
                ],
            ];
            $params['body'][] = $item->getDocumentData();
        }

        return $this->connection->bulkStatement($params);
    }

    /**
     * Bulk Delete a collection of Models.
     *
     * @param array|collecection $collection
     *
     * @return mixed
     */
    public function bulkDelete($collection = [])
    {
        $params = [];

        $index = $this->connection->getDefaultIndex();

        foreach ($collection as $item) {
            $params['body'][] = [
                'delete' => [
                    '_id'    => $item->getKey(),
                    '_type'  => $item->getDocumentType(),
                    '_index' => $index,
                ],
            ];
        }

        return $this->connection->bulkStatement($params);
    }
}
