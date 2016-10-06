<?php

namespace Sleimanx2\Plastic\Map;

use Illuminate\Support\Fluent;

class Grammar
{
    /**
     * Create a map body.
     *
     * @param Blueprint $blueprint
     * @param Fluent    $command
     *
     * @return array
     */
    public function compileCreate(Blueprint $blueprint, Fluent $command)
    {
        $fields = $blueprint->getFields();

        $statement = $this->compileFields($fields);

        return $statement;
    }

    /**
     * Compile an integer map.
     *
     * @param Fluent $fluent
     *
     * @return array
     */
    public function compileInteger(Fluent $fluent)
    {
        return $this->compileNumeric($fluent);
    }

    /**
     * Compile a long map.
     *
     * @param Fluent $fluent
     *
     * @return array
     */
    public function compileLong(Fluent $fluent)
    {
        return $this->compileNumeric($fluent);
    }

    /**
     * Compile a short map.
     *
     * @param Fluent $fluent
     *
     * @return array
     */
    public function compileShort(Fluent $fluent)
    {
        return $this->compileNumeric($fluent);
    }

    /**
     * Compile a byte map.
     *
     * @param Fluent $fluent
     *
     * @return array
     */
    public function compileByte(Fluent $fluent)
    {
        return $this->compileNumeric($fluent);
    }

    /**
     * Compile a double map.
     *
     * @param Fluent $fluent
     *
     * @return array
     */
    public function compileDouble(Fluent $fluent)
    {
        return $this->compileNumeric($fluent);
    }

    /**
     * Compile a binary map.
     *
     * @param Fluent $fluent
     *
     * @return array
     */
    public function compileBinary(Fluent $fluent)
    {
        $map = [
            'type'       => 'binary',
            'doc_values' => $fluent->doc_values,
            'store'      => $fluent->store,
        ];

        return $this->formatMap($map);
    }

    /**
     * Compile float map.
     *
     * @param Fluent $fluent
     *
     * @return array
     */
    public function compileFloat(Fluent $fluent)
    {
        return $this->compileNumeric($fluent);
    }

    /**
     * Compile a date map.
     *
     * @param Fluent $fluent
     *
     * @return array
     */
    public function compileDate(Fluent $fluent)
    {
        $map = [
            'type'             => 'date',
            'boost'            => $fluent->boost,
            'doc_values'       => $fluent->doc_values,
            'format'           => $fluent->format,
            'ignore_malformed' => $fluent->ignore_malformed,
            'include_in_all'   => $fluent->include_in_all,
            'index'            => $fluent->index,
            'null_value'       => $fluent->null_value,
            'precision_step'   => $fluent->precision_step,
            'store'            => $fluent->store,
        ];

        return $this->formatMap($map);
    }

    /**
     * Compile a boolean map.
     *
     * @param Fluent $fluent
     *
     * @return array
     */
    public function compileBoolean(Fluent $fluent)
    {
        $map = [
            'type'       => 'boolean',
            'boost'      => $fluent->boost,
            'doc_values' => $fluent->doc_values,
            'index'      => $fluent->index,
            'null_value' => $fluent->null_value,
            'store'      => $fluent->store,
        ];

        return $this->formatMap($map);
    }

    /**
     * Compile a geo point map.
     *
     * @param Fluent $fluent
     *
     * @return array
     */
    public function compilePoint(Fluent $fluent)
    {
        $map = [
            'type'              => 'geo_point',
            'geohash'           => $fluent->geohash,
            'geohash_precision' => $fluent->geohash_precision,
            'geohash_prefix'    => $fluent->geohash_prefix,
            'ignore_malformed'  => $fluent->ignore_malformed,
            'lat_lon'           => $fluent->lat_lon,
            'precision_step'    => $fluent->precision_step,
        ];

        return $this->formatMap($map);
    }

    /**
     * Compile a geo shape map.
     *
     * @param Fluent $fluent
     *
     * @return array
     */
    public function compileShape(Fluent $fluent)
    {
        $map = [
            'type'               => 'geo_shape',
            'tree'               => $fluent->tree,
            'precision'          => $fluent->precision,
            'tree_levels'        => $fluent->tree_levels,
            'strategy'           => $fluent->strategy,
            'distance_error_pct' => $fluent->distance_error_pct,
            'orientation'        => $fluent->orientation,
            'points_only'        => $fluent->points_only,
        ];

        return $this->formatMap($map);
    }

