<?php

namespace Sleimanx2\Plastic\Map;

use Closure;

class Blueprint
{
    /**
     * The type the blueprint describes.
     *
     * @var string
     */
    protected $type;

    /**
     * The fields that should be mapped.
     *
     * @var array
     */
    protected $fields = [];

    /**
     * Blueprint constructor.
     *
     * @param $type
     * @param Closure|null $callback
     */
    public function __construct($type, Closure $callback = null)
    {
        $this->type = $type;

        if (!is_null($callback)) {
            $callback($this);
        }
    }


}