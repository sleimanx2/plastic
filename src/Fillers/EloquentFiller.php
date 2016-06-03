<?php

namespace Sleimanx2\Plastic\Fillers;

use Illuminate\Database\Eloquent\Model;
use Sleimanx2\Plastic\PlasticResults as Result;

class EloquentFiller implements FillerInterface
{
    /**
     * Fill the results hists into Model
     *
     * @param Model $model
     * @param Result $results
     * @return mixed|void
     */
    public function fill(Model $model, Result $results)
    {
        $results->hits = $results->hits()->map(function ($hit) use ($model) {
            return $this->fillModel($model, $hit);
        });
    }

    /**
     * Fill a model with form an elastic hit
     *
     * @param $hit
     * @param $model
     * @return mixed
     */
    public function fillModel($model, $hit)
    {
        $instance = $model->newInstance([], true);
        $attributes = $hit['_source'];
        // Add fields to attributes
        if (isset($hit['fields'])) {
            foreach ($hit['fields'] as $key => $value) {
                $attributes[$key] = $value;
            }
        }
        $instance->setRawAttributes((array)$attributes, true);
        // Looping through the attributes to map related fields to their model
        foreach ($attributes as $attribute => $value) {

            // If value is an array it could be a relation candidate.
            if (is_array($value)) {

                // If the attribute key is a function this means its a relation
                if (method_exists($instance, $attribute)) {

                    $model = $instance->$attribute()->getModel();
                    // Allowing mass assignment ...
                    $model->unguard();
                    // If multy value loop and fill else fill
                    if (array_keys($value) === range(0, count($value) - 1) or empty($value)) {
                        $instance->$attribute = collect();
                        foreach ($value as $item) {
                            $newItem = new $model();
                            $newItem->fill($item);
                            $instance->$attribute->push($newItem);
                        }
                    } else {
                        $instance->$attribute = $model->fill($value);
                    }
                }
            }
        }

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
}