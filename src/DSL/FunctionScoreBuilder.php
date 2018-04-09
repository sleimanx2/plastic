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
     * @param null   $query
     */
    public function field($field, $factor, $modifier = 'none', $query = null)
    {
        $this->query->addFieldValueFactorFunction($field, $factor, $modifier, $query);
    }

    /**
     * @param $type
     * @param $field
     * @param $function
     * @param array $options
     * @param null  $query
     */
    public function decay($type, $field, $function, $options = [], $query = null)
    {
        $this->query->addDecayFunction($type, $field, $function, $options, $query);
    }

    /**
     * @param $weight
     * @param null $query
     */
    public function weight($weight, $query = null)
    {
        $this->query->addWeightFunction($weight, $query);
    }

    /**
     * @param $seed
     * @param null $query
     */
    public function random($seed = null, $query = null)
    {
        $this->query->addRandomFunction($seed, $query);
    }

    /**
     * @param $inline
     * @param array $params
     * @param array $options
     * @param null  $query
     */
    public function script($inline, $params = [], $options = [], $query = null)
    {
        $this->query->addScriptScoreFunction($inline, $params, $options, $query);
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
