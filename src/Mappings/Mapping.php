<?php

namespace Sleimanx2\Plastic\Mappings;

use Illuminate\Database\Eloquent\Model;
use Sleimanx2\Plastic\Exception\InvalidArgumentException;
use Sleimanx2\Plastic\Exception\MissingArgumentException;
use Sleimanx2\Plastic\Searchable;

abstract class Mapping
{
    /**
     * Eloquent instance.
     *
     * @var Model
     */
    protected $model;

    /**
     * Mapping constructor.
     */
    public function __construct()
    {
        $this->prepareModel();
    }

    /**
     * Validate the given model and create a new instance.
     */
    protected function prepareModel()
    {
        if (!$this->model) {
            throw new MissingArgumentException('model property should be filled');
        }

        $this->model = new $this->model();

        $traits = class_uses($this->model);

        if (!isset($traits[Searchable::class])) {
            throw new InvalidArgumentException(get_class($this->model).' does not use the searchable trait');
        }
    }

    /**
     * Get the model elastic type.
     *
     * @return mixed
     */
    public function getModelType()
    {
        return $this->model->getDocumentType();
    }

    /**
     * Get the model elastic type.
     *
     * @return mixed
     */
    public function getModelIndex()
    {
        return $this->model->getDocumentIndex();
    }
}
