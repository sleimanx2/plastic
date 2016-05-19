<?php

namespace Sleimanx2\Plastic;


trait Searchable
{
    /**
     * Return the elastic type used by the model
     */
    public function getType()
    {
        if (isset($this->type)) {
            return $this->type;
        }

        $this->getTable();
    }
}