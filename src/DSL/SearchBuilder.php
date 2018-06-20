<?php

namespace Sleimanx2\Plastic\DSL;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Traits\Macroable;
use ONGR\ElasticsearchDSL\Highlight\Highlight;
use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Query\FullText\CommonTermsQuery;
use ONGR\ElasticsearchDSL\Query\FullText\MatchQuery;
use ONGR\ElasticsearchDSL\Query\FullText\MultiMatchQuery;
use ONGR\ElasticsearchDSL\Query\FullText\QueryStringQuery;
use ONGR\ElasticsearchDSL\Query\FullText\SimpleQueryStringQuery;
use ONGR\ElasticsearchDSL\Query\Geo\GeoBoundingBoxQuery;
use ONGR\ElasticsearchDSL\Query\Geo\GeoDistanceQuery;
use ONGR\ElasticsearchDSL\Query\Geo\GeoDistanceRangeQuery;
use ONGR\ElasticsearchDSL\Query\Geo\GeoPolygonQuery;
use ONGR\ElasticsearchDSL\Query\Geo\GeoShapeQuery;
use ONGR\ElasticsearchDSL\Query\Joining\NestedQuery;
use ONGR\ElasticsearchDSL\Query\MatchAllQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\ExistsQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\FuzzyQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\IdsQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\PrefixQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\RangeQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\RegexpQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermsQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\WildcardQuery;
use ONGR\ElasticsearchDSL\Search as Query;
use ONGR\ElasticsearchDSL\Sort\FieldSort;
use Sleimanx2\Plastic\Connection;
use Sleimanx2\Plastic\Exception\InvalidArgumentException;
use Sleimanx2\Plastic\Fillers\EloquentFiller;
use Sleimanx2\Plastic\Fillers\FillerInterface;
use Sleimanx2\Plastic\PlasticPaginator;
use Sleimanx2\Plastic\PlasticResult;
use Sleimanx2\Plastic\Searchable;

class SearchBuilder
{
    use Macroable;

    /**
     * An instance of DSL query.
     *
     * @var Query
     */
    public $query;

    /**
     * The elastic type to query against.
     *
     * @var string
     */
    public $type;

    /**
     * The elastic index to query against.
     *
     * @var string
     */
    public $index;

    /**
     * The model to use when querying elastic search.
     *
     * @var Model
     */
    protected $model;

    /**
     * The model filler to use after retrieving the results.
     *
     * @var FillerInterface
     */
    protected $modelFiller;

    /**
     * An instance of plastic Connection.
     *
     * @var Connection
     */
    protected $connection;

    /**
     * Query bool state.
     *
     * @var string
     */
    protected $boolState = BoolQuery::MUST;

    /**
     * Builder constructor.
     *
     * @param Connection $connection
     * @param Query      $grammar
     */
    public function __construct(Connection $connection, Query $grammar = null)
    {
        $this->connection = $connection;
        $this->query = $grammar ?: $connection->getDSLGrammar();
    }

