<?php

namespace Sleimanx2\Plastic;


use Sleimanx2\Plastic\DSL\Builder;
use Sleimanx2\Plastic\Facades\Plastic;

trait Searchable
{

    public static function bootSearchable()
    {
        static::creating(function ($model) {

            // fill the fields that should be mapped

            // index the model

        });

        static::updating(function ($model) {

            // fill the fields that should be mapped

            // index the model

            // collect related models that should be updated

            // fill each related models with its new data

            // bulk update related models

        });

        static::deleting(function ($model) {

            // fill the fields that should be mapped

            // index the model

            // collect related models that should be updated

            // fill each related models with its new data

            // bulk update related models

        });
    }

    /**
     * Start an elastic dsl query builder
     *
     * @return Builder
     */
    public function search()
    {
        return $this->dsl = Plastic::type($this->getType());
    }
    /**
     * Get the model elastic type
     *
     * @return string
     */
    public function getType()
    {
        // if the type is defined use it else return the table name
        if (isset($this->type)) {
            return $this->type;
        }

        return $this->getTable();
    }
}