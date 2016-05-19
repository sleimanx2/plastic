<?php

namespace Sleimanx2\Plastic\Mappings;

use Sleimanx2\Plastic\Exception\InvalidArgumentException;
use Sleimanx2\Plastic\Searchable;

abstract class Mapping
{

    /**
     * Elastic type that should be mapped
     *
     * @var string
     */
    protected $type = '';

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
        $this->model = new $this->model;

        $traits = class_uses($this->model);

        if (!isset($traits[Searchable::class])) {
            throw new InvalidArgumentException(get_class($this->model) . ' does not use the searchable trait');
        }

        $this->type = $this->model->getType();
    }
}