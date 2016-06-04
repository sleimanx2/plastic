<?php

namespace Sleimanx2\Plastic;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Sleimanx2\Plastic\DSL\Builder;
use Sleimanx2\Plastic\Facades\Plastic;
use Sleimanx2\Plastic\Persistence\EloquentPersistence;

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
            $model->documet()->save();
        });

        static::updating(function ($model) {
            $model->document()->update();
        });

        static::deleting(function ($model) {
            $model->document()->delete();
        });
    }

    /**
     * Start an elastic dsl query builder
     *
     * @return Builder
     */
    public function search()
    {
        return Plastic::model($this);
    }


    /**
     * Start an elastic persistence handler
     *
     * @return EloquentPersistence
     */
    public function document()
    {
        return Plastic::persistence($this);
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

    /**
     * Build the document data with the appropriate method
     *
     * @return array
     */
    public function getDocumentData()
    {
        // If the model contain a buildDocument function
        // use it to build the document
        if (method_exists($this, 'buildDocument')) {
            $document = $this->buildDocument();

            return $document;
        }
        // If a searchable array is provided build
        // the document from the given array
        elseif (is_array($this->searchable)) {
            $document = $this->buildDocumentFromArray($this->searchable);

            return $document;
        } else {
            $document = $this->toArray();

            return $document;
        }
    }

    /**
     * Build the document from a searchable array
     *
     * @param array $searchable
     * @return array
     */
    protected function buildDocumentFromArray(array $searchable)
    {
        $document = [];

        foreach ($searchable as $value) {

            $result = $this->$value;

            if ($result instanceof Collection) {

                $result = $result->toArray();

            } elseif ($result instanceof Carbon) {

                $result = $result->format('c');

            } else {

                $result = $this->$value;
            }

            $document[$value] = $result;
        }

        return $document;
    }

}