    /**
     * Set the elastic type to query against.
     *
     * @param string $type
     *
     * @return $this
     */
    public function type($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Set the elastic index to query against.
     *
     * @param string $index
     *
     * @return $this
     */
    public function index($index)
    {
        $this->index = $index;

        return $this;
    }

    /**
     * Set the eloquent model to use when querying elastic search.
     *
     * @param Model|Searchable $model
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     */
    public function model(Model $model)
    {
        // Check if the model is searchable before setting the query builder model
        $traits = class_uses_recursive(get_class($model));

        if (!isset($traits[Searchable::class])) {
            throw new InvalidArgumentException(get_class($model).' does not use the searchable trait');
        }

        $this->type($model->getDocumentType());

        if ($index = $model->getDocumentIndex()) {
            $this->index($index);
        }

        $this->model = $model;

        return $this;
    }

    /**
     * Set the query from/offset value.
     *
     * @param int $offset
     *
     * @return $this
     */
    public function from($offset)
    {
        $this->query->setFrom($offset);

        return $this;
    }

    /**
     * Set the query limit/size value.
     *
     * @param int $limit
     *
     * @return $this
     */
    public function size($limit)
    {
        $this->query->setSize($limit);

        return $this;
    }

    /**
     * Set the query sort values values.
     *
     * @param string|array $fields
     * @param null         $order
     * @param array        $parameters
     *
     * @return $this
     */
    public function sortBy($fields, $order = null, array $parameters = [])
    {
        $fields = is_array($fields) ? $fields : [$fields];

        foreach ($fields as $field) {
            $sort = new FieldSort($field, $order, $parameters);

            $this->query->addSort($sort);
        }

        return $this;
    }

    /**
     * Set the query min score value.
     *
     * @param $score
     *
     * @return $this
     */
    public function minScore($score)
    {
        $this->query->setMinScore($score);

        return $this;
    }

    /**
     * Switch to a should statement.
     */
    public function should()
    {
        $this->boolState = BoolQuery::SHOULD;

        return $this;
    }

    /**
     * Switch to a must statement.
     */
    public function must()
    {
        $this->boolState = BoolQuery::MUST;

        return $this;
    }

    /**
     * Switch to a must not statement.
     */
    public function mustNot()
    {
        $this->boolState = BoolQuery::MUST_NOT;

        return $this;
    }

    /**
     * Switch to a filter query.
     */
    public function filter()
    {
        $this->boolState = BoolQuery::FILTER;

        return $this;
    }

    /**
     * Add an ids query.
     *
     * @param array | string $ids
     *
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
     * Add an term query.
     *
     * @param string $field
     * @param string $term
     * @param array  $attributes
     *
     * @return $this
     */
    public function term($field, $term, array $attributes = [])
    {
        $query = new TermQuery($field, $term, $attributes);

        $this->append($query);

        return $this;
    }

    /**
     * Add an terms query.
     *
     * @param string $field
     * @param array  $terms
     * @param array  $attributes
     *
     * @return $this
     */
    public function terms($field, array $terms, array $attributes = [])
    {
        $query = new TermsQuery($field, $terms, $attributes);

        $this->append($query);

        return $this;
    }

    /**
     * Add an exists query.
     *
     * @param string|array $fields
     *
     * @return $this
     */
    public function exists($fields)
    {
        $fields = is_array($fields) ? $fields : [$fields];

        foreach ($fields as $field) {
            $query = new ExistsQuery($field);

            $this->append($query);
        }

        return $this;
    }

    /**
     * Add a wildcard query.
     *
     * @param string $field
     * @param string $value
     * @param float  $boost
     *
     * @return $this
     */
    public function wildcard($field, $value, $boost = 1.0)
    {
        $query = new WildcardQuery($field, $value, ['boost' => $boost]);

        $this->append($query);

        return $this;
    }

    /**
     * Add a boost query.
     *
     * @param float|null $boost
     *
     * @return $this
     *
     * @internal param $field
     */
    public function matchAll($boost = 1.0)
    {
        $query = new MatchAllQuery(['boost' => $boost]);

        $this->append($query);

        return $this;
    }

    /**
     * Add a match query.
     *
     * @param string $field
     * @param string $term
     * @param array  $attributes
     *
     * @return $this
     */
    public function match($field, $term, array $attributes = [])
    {
        $query = new MatchQuery($field, $term, $attributes);

        $this->append($query);

        return $this;
    }

    /**
     * Add a multi match query.
     *
     * @param array  $fields
     * @param string $term
     * @param array  $attributes
     *
     * @return $this
     */
    public function multiMatch(array $fields, $term, array $attributes = [])
    {
        $query = new MultiMatchQuery($fields, $term, $attributes);

        $this->append($query);

        return $this;
    }

    /**
     * Add a geo bounding box query.
     *
     * @param string $field
     * @param array  $values
     * @param array  $parameters
     *
     * @return $this
     */
    public function geoBoundingBox($field, $values, array $parameters = [])
    {
        $query = new GeoBoundingBoxQuery($field, $values, $parameters);

        $this->append($query);

        return $this;
    }

    /**
     * Add a geo distance query.
     *
     * @param string $field
     * @param string $distance
     * @param mixed  $location
     * @param array  $attributes
     *
     * @return $this
     */
    public function geoDistance($field, $distance, $location, array $attributes = [])
    {
        $query = new GeoDistanceQuery($field, $distance, $location, $attributes);

        $this->append($query);

        return $this;
    }

    /**
     * Add a geo distance range query.
     *
     * @param string $field
     * @param $from
     * @param $to
     * @param mixed $location
     * @param array $attributes
     *
     * @return $this
     */
    public function geoDistanceRange($field, $from, $to, array $location, array $attributes = [])
    {
        $range = compact('from', 'to');

        $query = new GeoDistanceRangeQuery($field, $range, $location, $attributes);

        $this->append($query);

        return $this;
    }

    /**
     * Add a geo polygon query.
     *
     * @param string $field
     * @param array  $points
     * @param array  $attributes
     *
     * @return $this
     */
    public function geoPolygon($field, array $points = [], array $attributes = [])
    {
        $query = new GeoPolygonQuery($field, $points, $attributes);

        $this->append($query);

        return $this;
    }

    /**
     * Add a geo shape query.
     *
     * @param string $field
     * @param $type
     * @param array $coordinates
     * @param array $attributes
     *
     * @return $this
     */
    public function geoShape($field, $type, array $coordinates = [], array $attributes = [])
    {
        $query = new GeoShapeQuery();

        $query->addShape($field, $type, $coordinates, $attributes);

        $this->append($query);

        return $this;
    }

    /**
     * Add a prefix query.
     *
     * @param string $field
     * @param string $term
     * @param array  $attributes
     *
     * @return $this
     */
    public function prefix($field, $term, array $attributes = [])
    {
        $query = new PrefixQuery($field, $term, $attributes);

        $this->append($query);

        return $this;
    }

    /**
     * Add a query string query.
     *
     * @param string $query
     * @param array  $attributes
     *
     * @return $this
     */
    public function queryString($query, array $attributes = [])
    {
        $query = new QueryStringQuery($query, $attributes);

        $this->append($query);

        return $this;
    }

    /**
     * Add a simple query string query.
     *
     * @param string $query
     * @param array  $attributes
     *
     * @return $this
     */
    public function simpleQueryString($query, array $attributes = [])
    {
        $query = new SimpleQueryStringQuery($query, $attributes);

        $this->append($query);

        return $this;
    }

    /**
     * Add a highlight to result.
     *
     * @param array  $fields
     * @param array  $parameters
     * @param string $preTag
     * @param string $postTag
     *
     * @see https://www.elastic.co/guide/en/elasticsearch/reference/current/search-request-highlighting.html
     *
     * @return $this
     */
    public function highlight($fields = ['_all' => []], $parameters = [], $preTag = '<mark>', $postTag = '</mark>')
    {
        $highlight = new Highlight();
        $highlight->setTags([$preTag], [$postTag]);

        foreach ($fields as $field => $fieldParams) {
            $highlight->addField($field, $fieldParams);
        }

        if ($parameters) {
            $highlight->setParameters($parameters);
        }

        $this->query->addHighlight($highlight);

        return $this;
    }

    /**
     * Add a range query.
     *
     * @param string $field
     * @param array  $attributes
     *
     * @return $this
     */
    public function range($field, array $attributes = [])
    {
        $query = new RangeQuery($field, $attributes);

        $this->append($query);

        return $this;
    }

    /**
     * Add a regexp query.
     *
     * @param string $field
     * @param array  $attributes
     *
     * @return $this
     */
    public function regexp($field, $regex, array $attributes = [])
    {
        $query = new RegexpQuery($field, $regex, $attributes);

        $this->append($query);

        return $this;
    }

    /**
     * Add a common term query.
     *
     * @param $field
     * @param $term
     * @param array $attributes
     *
     * @return $this
     */
    public function commonTerm($field, $term, array $attributes = [])
    {
        $query = new CommonTermsQuery($field, $term, $attributes);

        $this->append($query);

        return $this;
    }

    /**
     * Add a fuzzy query.
     *
     * @param $field
     * @param $term
     * @param array $attributes
     *
     * @return $this
     */
    public function fuzzy($field, $term, array $attributes = [])
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
     * @param string   $score_mode
     *
     * @return $this
     */
    public function nested($field, \Closure $closure, $score_mode = 'avg')
    {
        $builder = new self($this->connection, new $this->query());

        $closure($builder);

        $nestedQuery = $builder->query->getQueries();

        $query = new NestedQuery($field, $nestedQuery, ['score_mode' => $score_mode]);

        $this->append($query);

        return $this;
    }

    /**
     * Add aggregation.
     *
     * @param \Closure $closure
     *
     * @return $this
     */
    public function aggregate(\Closure $closure)
    {
        $builder = new AggregationBuilder($this->query);

        $closure($builder);

        return $this;
    }

    /**
     * Add function score.
     *
     * @param \Closure $search
     * @param \Closure $closure
     * @param array    $parameters
     *
     * @return $this
     */
    public function functions(\Closure $search, \Closure $closure, $parameters = [])
    {
        $builder = new self($this->connection, new $this->query());
        $search($builder);

        $builder = new FunctionScoreBuilder($builder, $parameters);

        $closure($builder);

        $this->append($builder->getQuery());

        return $this;
    }

    /**
     * Set the model filler to use after retrieving the results.
     *
     * @param FillerInterface $filler
     */
    public function setModelFiller(FillerInterface $filler)
    {
        $this->modelFiller = $filler;
    }

    /**
     * get the model filler to use after retrieving the results.
     *
     * @return FillerInterface
     */
    public function getModelFiller()
    {
        return $this->modelFiller ? $this->modelFiller : new EloquentFiller();
    }

    /**
     * Execute the search query against elastic and return the raw result.
     *
     * @return array
     */
    public function getRaw()
    {
        $params = [
            'index' => $this->getIndex(),
            'type'  => $this->getType(),
            'body'  => $this->toDSL(),
        ];

        return $this->connection->searchStatement($params);
    }

    /**
     * Execute the search query against elastic and return the raw result if the model is not set.
     *
     * @return PlasticResult
     */
    public function get()
    {
        $result = $this->getRaw();

        $result = new PlasticResult($result);

        if ($this->model) {
            $this->getModelFiller()->fill($this->model, $result);
        }

        return $result;
    }

    /**
     * Return the current elastic type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Return the current elastic index.
     *
     * @return string
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Return the current plastic connection.
     *
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Return the boolean query state.
     *
     * @return string
     */
    public function getBoolState()
    {
        return $this->boolState;
    }

    /**
     * Paginate result hits.
     *
     * @param int      $limit
     * @param null|int $current
     *
     * @return PlasticPaginator
     */
    public function paginate($limit = 25, $current = null)
    {
        $page = $this->getCurrentPage($current);

        $from = $limit * ($page - 1);
        $size = $limit;

        $result = $this->from($from)->size($size)->get();

        return new PlasticPaginator($result, $size, $page);
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
     * Append a query.
     *
     * @param $query
     *
     * @return $this
     */
    public function append($query)
    {
        $this->query->addQuery($query, $this->getBoolState());

        return $this;
    }

    /**
     * return the current query string value.
     *
     * @param null|int $current
     *
     * @return int
     */
    protected function getCurrentPage($current)
    {
        return $current ?: (int) \Request::get('page', 1);
    }
}
