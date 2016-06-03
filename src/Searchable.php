<?php

namespace Sleimanx2\Plastic;


use Sleimanx2\Plastic\DSL\Builder;
use Sleimanx2\Plastic\Facades\Plastic;

trait Searchable
{

    /**
     * Is indexed in elastic search
     *
     * @var bool
     */
    protected $isDocument = false;

    /**
     * Document Score
     *
     * Hit score after querying Elasticsearch.
     *
     * @var null|int
     */
    protected $documentScore = null;

    /**
     * Document Version
     *
     * Elasticsearch document version.
     *
     * @var null|int
     */
    protected $documentVersion = null;


    /**
     * Searchable boot model
     */
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
        return $this->dsl = Plastic::model($this);
    }

    public function addToIndex()
    {
        if (!$this->exists) {
            throw new \Exception('Model not persisted yet');
        }




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