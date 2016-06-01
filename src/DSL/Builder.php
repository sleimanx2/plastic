<?php

namespace Sleimanx2\Plastic\DSL;

use ONGR\ElasticsearchDSL\Query\CommonTermsQuery;
use ONGR\ElasticsearchDSL\Query\ConstantScoreQuery;
use ONGR\ElasticsearchDSL\Query\FuzzyQuery;
use ONGR\ElasticsearchDSL\Query\IdsQuery;
use ONGR\ElasticsearchDSL\Query\MatchAllQuery;
use ONGR\ElasticsearchDSL\Query\MatchQuery;
use ONGR\ElasticsearchDSL\Query\MoreLikeThisQuery;
use ONGR\ElasticsearchDSL\Query\MultiMatchQuery;
use ONGR\ElasticsearchDSL\Query\NestedQuery;
use ONGR\ElasticsearchDSL\Query\PrefixQuery;
use ONGR\ElasticsearchDSL\Query\QueryStringQuery;
use ONGR\ElasticsearchDSL\Query\RangeQuery;
use ONGR\ElasticsearchDSL\Query\RegexpQuery;
use ONGR\ElasticsearchDSL\Query\SimpleQueryStringQuery;
use ONGR\ElasticsearchDSL\Query\TermQuery;
use ONGR\ElasticsearchDSL\Query\TermsQuery;
use ONGR\ElasticsearchDSL\Query\WildcardQuery;
use ONGR\ElasticsearchDSL\Search as Query;
use ONGR\ElasticsearchDSL\Suggest\CompletionSuggest;
use Sleimanx2\Plastic\Connection;

class Builder
{

    /**
     * bool query states
     */
    const MUST = 'must';

    const MUST_NOT = 'must_not';

    const SHOULD = 'should';

    const FILTER = 'filter';

    /**
     * An instance of DSL query
     *
     * @var Query
     */
    public $query;

    /**
     * An instance of plastic Connection
     *
     * @var Connection
     */
    protected $connection;

    /**
     * Query filtering state
     *
     * @var bool
     */
    protected $filtering = false;

    /**
     * Query bool state
     *
     * @var string
     */
    protected $boolState = self::MUST;

    /**
     * The type which the query is targeting.
     *
     * @var string
     */
    public $from;

    /**
     * Builder constructor.
     *
     * @param Connection $connection
     * @param Query $grammar
     */
    public function __construct(Connection $connection, Query $grammar = null)
    {
        $this->connection = $connection;
        $this->query = $grammar ?: $connection->getDSLGrammar();
    }

    /**
     * Set the type to query from
     *
     * @param $type
     * @return $this
     */
    public function from($type)
    {
        $this->from = $type;

        return $this;
    }

    /**
     * Switch to a should statement
     */
    public function should()
    {
        $this->boolState = self::SHOULD;

        return $this;
    }

    /**
     * Switch to a must statement
     */
    public function must()
    {
        $this->boolState = self::MUST;

        return $this;
    }

    /**
     * Switch to a must not statement
     */
    public function mustNot()
    {
        $this->boolState = self::MUST_NOT;

        return $this;
    }

    /**
     * Switch to a filter query
     */
    public function filter()
    {
        $this->filtering = true;

        return $this;
    }

    /**
     * Switch to a regular query
     */
    public function query()
    {
        $this->filtering = false;

        return $this;
    }

    /**
     * Add an ids query
     *
     * @param array | string $ids
     * @return $this
     */
    public function ids($ids)
    {
        $ids = is_array($ids) ? $ids : [$ids];

        $query = new IdsQuery($ids);

        $this->append($query);

        return $this;
    }

    /**
     * Add an term query
     *
     * @param string $field
     * @param string $term
     * @param array $attributes
     * @return $this
     */
    public function term($field, $term, array $attributes)
    {
        $query = new TermQuery($field, $term, $attributes);

        $this->append($query);

        return $this;
    }

    /**
     * Add an terms query
     *
     * @param string $field
     * @param array $terms
     * @param array $attributes
     * @return $this
     */
    public function terms($field, array $terms, array $attributes)
    {
        $query = new TermsQuery($field, $terms, $attributes);

        $this->append($query);

        return $this;
    }

    /**
     * Add a wildcard query
     *
     * @param string $field
     * @param string $value
     * @param float $boost
     * @return $this
     */
    public function wildcard($field, $value, $boost = 1.0)
    {
        $query = new WildcardQuery($field, $value, ['boost' => $boost]);

        $this->append($query);

        return $this;
    }

    /**
     * Add a boost query
     *
     * @param float|null $boost
     * @return $this
     * @internal param $field
     */
    public function matchAll($boost = 1.0)
    {
        $query = new MatchAllQuery(['boost' => $boost]);

        $this->append($query);

        return $this;
    }

