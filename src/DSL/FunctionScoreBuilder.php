<?php

namespace Sleimanx2\Plastic\DSL;

use ONGR\ElasticsearchDSL\Query\Compound\FunctionScoreQuery;

/**
 * Class FunctionScoreBuilder.
 */
class FunctionScoreBuilder
{
    /**
     * @var FunctionScoreQuery
     */
    private $query;

    /**
     * FunctionScoreBuilder constructor.
     *
     * @param SearchBuilder $search
     * @param array         $parameters
     */
    public function __construct(SearchBuilder $search, $parameters = [])
    {
        $this->query = new FunctionScoreQuery($search->query->getQueries(), $parameters);
    }

    /**
     * @param $field
     * @param $factor
     * @param string $modifier
     */
    public function field($field, $factor, $modifier = 'none')
    {
        $this->query->addFieldValueFactorFunction($field, $factor, $modifier);
    }

    /**
     * @param $type
     * @param $field
     * @param $function
     * @param array $options
     */
    public function decay($type, $field, $function, $options = [])
    {
        $this->query->addDecayFunction($type, $field, $function, $options);
    }

    /**
     * @param $weight
     */
    public function weight($weight)
    {
        $this->query->addWeightFunction($weight);
    }

    /**
     * @param $seed
     */
    public function random($seed = null)
    {
        $this->query->addRandomFunction($seed);
    }

    /**
     * @param $inline
     * @param array $params
     * @param array $options
     */
    public function script($inline, $params = [], $options = [])
    {
        $this->query->addScriptScoreFunction($inline, $params, $options);
    }

    /**
     * @param $functions
     */
    public function simple($functions)
    {
        $this->query->addSimpleFunction($functions);
    }

    /**
     * Return the DSL query.
     *
     * @return array
     */
    public function toDSL()
    {
        return $this->query->toArray();
    }

    /**
     * @return FunctionScoreQuery
     */
    public function getQuery()
    {
        return $this->query;
    }
}
