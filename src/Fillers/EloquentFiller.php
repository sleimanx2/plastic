<?php

namespace Sleimanx2\Plastic\Fillers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use ReflectionMethod;
use Sleimanx2\Plastic\PlasticResult as Result;

class EloquentFiller implements FillerInterface
{
    /**
     * Fill the results hists into Model.
     *
     * @param Model  $model
     * @param Result $result
     *
     * @return mixed|void
     */
    public function fill(Model $model, Result $result)
    {
        $hits = $result->hits()->map(function ($hit) use ($model) {
            return $this->fillModel($model, $hit);
        });

        $result->setHits($hits);
    }

    /**
     * New From Hit Builder.
     *
     * Variation on newFromBuilder. Instead, takes
     *
     * @param $model
     * @param array $hit
     *
     * @return static
     */
    public function fillModel(Model $model, $hit = [])
    {
        $key_name = $model->getKeyName();

        $attributes = $hit['_source'];

        if (isset($hit['_id'])) {
            $attributes[$key_name] = is_numeric($hit['_id']) ? intval($hit['_id']) : $hit['_id'];
        }

        // Add fields to attributes
        if (isset($hit['fields'])) {
            foreach ($hit['fields'] as $key => $value) {
                $attributes[$key] = $value;
            }
        }

        $instance = $this->newFromBuilderRecursive($model, $attributes);

        // In addition to setting the attributes
        // from the index, we will set the score as well.
        $instance->documentScore = $hit['_score'];
        // This is now a model created
        // from an Elasticsearch document.
        $instance->isDocument = true;
        // Set our document version if it's
        if (isset($hit['_version'])) {
            $instance->documentVersion = $hit['_version'];
        }

        return $instance;
    }

    /**
     * Fill a model with form an elastic hit.
     *
     * @param Model    $model
     * @param array    $attributes
     * @param Relation $parentRelation
     *
     * @return mixed
     */
    public function newFromBuilderRecursive(Model $model, array $attributes = [], Relation $parentRelation = null)
    {
        $instance = $model->newInstance([], $exists = true);

        // fill the instance attributes with checking
        $instance->unguard();
        $instance->fill($attributes);
        $instance->reguard();
        // Load relations recursive
        $this->loadRelationsAttributesRecursive($instance);

        // Load pivot
        $this->loadPivotAttribute($instance, $parentRelation);

        return $instance;
    }

    /**
     * Get the relations attributes from a model.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     */
    protected function loadRelationsAttributesRecursive(Model $model)
    {
        $attributes = $model->getAttributes();

        foreach ($attributes as $key => $value) {
            if (method_exists($model, $key)) {
                $reflection_method = new ReflectionMethod($model, $key);

                if ($reflection_method->class != "Illuminate\Database\Eloquent\Model") {
                    $relation = $model->$key();

                    if ($relation instanceof Relation) {

                        // Get the relation models/model if value is not null
                        if ($value === null) {
                            $models = null;
                        } else {

                          // Check if the relation field is single model or collections
                          if (!$multiLevelRelation = $this->isMultiLevelArray($value)) {
                              $value = [$value];
                          }

                            $models = $this->hydrateRecursive($relation->getModel(), $value, $relation);

                            if (!$multiLevelRelation) {
                                $models = $models->first();
                            }
                        }

                        // Unset attribute before setting relation
                        unset($model[$key]);

                        // Set the relation value
                        $model->setRelation($key, $models);
                    }
                }
            }
        }
    }

    /**
     * Create a collection of models from plain arrays recursive.
     *
     * @param Model    $model
     * @param Relation $parentRelation
     * @param array    $items
     *
     * @return Collection
     */
    protected function hydrateRecursive(Model $model, array $items, Relation $parentRelation = null)
    {
        $instance = $model;

        $items = array_map(function ($item) use ($instance, $parentRelation) {
            return $this->newFromBuilderRecursive($instance, $item, $parentRelation);
        }, $items);

        return $instance->newCollection($items);
    }

    /**
     * Get the pivot attribute from a model.
     *
     * @param \Illuminate\Database\Eloquent\Model              $model
     * @param \Illuminate\Database\Eloquent\Relations\Relation $parentRelation
     */
    public function loadPivotAttribute(Model $model, Relation $parentRelation = null)
    {
        $attributes = $model->getAttributes();
        foreach ($attributes as $key => $value) {
            if ($key === 'pivot') {
                unset($model[$key]);
                $pivot = $parentRelation->newExistingPivot($value);
                $model->setRelation($key, $pivot);
            }
        }
    }

    /**
     * Check if an array is multi-level array like [[id], [id], [id]].
     *
     * For detect if a relation field is single model or collections.
     *
     * @param array $array
     *
     * @return bool
     */
    private function isMultiLevelArray(array $array)
    {
        foreach ($array as $key => $value) {
            if (!is_array($value)) {
                return false;
            }
        }

        return true;
    }
}
