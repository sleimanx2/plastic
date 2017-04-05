<?php

namespace Sleimanx2\Plastic;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Sleimanx2\Plastic\Facades\Plastic;
use Sleimanx2\Plastic\Persistence\EloquentPersistence;

/**
 * @method static \Sleimanx2\Plastic\DSL\SearchBuilder search()
 * @method static \Sleimanx2\Plastic\DSL\SuggestionBuilder suggest()
 */
trait Searchable
{
    /**
     * Is indexed in elastic search.
     *
     * @var bool
     */
    public $isDocument = false;

    /**
     * Hit score after querying Elasticsearch.
     *
     * @var null|int
     */
    public $documentScore = null;

    /**
     * Elasticsearch document version.
     *
     * @var null|int
     */
    public $documentVersion = null;

    /**
     * Searchable boot model.
     */
    public static function bootSearchable()
    {
        static::saved(function ($model) {
            if ($model->shouldSyncDocument()) {
                $model->document()->save();
            }
        });

        static::deleted(function ($model) {
            if ($model->shouldSyncDocument()) {
                $model->document()->delete();
            }
        });
    }

    /**
     * Start an elastic persistence query builder.
     *
     * @return EloquentPersistence
     */
    public function document()
    {
        return Plastic::persist()->model($this);
    }

    /**
     * Get the model elastic type.
     *
     * @return string
     */
    public function getDocumentType()
    {
        // if the type is defined use it else return the table name
        if (isset($this->documentType) and !empty($this->documentType)) {
            return $this->documentType;
        }

        return $this->getTable();
    }

    /**
     * Get the model elastic index if available.
     *
     * @return mixed
     */
    public function getDocumentIndex()
    {
        // if a custom index is defined use it else return null
        if (isset($this->documentIndex) and !empty($this->documentIndex)) {
            return $this->documentIndex;
        }
    }

    /**
     * Build the document data with the appropriate method.
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
     * Build the document from a searchable array.
     *
     * @param array $searchable
     *
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
                $result = $result->toDateTimeString();
            } else {
                $result = $this->$value;
            }

            $document[$value] = $result;
        }

        return $document;
    }

    /**
     * Checks if the model content should be auto synced with elastic.
     *
     * @return boolean;
     */
    public function shouldSyncDocument()
    {
        if (property_exists($this, 'syncDocument')) {
            return $this->syncDocument;
        }

        return true;
    }

    /**
     * Handle dynamic method calls into the model.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        if ($method == 'search') {
            //Start an elastic dsl search query builder
            return Plastic::search()->model($this);
        }

        if ($method == 'suggest') {
            //Start an elastic dsl suggest query builder
            return Plastic::suggest()->index($this->getDocumentIndex());
        }

        return parent::__call($method, $parameters);
    }
}
