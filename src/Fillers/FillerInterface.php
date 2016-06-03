<?php

namespace Sleimanx2\Plastic\Fillers;

use Illuminate\Database\Eloquent\Model;
use Sleimanx2\Plastic\PlasticResults as  Result;

interface FillerInterface
{
    /**
     * Fill the results hists into Model
     *
     * @param Model $model
     * @param Result $results
     * @return mixed
     */
    public function fill(Model $model, Result $results);
}