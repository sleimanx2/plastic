<?php

namespace Sleimanx2\Plastic\Map;


use Illuminate\Support\Fluent;

class Grammar
{
    /**
     * Create a map body
     *
     * @param Blueprint $blueprint
     * @param Fluent $command
     * @return array
     */
    public function compileCreate(Blueprint $blueprint, Fluent $command)
    {
        $fields = $blueprint->getFields();

        $statement = [];

        foreach ($fields as $field) {

            $method = 'compile' . ucfirst($field->type);

            if (method_exists($this, $method)) {
                if (!empty($map = $this->$method($field))) {
                    $statement[$command->type][] = $map;
                }
            }
        }

        return $statement;
    }

    /**
     * Compile an integer map
     *
     * @param Fluent $fluent
     * @return array
     */
    public function compileInteger(Fluent $fluent)
    {
        return $this->compileNumeric($fluent);
    }

    /**
     * Add a long numeric field to the map
     *
     * @param Fluent $fluent
     * @return array
     */
    public function compileLong(Fluent $fluent)
    {
        return $this->compileNumeric($fluent);
    }

    /**
     * Add a short numeric field to the map
     *
     * @param Fluent $fluent
     * @return array
     */
    public function compileShort(Fluent $fluent)
    {
        return $this->compileNumeric($fluent);
    }

    /**
     * Add a byte numeric field to the map
     *
     * @param Fluent $fluent
     * @return array
     */
    public function compileByte(Fluent $fluent)
    {
        return $this->compileNumeric($fluent);
    }

    /**
     * Add a double field to the map
     *
     * @param Fluent $fluent
     * @return array
     */
    public function compileDouble(Fluent $fluent)
    {
        return $this->compileNumeric($fluent);
    }

    /**
     * Add a binary field to the map
     *
     * @param Fluent $fluent
     * @return array
     */
    public function compileBinary(Fluent $fluent)
    {
        return $this->compileNumeric($fluent);
    }


    /**
     * Add a float field to the map
     *
     * @param Fluent $fluent
     * @return array
     */
    public function compileFloat(Fluent $fluent)
    {
        return $this->compileNumeric($fluent);
    }

    /**
     * Compile a string map
     *
     * @param Fluent $fluent
     * @return array
     */
    public function compileString(Fluent $fluent)
    {
        $map = [
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
            'term_vector'            => $fluent->term_vector
        ];

        return $this->formatMap($map);
    }


    /**
     * Compile a numeric map
     *
     * @param Fluent $fluent
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
            'precision_step'   => $fluent->precision_step
        ];

        return $this->formatMap($map);
    }


    /**
     * Format the map array for submission
     *
     * @param array $map
     * @return array
     */
    protected function formatMap(array $map)
    {
        return array_filter($map);
    }


}