    /**
     * Add a match query
     *
     * @param string $field
     * @param string $term
     * @param array $attributes
     * @return $this
     */
    public function match($field, $term, array $attributes = [])
    {
        $query = new MatchQuery($field, $term, $attributes);

        $this->append($query);

        return $this;
    }

    /**
     * Add a multy match query
     *
     * @param array $fields
     * @param string $term
     * @param array $attributes
     * @return $this
     */
    public function multyMatch(array $fields, $term, array $attributes = [])
    {
        $query = new MultiMatchQuery($fields, $term, $attributes);

        $this->append($query);

        return $this;
    }

    /**
     * Add a prefix query
     *
     * @param string $field
     * @param string $term
     * @param array $attributes
     * @return $this
     */
    public function prefix($field, $term, array $attributes = [])
    {
        $query = new PrefixQuery($field, $term, $attributes);

        $this->append($query);

        return $this;
    }

    /**
     * Add a query string query
     *
     * @param string $query
     * @param array $attributes
     * @return $this
     */
    public function queryString($query, array $attributes = [])
    {
        $query = new QueryStringQuery($query, $attributes);

        $this->append($query);

        return $this;
    }


    /**
     * Add a simple query string query
     *
     * @param string $query
     * @param array $attributes
     * @return $this
     */
    public function simpleQueryString($query, array $attributes = [])
    {
        $query = new SimpleQueryStringQuery($query, $attributes);

        $this->append($query);

        return $this;
    }

    /**
     * Add a range query
     *
     * @param string $field
     * @param array $attributes
     * @return $this
     */
    public function range($field, array $attributes)
    {
        $query = new RangeQuery($field, $attributes);

        $this->append($query);

        return $this;
    }


    /**
     * Add a regexp query
     *
     * @param string $field
     * @param array $attributes
     * @return $this
     */
    public function regexp($field, $regex, array $attributes)
    {
        $query = new RegexpQuery($field, $regex, $attributes);

        $this->append($query);

        return $this;
    }

    /**
     * Add a common term query
     *
     * @param $field
     * @param $term
     * @param array $attributes
     * @return $this
     */
    public function commonTerm($field, $term, array $attributes)
    {
        $query = new CommonTermsQuery($field, $term, $attributes);

        $this->append($query);

        return $this;
    }

    /*
     *
     * @todo add Boosting query
     *
     * @todo add ConstantScore query
     *
     * @todo add DisMaxQuery
     *
     * @todo add FunctionScoreQuery
     *
     * @todo should you implement has_parent has_child
     *
     * @todo should you implement the indices query
     *
     * @todo think of more like this implementation
     *
     * @todo dig into events
     *
     * @todo create an aggregation builder $query->aggregate(AggregateBuilder $builder){  }
     *
     * @todo add suggest query
     *
     * @todo add fields method to select the field to be selected
     *
     * @todo add limit
     *
     * @todo add order
     *
     * @todo add pagination
     *
     * @todo add event sync create / update / delete ($searchable = ['id','title','body','tags'])
     *
     * @todo add model mapping
     *
     * @todo tests
     *
     */

    /**
     * Add a fuzzy query
     *
     * @param $field
     * @param $term
     * @param array $attributes
     * @return $this
     */
    public function fuzzy($field, $term, array $attributes)
    {
        $query = new FuzzyQuery($field, $term, $attributes);

        $this->append($query);

        return $this;
    }


    /**
     * Add a nested query.
     *
     * @param $field
     * @param \Closure $closure
     * @param string $score_mode
     * @return $this
     */
    public function nested($field, \Closure $closure, $score_mode = 'avg')
    {
        $builder = new Builder($this->connection, new $this->grammar);

        $closure($builder);

        $nestedQuery = $builder->query->getQueries();

        $query = new NestedQuery($field, $nestedQuery, ['score_mode' => $score_mode]);

        $this->append($query);

        return $this;
    }


    /**
     * Add aggregation
     *
     * @param \Closure $closure
     * @return $this
     */
    public function aggregate(\Closure $closure)
    {
        $builder = new AggregationBuilder($this->query);

        $closure($builder);

        return $this;
    }

    /**
     * Add suggestions
     *
     * @param \Closure $closure
     * @return $this
     */
    public function suggest(\Closure $closure)
    {
        $builder = new SuggestionBuilder($this->query);

        $closure($builder);

        return $this;
    }

    /**
     * Return the DSL query
     *
     * @return array
     */
    public function toDSL()
    {
        return $this->query->toArray();
    }

    /**
     * Append a query
     *
     * @param $query
     */
    public function append($query)
    {
        if ($this->filtering) {
            $this->query->addFilter($query, $this->boolState);
        } else {
            $this->query->addQuery($query, $this->boolState);
        }
    }
}