    /**
     * Compile an ip map.
     *
     * @param Fluent $fluent
     *
     * @return array
     */
    public function compileIp(Fluent $fluent)
    {
        $map = [
            'type'           => $fluent->type,
            'boost'          => $fluent->boost,
            'doc_values'     => $fluent->doc_values,
            'include_in_all' => $fluent->include_in_all,
            'index'          => $fluent->index,
            'null_value'     => $fluent->null_value,
            'precision_step' => $fluent->precision_step,
            'store'          => $fluent->store,
        ];

        return $this->formatMap($map);
    }

    /**
     * Compile a completion map.
     *
     * @param Fluent $fluent
     *
     * @return array
     */
    public function compileCompletion(Fluent $fluent)
    {
        $map = [
            'type'                => 'completion',
            'analyzer'            => $fluent->analyzer,
            'search_analyzer'     => $fluent->search_analyzer,
            'payloads'            => $fluent->payloads,
            'preserve_separators' => $fluent->preserve_separators,
            'max_input_length'    => $fluent->max_input_length,
        ];

        return $this->formatMap($map);
    }

    /**
     * Compile a completion map.
     *
     * @param Fluent $fluent
     *
     * @return array
     */
    public function compileToken_count(Fluent $fluent)
    {
        $map = [
            'type'           => 'token_count',
            'boost'          => $fluent->boost,
            'doc_values'     => $fluent->doc_values,
            'include_in_all' => $fluent->include_in_all,
            'index'          => $fluent->index,
            'null_value'     => $fluent->null_value,
            'precision_step' => $fluent->precision_step,
            'store'          => $fluent->store,
        ];

        return $this->formatMap($map);
    }

    /**
     * Compile a string map.
     *
     * @param Fluent $fluent
     *
     * @return array
     */
    public function compileString(Fluent $fluent)
    {
        $map = [
            'type'                   => 'string',
            'analyzer'               => $fluent->analyzer,
            'boost'                  => $fluent->boost,
            'doc_values'             => $fluent->doc_values,
            'fielddata'              => $fluent->fielddata,
            'fields'                 => $fluent->fields,
            'ignore_above'           => $fluent->ignore_above,
            'include_in_all'         => $fluent->include_in_all,
            'index'                  => $fluent->index,
            'index_options'          => $fluent->index_options,
            'norms'                  => $fluent->norms,
            'position_increment_gap' => $fluent->position_increment_gap,
            'store'                  => $fluent->store,
            'search_analyzer'        => $fluent->search_analyzer,
            'search_quote_analyzer'  => $fluent->search_quote_analyzer,
            'similarity'             => $fluent->similarity,
            'term_vector'            => $fluent->term_vector,
        ];

        return $this->formatMap($map);
    }

    /**
     * Compile a numeric map.
     *
     * @param Fluent $fluent
     *
     * @return array
     */
    public function compileNumeric(Fluent $fluent)
    {
        $map = [
            'type'             => $fluent->type,
            'coerce'           => $fluent->coerce,
            'boost'            => $fluent->boost,
            'doc_values'       => $fluent->doc_values,
            'ignore_malformed' => $fluent->ignore_malformed,
            'include_in_all'   => $fluent->include_in_all,
            'index'            => $fluent->index,
            'null_value'       => $fluent->null_value,
            'precision_step'   => $fluent->precision_step,
        ];

        return $this->formatMap($map);
    }

    /**
     * Compile a nested map.
     *
     * @param Fluent $fluent
     *
     * @return array
     */
    public function compileNested(Fluent $fluent)
    {
        $blueprint = new Blueprint($fluent->type);

        /* @var \Closure $callback */
        $callback = $fluent->callback;

        if (is_callable($callback)) {
            $callback($blueprint);
        }

        return [
            'type'       => 'nested',
            'properties' => $this->compileFields($blueprint->getFields()),
        ];
    }

    /**
     * Compile a object map.
     *
     * @param Fluent $fluent
     *
     * @return array
     */
    public function compileObject(Fluent $fluent)
    {
        $blueprint = new Blueprint($fluent->type);

        /* @var \Closure $callback */
        $callback = $fluent->callback;

        if (is_callable($callback)) {
            $callback($blueprint);
        }

        return [
            'properties' => $this->compileFields($blueprint->getFields()),
        ];
    }

    /**
     * Format the map array for submission.
     *
     * @param array $map
     *
     * @return array
     */
    protected function formatMap(array $map)
    {
        return array_filter($map);
    }

    /**
     * Compile an array of fluent fields.
     *
     * @param $fields
     *
     * @return array
     */
    public function compileFields($fields)
    {
        $statement = [];

        foreach ($fields as $field) {
            $method = 'compile'.ucfirst($field->type);

            if (method_exists($this, $method)) {
                if (!empty($map = $this->$method($field))) {
                    $statement[$field->name] = $map;
                }
            }
        }

        return $statement;
    }
}
