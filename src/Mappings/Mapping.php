<?php

namespace Sleimanx2\Plastic\Mappings;

use Sleimanx2\Plastic\Exception\InvalidArgumentException;
use Sleimanx2\Plastic\Exception\MissingArgumentException;
use Sleimanx2\Plastic\Searchable;

abstract class Mapping
{
    /**
     * Eloquent instance.
     *
     * @var \Sleimanx2\Plastic\Searchable
     */
    protected $model;

    /**
     * Index name.
     *
     * @var string|null
     */
    protected $index;

    /**
     * Mapping constructor.
     */
    public function __construct()
    {
        $this->prepareModel();
    }

    /**
     * Gets the index name.
     *
     * @return string|null
     */
    public function index()
    {
        return $this->index;
    }

    /**
     * Sets the index name.
     *
     * @param string|null $index
     */
    public function setIndex($index)
    {
        $this->index = $index;
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

        $traits = class_uses_recursive(get_class($this->model));

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
     * Get the model elastic index.
     *
     * @return mixed
     */
    public function getModelIndex()
    {
        return $this->index() ?: $this->model->getDocumentIndex();
    }
}
